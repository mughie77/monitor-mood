<?php
require_once __DIR__ . '/functions.php';

start_session();

// Only logged-in students can submit
if (!is_logged_in() || get_user_role() !== 'student') {
    // Set feedback message and redirect
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Akses tidak sah.'];
    redirect('../bullying_form');
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = sanitize_input($_POST['report_description']);
    $student_user_id = $_SESSION['user_id'];

    if (empty($description)) {
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Deskripsi laporan tidak boleh kosong.'];
        redirect('../bullying_form');
    }

    // Prepare and execute the insert statement
    $stmt = $mysqli->prepare("INSERT INTO bullying_reports (student_user_id, report_description) VALUES (?, ?)");
    $stmt->bind_param("is", $student_user_id, $description);

    if ($stmt->execute()) {
        $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Laporan Anda telah berhasil dikirim. Terima kasih.'];
    } else {
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Gagal mengirim laporan. Silakan coba lagi.'];
    }

    $stmt->close();
    $mysqli->close();

    redirect('../bullying_form');

} else {
    // Redirect if accessed directly
    redirect('../dashboard');
}
?>
