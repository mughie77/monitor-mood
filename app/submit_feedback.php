<?php
require_once __DIR__ . '/functions.php';

start_session();

// Only logged-in students can submit
if (!is_logged_in() || get_user_role() !== 'student') {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Akses tidak sah.'];
    redirect('../feedback_form');
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_text = sanitize_input($_POST['feedback_text']);
    $student_user_id = $_SESSION['user_id'];

    if (empty($feedback_text)) {
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Kritik dan saran tidak boleh kosong.'];
        redirect('../feedback_form');
    }

    // Prepare and execute the insert statement
    $stmt = $mysqli->prepare("INSERT INTO feedback (student_user_id, feedback_text) VALUES (?, ?)");
    $stmt->bind_param("is", $student_user_id, $feedback_text);

    if ($stmt->execute()) {
        $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Terima kasih! Masukan Anda telah berhasil dikirim.'];
    } else {
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Gagal mengirim masukan. Silakan coba lagi.'];
    }

    $stmt->close();
    $mysqli->close();

    redirect('../feedback_form');

} else {
    // Redirect if accessed directly
    redirect('../dashboard');
}
?>
