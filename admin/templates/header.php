<?php
// The path to the core functions file is relative to the file including this header.
// So, the path needs to be adjusted based on the location of the calling file.
// A better approach is to define a ROOT_PATH constant in a central config file.
// For now, we'll assume the including file is in the `admin` directory.
require_once __DIR__ . '/../../app/functions.php';

// Protect the page for admin users only
protect_page(['admin']);

// Get user's name for the welcome message
$full_name = $_SESSION['username']; // For admins, username is fine for now.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin' : 'Admin Dashboard'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo base_url('admin/assets/css/admin_style.css'); ?>">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <div class="sidebar-heading text-white">Mood Tracker Admin</div>
        <div class="list-group list-group-flush">
            <a href="<?php echo base_url('admin/index.php'); ?>" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="<?php echo base_url('admin/manage_students.php'); ?>" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-user-graduate me-2"></i>Manage Students
            </a>
            <a href="<?php echo base_url('admin/manage_teachers.php'); ?>" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-chalkboard-teacher me-2"></i>Manage Teachers
            </a>
            <a href="<?php echo base_url('admin/manage_admins.php'); ?>" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-user-shield me-2"></i>Manage Admins
            </a>
            <a href="<?php echo base_url('logout.php'); ?>" class="list-group-item list-group-item-action bg-dark text-white mt-auto">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
                <div class="ms-auto">Welcome, <?php echo htmlspecialchars($full_name); ?>!</div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <!-- Main content starts here -->
