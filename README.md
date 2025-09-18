# Mood Tracker Web Application

A full-featured web application built with native PHP and MySQL that allows students and teachers to log their daily mood. The application features a multi-level management system for admins, teachers, and students, complete with a modern, luxurious user interface.

## Features

- **Role-Based Access Control:** A secure login system that directs users (Admin, Teacher, Student) to their respective dashboards.
- **Daily Mood Logging:** An elegant interface for students and teachers to select and record their mood once per day.
- **Admin Dashboard:** A comprehensive dashboard for administrators with a modern side-navigation menu.
- **User Management (CRUD):** Admins can Create, Read, Update, and Delete student, teacher, and other admin accounts.
- **Advanced Mood Analysis:**
    - Interactive line charts visualizing mood trends over different periods (daily, monthly, yearly).
    - Summary statistics for a quick overview of user counts and average moods.
- **Modern & Responsive UI:** Built with Bootstrap 5 and custom styling for a clean, luxurious, and mobile-friendly experience.

## Technology Stack

- **Backend:** PHP 8+ (Native)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Frameworks/Libraries:**
    - [Bootstrap 5](https://getbootstrap.com/) for responsive design and components.
    - [Chart.js](https://www.chartjs.org/) for interactive data visualization.
    - [Font Awesome](https://fontawesome.com/) for icons.
    - [Google Fonts (Poppins)](https://fonts.google.com/specimen/Poppins) for typography.

---

## Installation and Setup Guide

Follow these steps to set up and run the application on your local machine.

### 1. Prerequisites

Ensure you have a local server environment installed, such as:
- [XAMPP](https://www.apachefriends.org/index.html) (recommended for Windows/macOS/Linux)
- WAMP (for Windows)
- MAMP (for macOS)

This guide will assume you are using **XAMPP**.

### 2. Get the Code

Clone this repository or download the source code and place it in your server's web root directory.
- For XAMPP, this is typically the `htdocs` folder (e.g., `C:\xampp\htdocs`).
- You can place the project in a subfolder, for example: `C:\xampp\htdocs\mood-tracker`

### 3. Database Setup

The application requires a MySQL database to store all its data.

1.  **Start Apache and MySQL** from your XAMPP control panel.
2.  Open your web browser and navigate to `http://localhost/phpmyadmin`.
3.  **Create a new database:**
    - Click on the **"Databases"** tab.
    - Enter a name for the database, for example, `mood_tracker`.
    - Choose a collation (e.g., `utf8mb4_general_ci`) and click **"Create"**.
4.  **Import the SQL schema:**
    - Select the newly created database (`mood_tracker`) from the left-hand menu.
    - Click on the **"Import"** tab.
    - Click **"Choose File"** and select the `database.sql` file located in the root of this project.
    - Scroll down and click **"Go"**.

This will create all the necessary tables (`users`, `students`, `teachers`, `mood_records`) and seed them with sample data.

### 4. Configure the Application

You need to tell the application how to connect to your newly created database.

1.  Navigate to the `app` folder within the project directory.
2.  Open the `config.php` file in a text editor.
3.  Update the database credentials to match your local setup. If you used the name `mood_tracker` and have a standard XAMPP installation, the settings might look like this:

    ```php
    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root'); // Default XAMPP username
    define('DB_PASSWORD', '');     // Default XAMPP password is empty
    define('DB_NAME', 'mood_tracker');
    ```
4.  Update the `SITE_URL` to match the path to your project folder. For example, if your project is in `htdocs/mood-tracker`, the URL should be:
    ```php
    // Site Configuration
    define('SITE_URL', 'http://localhost/mood-tracker');
    ```

### 5. Running the Application

Once the setup is complete, you can access the application by navigating to the URL you configured in `config.php`.

-   **URL:** `http://localhost/mood-tracker` (or your chosen folder name)

---

## Default Login Credentials

You can use these sample accounts (which are included in `database.sql`) to test the application:

| Role      | Username   | Password   |
|-----------|------------|------------|
| **Admin**   | `admin`    | `password` |
| **Teacher** | `teacher1` | `password` |
| **Student** | `student1` | `password` |

You can add, edit, or delete these users from the Admin Dashboard after logging in as the `admin`.
