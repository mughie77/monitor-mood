<?php
$page_title = 'Dasbor Saya';
require_once 'templates/header.php';

// Protect page for specific roles
protect_page(['teacher', 'student']);

// Fetch user's full name
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$table_name = $role . 's'; // 'students' or 'teachers'

$stmt = $mysqli->prepare("SELECT full_name FROM $table_name WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_profile = $result->fetch_assoc();
$full_name = $user_profile ? $user_profile['full_name'] : $_SESSION['username'];
$stmt->close();

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
        <h2>Selamat Datang, <?php echo htmlspecialchars($full_name); ?>!</h2>
        <a href="logout.php" class="btn btn-outline-danger">Keluar</a>
    </div>

    <div id="mood-response-message"></div>

    <div class="card text-center" id="mood-panel">
        <div class="card-header">
            <h4 class="mb-0">Bagaimana perasaanmu hari ini?</h4>
        </div>
        <div class="card-body">
            <?php if ($mood_today): ?>
                <div class="card-mood-submitted p-4">
                    <h5 class="text-success">Terima kasih telah berbagi suasana hati Anda hari ini!</h5>
                    <p class="mb-0">Anda dapat berbagi suasana hati lagi besok.</p>
                </div>
            <?php else: ?>
                <div id="mood-selector-ui" class="mood-selector">
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
            <?php endif; ?>
        </div>
    </div>

    <!-- Placeholder for past mood history -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Riwayat Suasana Hati Anda</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Entri suasana hati Anda sebelumnya akan ditampilkan di sini segera.</p>
        </div>
    </div>

    <!-- Bullying Report Section for Students -->
    <?php if ($role === 'student'): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5>Lapor Perundungan</h5>
        </div>
        <div class="card-body text-center">
            <p class="text-muted">Jika Anda melihat atau mengalami perundungan, jangan ragu untuk melaporkannya. Laporan Anda akan ditangani secara rahasia.</p>
            <a href="bullying_form.php" class="btn btn-danger"><i class="fas fa-bullhorn me-2"></i>Buat Laporan Baru</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php
$custom_js = 'assets/js/dashboard.js';
require_once 'templates/footer.php';
?>
