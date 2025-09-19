<?php
$page_title = 'Dasbor Saya';
require_once 'templates/header.php';

// Protect page for specific roles
protect_page(['teacher', 'student']);

// Fetch user's full name
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$table_name = $role . 's';

$user_profile = null;
if ($role === 'student' || $role === 'teacher') {
    $stmt = $mysqli->prepare("SELECT full_name FROM $table_name WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_profile = $result->fetch_assoc();
    $stmt->close();
}
$full_name = $user_profile ? $user_profile['full_name'] : $_SESSION['username'];

// Check if mood for today has already been submitted
$today = date("Y-m-d");
$stmt = $mysqli->prepare("SELECT mood_value FROM mood_records WHERE user_id = ? AND record_date = ?");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$mood_today = $result->fetch_assoc();
$stmt->close();

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Selamat Datang, <?php echo htmlspecialchars($full_name); ?>!</h2>
            <p class="text-muted">Semoga harimu menyenangkan.</p>
        </div>
        <a href="logout.php" class="btn btn-outline-danger">Keluar</a>
    </div>

    <div id="mood-response-message"></div>

    <!-- Main Mood Card -->
    <div class="card text-center shadow-lg" id="mood-panel">
        <div class="card-body p-5">
            <?php if ($mood_today): ?>
                <div class="mood-submitted-view">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-success">Terima kasih!</h4>
                    <p class="mb-0">Anda sudah mencatat suasana hati hari ini. Kembali lagi besok!</p>
                </div>
            <?php else: ?>
                <div id="mood-selector-ui">
                    <h4 class="mb-4">Bagaimana perasaanmu hari ini?</h4>
                    <div class="mood-selector">
                        <div class="mood-option" data-mood-value="1">
                            <i class="fas fa-face-frown"></i>
                            <span>Sedih</span>
                        </div>
                        <div class="mood-option" data-mood-value="2">
                            <i class="fas fa-face-meh"></i>
                            <span>Biasa</span>
                        </div>
                        <div class="mood-option" data-mood-value="3">
                            <i class="fas fa-face-smile"></i>
                            <span>Senang</span>
                        </div>
                        <div class="mood-option" data-mood-value="4">
                            <i class="fas fa-face-grin-beam"></i>
                            <span>Bersemangat</span>
                        </div>
                        <div class="mood-option" data-mood-value="5">
                            <i class="fas fa-face-grin-stars"></i>
                            <span>Luar Biasa</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Other Actions for Students -->
    <?php if ($role === 'student'): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body text-center">
                    <i class="fas fa-bullhorn fa-3x mb-3 text-warning"></i>
                    <h5>Lapor Perundungan</h5>
                    <p class="text-muted small">Laporkan perundungan secara rahasia untuk menjaga lingkungan sekolah tetap aman.</p>
                    <a href="bullying_form.php" class="btn btn-warning stretched-link">Buat Laporan</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card action-card">
                <div class="card-body text-center">
                    <i class="fas fa-lightbulb fa-3x mb-3 text-info"></i>
                    <h5>Kritik dan Saran</h5>
                    <p class="text-muted small">Punya ide atau masukan untuk membuat sekolah lebih baik? Sampaikan di sini.</p>
                    <a href="feedback_form.php" class="btn btn-info stretched-link">Beri Masukan</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer.php';
?>
