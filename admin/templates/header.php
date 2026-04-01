<?php
require_once __DIR__ . '/../../app/functions.php';

// Protect the page for admin users only
protect_page(['admin']);

// Get user's name for the welcome message
$full_name = $_SESSION['username'];

// Get current page for active menu link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin SI-SONYA' : 'Admin SI-SONYA'; ?></title>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<div class="flex flex-col md:flex-row min-h-screen" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper" class="hidden md:block bg-fun-blue text-white w-full md:w-64 flex-shrink-0 transition-all duration-300">
        <div class="p-6 text-2xl font-bold border-b border-white/10">Admin SI-SONYA</div>
        <div class="flex flex-col py-4">
            <a href="<?php echo base_url('admin/index'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'index.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-tachometer-alt mr-3 w-5"></i>Dasbor
            </a>
            <a href="<?php echo base_url('admin/report_students'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'report_students.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-file-alt mr-3 w-5"></i>Laporan Siswa
            </a>
            <a href="<?php echo base_url('admin/report_teachers'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'report_teachers.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-file-alt mr-3 w-5"></i>Laporan Guru
            </a>
            <a href="<?php echo base_url('admin/bullying_reports'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'bullying_reports.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-bullhorn mr-3 w-5"></i>Laporan Perundungan
            </a>
            <a href="<?php echo base_url('admin/view_feedback'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'view_feedback.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-lightbulb mr-3 w-5"></i>Kritik dan Saran
            </a>
            <a href="<?php echo base_url('admin/manage_students'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'manage_students.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-user-graduate mr-3 w-5"></i>Kelola Siswa
            </a>
            <a href="<?php echo base_url('admin/manage_teachers'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'manage_teachers.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-chalkboard-teacher mr-3 w-5"></i>Kelola Guru
            </a>
            <a href="<?php echo base_url('admin/manage_admins'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'manage_admins.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-user-shield mr-3 w-5"></i>Kelola Admin
            </a>
            <a href="<?php echo base_url('admin/manage_contacts'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition <?php echo ($current_page == 'manage_contacts.php') ? 'bg-white/20' : ''; ?>">
                <i class="fas fa-address-book mr-3 w-5"></i>Kontak Darurat
            </a>
            <a href="<?php echo base_url('logout'); ?>" class="px-6 py-3 flex items-center hover:bg-white/10 transition mt-auto">
                <i class="fas fa-sign-out-alt mr-3 w-5"></i>Keluar
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper" class="flex-grow flex flex-col min-h-screen overflow-x-hidden">
        <nav class="bg-white shadow-sm border-b px-6 py-4 flex items-center justify-between">
            <button class="text-fun-blue md:hidden" id="menu-toggle"><i class="fas fa-bars text-xl"></i></button>
            <div class="hidden md:block"></div>
            <div class="text-sm font-medium">Selamat Datang, <span class="text-fun-blue"><?php echo htmlspecialchars($full_name); ?></span>!</div>
        </nav>

        <div class="p-6 md:p-8">
            <!-- Main content starts here -->
