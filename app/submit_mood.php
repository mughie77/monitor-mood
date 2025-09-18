<?php
header('Content-Type: application/json');
require_once __DIR__ . '/functions.php';

start_session();

// Ensure the user is logged in as a teacher or student
if (!is_logged_in() || !in_array(get_user_role(), ['teacher', 'student'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$mood_value = isset($_POST['mood_value']) ? (int)$_POST['mood_value'] : 0;

// Validate the mood value
if ($mood_value < 1 || $mood_value > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid mood value provided.']);
    exit();
}

$record_date = date('Y-m-d');

// Check if a mood has already been recorded for today to prevent duplicates
$stmt_check = $mysqli->prepare("SELECT id FROM mood_records WHERE user_id = ? AND record_date = ?");
$stmt_check->bind_param("is", $user_id, $record_date);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already submitted your mood for today.']);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// Insert the new mood record
$stmt_insert = $mysqli->prepare("INSERT INTO mood_records (user_id, mood_value, record_date) VALUES (?, ?, ?)");
$stmt_insert->bind_param("iis", $user_id, $mood_value, $record_date);

if ($stmt_insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your mood has been recorded successfully!']);
} else {
    // Potentially log the real error: $stmt_insert->error
    echo json_encode(['success' => false, 'message' => 'Failed to record your mood. Please try again.']);
}

$stmt_insert->close();
$mysqli->close();
?>
