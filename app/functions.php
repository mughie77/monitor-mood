<?php
// Include the database connection file
require_once __DIR__ . '/db.php';

/**
 * Starts a session safely.
 */
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Redirects the user to a specified URL.
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Checks if a user is logged in.
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    start_session();
    return isset($_SESSION['user_id']);
}

/**
 * Gets the role of the logged-in user.
 * @return string|null The user's role or null if not logged in.
 */
function get_user_role() {
    start_session();
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Restricts access to a page based on user role.
 * If no roles are provided, it just checks if the user is logged in.
 * @param array $roles An array of roles that are allowed access.
 */
function protect_page($roles = []) {
    start_session();
    if (!is_logged_in()) {
        redirect('login.php');
    }

    $user_role = get_user_role();
    if (!empty($roles) && !in_array($user_role, $roles)) {
        // Redirect to a generic dashboard or an error page
        redirect('dashboard.php?error=unauthorized');
    }
}

/**
 * Sanitizes user input to prevent XSS.
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * A simple function to get the base URL of the site.
 * @return string The site's base URL.
 */
function base_url($path = '') {
    // This function assumes the config file is loaded.
    // A more robust solution might be needed depending on server setup.
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Converts a numeric mood value to its Indonesian text description.
 * @param int $mood_value The numeric mood value (1-5).
 * @return string The text description of the mood.
 */
function get_mood_description($mood_value) {
    // Round the value to handle averages
    $mood_value = round($mood_value);
    switch ($mood_value) {
        case 1:
            return 'Sedih';
        case 2:
            return 'Biasa';
        case 3:
            return 'Senang';
        case 4:
            return 'Bersemangat';
        case 5:
            return 'Luar Biasa';
        default:
            return 'Tidak Diketahui';
    }
}
?>
