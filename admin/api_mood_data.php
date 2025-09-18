<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/functions.php';

// Only admins can access this API
protect_page(['admin']);

// --- Chart Data & Filter Logic ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';
$chart_query = "";
$summary_where_clause = "";

switch ($filter) {
    case 'monthly':
        $chart_query = "SELECT DATE_FORMAT(record_date, '%Y-%m') as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY report_period, u.role ORDER BY report_period, u.role";
        $summary_where_clause = "WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        break;
    case 'yearly':
        $chart_query = "SELECT YEAR(record_date) as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id GROUP BY report_period, u.role ORDER BY report_period, u.role";
        $summary_where_clause = ""; // No date filter for all-time yearly average
        break;
    case 'daily':
    default:
        $chart_query = "SELECT DATE(record_date) as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY report_period, u.role ORDER BY report_period, u.role";
        $summary_where_clause = "WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
}

$result = $mysqli->query($chart_query);
$data_by_period = [];
while ($row = $result->fetch_assoc()) {
    $data_by_period[$row['report_period']][$row['role']] = (float)$row['average_mood'];
}

$labels = array_keys($data_by_period);
$student_values = [];
$teacher_values = [];
foreach ($labels as $label) {
    $student_values[] = $data_by_period[$label]['student'] ?? 0;
    $teacher_values[] = $data_by_period[$label]['teacher'] ?? 0;
}

$chartData = [
    'labels' => $labels,
    'datasets' => [
        ['label' => 'Rata-rata Mood Siswa', 'data' => $student_values, 'borderColor' => 'rgba(54, 162, 235, 1)', 'backgroundColor' => 'rgba(54, 162, 235, 0.2)', 'fill' => true, 'tension' => 0.1],
        ['label' => 'Rata-rata Mood Guru', 'data' => $teacher_values, 'borderColor' => 'rgba(255, 99, 132, 1)', 'backgroundColor' => 'rgba(255, 99, 132, 0.2)', 'fill' => true, 'tension' => 0.1]
    ]
];

// --- Summary Statistics Logic (Now Dynamic) ---
$summary = [];
$summary['total_students'] = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$summary['total_teachers'] = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'")->fetch_assoc()['count'];

$period_mood_query = "SELECT u.role, AVG(mr.mood_value) as avg_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id $summary_where_clause GROUP BY u.role";
$period_mood_result = $mysqli->query($period_mood_query);

$period_moods = [];
if ($period_mood_result) {
    while ($row = $period_mood_result->fetch_assoc()) {
        $period_moods[$row['role']] = round($row['avg_mood'], 2);
    }
}
$summary['avg_student_mood_period'] = $period_moods['student'] ?? 'N/A';
$summary['avg_teacher_mood_period'] = $period_moods['teacher'] ?? 'N/A';


// --- Final Response ---
$response = [
    'chartData' => $chartData,
    'summary' => $summary,
    'range_start' => !empty($labels) ? reset($labels) : null,
    'range_end' => !empty($labels) ? end($labels) : null
];

echo json_encode($response);

$mysqli->close();
?>
