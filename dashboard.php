<?php
$page_title = 'My Dashboard';
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
        <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <div id="mood-response-message"></div>

    <div class="card text-center" id="mood-panel">
        <div class="card-header">
            <h4 class="mb-0">How are you feeling today?</h4>
        </div>
        <div class="card-body">
            <?php if ($mood_today): ?>
                <div class="card-mood-submitted p-4">
                    <h5 class="text-success">Thanks for sharing your mood today!</h5>
                    <p class="mb-0">You can share your mood again tomorrow.</p>
                </div>
            <?php else: ?>
                <div id="mood-selector-ui" class="mood-selector">
                    <div class="mood-option" data-mood-value="1">
                        <i class="fas fa-face-frown"></i>
                        <span>Sad</span>
                    </div>
                    <div class="mood-option" data-mood-value="2">
                        <i class="fas fa-face-meh"></i>
                        <span>Neutral</span>
                    </div>
                    <div class="mood-option" data-mood-value="3">
                        <i class="fas fa-face-smile"></i>
                        <span>Happy</span>
                    </div>
                    <div class="mood-option" data-mood-value="4">
                        <i class="fas fa-face-grin-beam"></i>
                        <span>Excited</span>
                    </div>
                    <div class="mood-option" data-mood-value="5">
                        <i class="fas fa-face-grin-stars"></i>
                        <span>Great</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Placeholder for past mood history -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Your Mood History</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Your past mood entries will be displayed here soon.</p>
        </div>
    </div>

</div>

<?php
$custom_js = 'assets/js/dashboard.js';
require_once 'templates/footer.php';
?>
