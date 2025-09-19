<?php
$page_title = 'Kritik dan Saran';
require_once 'templates/header.php';

// Only students can access this page
protect_page(['student']);

// Check for any feedback messages from the submission script
$feedback_message = '';
if (isset($_SESSION['feedback'])) {
    $feedback_message = $_SESSION['feedback']['message'];
    $feedback_type = $_SESSION['feedback']['type'];
    unset($_SESSION['feedback']); // Clear the message after displaying
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Formulir Kritik dan Saran</h2>
        <a href="dashboard" class="btn btn-outline-secondary">Kembali ke Dasbor</a>
    </div>

    <?php if (!empty($feedback_message)): ?>
        <div class="alert alert-<?php echo $feedback_type; ?>" role="alert">
            <?php echo $feedback_message; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <p class="mb-0">Kami menghargai masukan Anda untuk membuat sekolah menjadi tempat yang lebih baik.</p>
        </div>
        <div class="card-body">
            <form action="app/submit_feedback.php" method="POST">
                <div class="mb-3">
                    <label for="feedback_text" class="form-label"><strong>Kritik atau Saran Anda</strong></label>
                    <textarea class="form-control" id="feedback_text" name="feedback_text" rows="8" required placeholder="Tuliskan masukan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Kirim Masukan</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
