<?php
require_once 'app/functions.php';

start_session();

// If user is already logged in, redirect to their dashboard
if (is_logged_in()) {
    $role = get_user_role();
    if ($role === 'admin') {
        redirect('admin/index');
    } else {
        redirect('dashboard');
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
                    redirect('admin/index');
                } else {
                    redirect('dashboard');
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
    <title>Login - SI-SONYA</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'fun-pink': '#FF6B6B',
                        'fun-yellow': '#FFD93D',
                        'fun-green': '#6BCB77',
                        'fun-orange': '#FF9248',
                        'fun-blue': '#4D96FF',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-fun-pink via-fun-orange to-fun-yellow min-h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-8 md:p-10">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-gradient-to-tr from-fun-blue to-fun-pink rounded-2xl flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-6 transform -rotate-6">
                SS
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-2">Login SI-SONYA</h2>
            <p class="text-gray-500 text-center mb-8 text-sm md:text-base">Sistem Informasi Sekolah Aman dan Nyaman</p>

            <?php if (!empty($error_message)): ?>
                <div class="w-full bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <form action="login" method="POST" class="w-full space-y-5">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengguna atau Email</label>
                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="username" name="username" required placeholder="Masukkan username">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                    <input type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-fun-blue hover:bg-opacity-90 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1 active:scale-95 mt-4">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
