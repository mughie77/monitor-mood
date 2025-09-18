<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/functions.php';

// Only admins can access this API
protect_page(['admin']);

// --- Chart Data Logic ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';
$query = "";

switch ($filter) {
    case 'monthly':
        $query = "SELECT DATE_FORMAT(record_date, '%Y-%m') as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY report_period, u.role ORDER BY report_period, u.role";
        break;
    case 'yearly':
        $query = "SELECT YEAR(record_date) as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id GROUP BY report_period, u.role ORDER BY report_period, u.role";
        break;
    case 'daily':
    default:
        $query = "SELECT DATE(record_date) as report_period, u.role, AVG(mr.mood_value) as average_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id WHERE mr.record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY report_period, u.role ORDER BY report_period, u.role";
        break;
}

$result = $mysqli->query($query);
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

// --- Summary Statistics Logic ---
// This part only needs to be fetched once, not on every filter change, but for simplicity we include it here.
$summary = [];
$summary['total_students'] = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$summary['total_teachers'] = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'")->fetch_assoc()['count'];

$today_mood_query = "SELECT u.role, AVG(mr.mood_value) as avg_mood FROM mood_records mr JOIN users u ON mr.user_id = u.id WHERE mr.record_date = CURDATE() GROUP BY u.role";
$today_mood_result = $mysqli->query($today_mood_query);
$today_moods = [];
while ($row = $today_mood_result->fetch_assoc()) {
    $today_moods[$row['role']] = round($row['avg_mood'], 2);
}
$summary['avg_student_mood_today'] = $today_moods['student'] ?? 'N/A';
$summary['avg_teacher_mood_today'] = $today_moods['teacher'] ?? 'N/A';


// --- Final Response ---
$response = [
    'chartData' => $chartData,
    'summary' => $summary
];

echo json_encode($response);

$mysqli->close();
?>
