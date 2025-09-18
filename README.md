# Aplikasi Web Pelacak Suasana Hati

Aplikasi web berfitur lengkap yang dibuat dengan PHP natif dan MySQL yang memungkinkan siswa dan guru untuk mencatat suasana hati harian mereka. Aplikasi ini memiliki sistem manajemen multi-level untuk admin, guru, dan siswa, lengkap dengan antarmuka pengguna yang modern dan mewah.

## Fitur

- **Kontrol Akses Berbasis Peran:** Sistem login aman yang mengarahkan pengguna (Admin, Guru, Siswa) ke dasbor masing-masing.
- **Pencatatan Suasana Hati Harian:** Antarmuka yang elegan bagi siswa dan guru untuk memilih dan mencatat suasana hati mereka sekali sehari.
- **Dasbor Admin:** Dasbor komprehensif untuk administrator dengan menu navigasi samping yang modern.
- **Manajemen Pengguna (CRUD):** Admin dapat Membuat, Membaca, Memperbarui, dan Menghapus (CRUD) akun siswa, guru, dan admin lainnya.
- **Analisis Suasana Hati Tingkat Lanjut:**
    - Grafik garis interaktif yang memvisualisasikan tren suasana hati selama periode yang berbeda (harian, bulanan, tahunan).
    - Statistik ringkasan untuk gambaran cepat tentang jumlah pengguna dan suasana hati rata-rata.
- **UI Modern & Responsif:** Dibangun dengan Bootstrap 5 dan gaya kustom untuk pengalaman yang bersih, mewah, dan ramah seluler.

## Tumpukan Teknologi

- **Backend:** PHP 8+ (Natif)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Kerangka Kerja/Pustaka:**
    - [Bootstrap 5](https://getbootstrap.com/) untuk desain responsif dan komponen.
    - [Chart.js](https://www.chartjs.org/) untuk visualisasi data interaktif.
    - [Font Awesome](https://fontawesome.com/) untuk ikon.
    - [Google Fonts (Poppins)](https://fonts.google.com/specimen/Poppins) untuk tipografi.

---

## Panduan Instalasi dan Pengaturan

Ikuti langkah-langkah ini untuk mengatur dan menjalankan aplikasi di mesin lokal Anda.

### 1. Prasyarat

Pastikan Anda telah menginstal lingkungan server lokal, seperti:
- [XAMPP](https://www.apachefriends.org/index.html) (disarankan untuk Windows/macOS/Linux)
- WAMP (untuk Windows)
- MAMP (untuk macOS)

Panduan ini mengasumsikan Anda menggunakan **XAMPP**.

### 2. Dapatkan Kode

Klona repositori ini atau unduh kode sumber dan letakkan di direktori root web server Anda.
- Untuk XAMPP, biasanya ini adalah folder `htdocs` (misalnya, `C:\xampp\htdocs`).
- Anda dapat menempatkan proyek di subfolder, misalnya: `C:\xampp\htdocs\pelacak-suasana-hati`

### 3. Pengaturan Database

Aplikasi ini memerlukan database MySQL untuk menyimpan semua datanya.

1.  **Jalankan Apache dan MySQL** dari panel kontrol XAMPP Anda.
2.  Buka browser web Anda dan navigasikan ke `http://localhost/phpmyadmin`.
3.  **Buat database baru:**
    - Klik pada tab **"Basis Data"**.
    - Masukkan nama untuk database, misalnya, `mood_tracker`.
    - Pilih collation (misalnya, `utf8mb4_general_ci`) dan klik **"Buat"**.
4.  **Impor skema SQL:**
    - Pilih database yang baru dibuat (`mood_tracker`) dari menu sebelah kiri.
    - Klik pada tab **"Impor"**.
    - Klik **"Pilih File"** dan pilih file `database.sql` yang terletak di root proyek ini.
    - Gulir ke bawah dan klik **"Go"**.

Ini akan membuat semua tabel yang diperlukan (`users`, `students`, `teachers`, `mood_records`) dan mengisinya dengan data sampel.

### 4. Konfigurasi Aplikasi

Anda perlu memberitahu aplikasi cara terhubung ke database yang baru Anda buat.

1.  Navigasikan ke folder `app` di dalam direktori proyek.
2.  Buka file `config.php` di editor teks.
3.  Perbarui kredensial database agar sesuai dengan pengaturan lokal Anda. Jika Anda menggunakan nama `mood_tracker` dan memiliki instalasi XAMPP standar, pengaturannya mungkin terlihat seperti ini:

    ```php
    // Konfigurasi Database
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root'); // Nama pengguna default XAMPP
    define('DB_PASSWORD', '');     // Kata sandi default XAMPP kosong
    define('DB_NAME', 'mood_tracker');
    ```
4.  Perbarui `SITE_URL` agar sesuai dengan path ke folder proyek Anda. Misalnya, jika proyek Anda ada di `htdocs/pelacak-suasana-hati`, URL-nya harus:
    ```php
    // Konfigurasi Situs
    define('SITE_URL', 'http://localhost/pelacak-suasana-hati');
    ```

### 5. Menjalankan Aplikasi

Setelah pengaturan selesai, Anda dapat mengakses aplikasi dengan menavigasi ke URL yang Anda konfigurasikan di `config.php`.

-   **URL:** `http://localhost/pelacak-suasana-hati` (atau nama folder pilihan Anda)

---

## Kredensial Login Default

Anda dapat menggunakan akun sampel ini (yang disertakan dalam `database.sql`) untuk menguji aplikasi:

| Peran     | Nama Pengguna | Kata Sandi |
|-----------|---------------|------------|
| **Admin** | `admin`       | `password` |
| **Guru**  | `teacher1`    | `password` |
| **Siswa** | `student1`    | `password` |

Anda dapat menambah, mengubah, atau menghapus pengguna ini dari Dasbor Admin setelah masuk sebagai `admin`.
