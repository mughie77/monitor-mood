<?php
require_once __DIR__ . '/../app/functions.php';
start_session();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - SI-SONYA' : 'SI-SONYA'; ?></title>
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
<body>

<div class="flex min-h-screen bg-gray-50">
    <!-- This is a basic structure that can be expanded. For non-admin pages, we can have a simple top nav or a less complex side nav -->
    <main class="flex-grow p-4 md:p-8">
        <!-- The main content will go here -->
