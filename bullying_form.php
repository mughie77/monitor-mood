<?php
$page_title = 'Lapor Perundungan';
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
        <h2>Formulir Laporan Perundungan</h2>
        <a href="dashboard" class="btn btn-outline-secondary">Kembali ke Dasbor</a>
    </div>

    <?php if (!empty($feedback_message)): ?>
        <div class="alert alert-<?php echo $feedback_type; ?>" role="alert">
            <?php echo $feedback_message; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <p class="mb-0">Silakan jelaskan kejadian yang ingin Anda laporkan di bawah ini. Laporan Anda bersifat rahasia.</p>
        </div>
        <div class="card-body">
            <form action="app/submit_bullying_report" method="POST">
                <div class="mb-3">
                    <label for="report_description" class="form-label"><strong>Deskripsi Kejadian</strong></label>
                    <textarea class="form-control" id="report_description" name="report_description" rows="8" required placeholder="Jelaskan apa yang terjadi, siapa yang terlibat, di mana kejadiannya, dan kapan waktunya..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane me-2"></i>Kirim Laporan</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
