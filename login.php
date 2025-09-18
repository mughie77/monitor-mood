<?php
require_once 'app/functions.php';

start_session();

// If user is already logged in, redirect to their dashboard
if (is_logged_in()) {
    $role = get_user_role();
    if ($role === 'admin') {
        redirect('admin/index.php');
    } else {
        redirect('dashboard.php');
    }
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Don't sanitize password before verification

    if (empty($username) || empty($password)) {
        $error_message = 'Silakan masukkan nama pengguna dan kata sandi.';
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = $mysqli->prepare("SELECT id, password_hash, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect('admin/index.php');
                } else {
                    redirect('dashboard.php');
                }
            } else {
                $error_message = 'Nama pengguna atau kata sandi tidak valid.';
            }
        } else {
            $error_message = 'Nama pengguna atau kata sandi tidak valid.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pelacak Suasana Hati</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ece9e6, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .login-card .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        .login-card .btn-primary {
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-body">
            <h2 class="text-center mb-4">Selamat Datang Kembali!</h2>
            <p class="text-center text-muted mb-4">Masuk untuk mencatat suasana hati Anda.</p>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nama Pengguna atau Email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
