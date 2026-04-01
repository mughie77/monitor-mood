<?php
$page_title = 'Dasbor Saya';
require_once 'templates/header.php';

// Protect page for specific roles
protect_page(['teacher', 'student']);

// Fetch user's full name
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$table_name = $role . 's';

$user_profile = null;
if ($role === 'student' || $role === 'teacher') {
    $stmt = $mysqli->prepare("SELECT full_name FROM $table_name WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_profile = $result->fetch_assoc();
    $stmt->close();
}
$full_name = $user_profile ? $user_profile['full_name'] : $_SESSION['username'];

// Check if mood for today has already been submitted
$today = date("Y-m-d");
$stmt = $mysqli->prepare("SELECT mood_value FROM mood_records WHERE user_id = ? AND record_date = ?");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$mood_today = $result->fetch_assoc();
$stmt->close();

?>

<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h5 class="text-fun-pink font-bold uppercase tracking-wider text-sm mb-1">Aplikasi SI-SONYA</h5>
            <h2 class="text-3xl font-extrabold text-gray-800">Selamat Datang, <span class="text-fun-blue"><?php echo htmlspecialchars($full_name); ?></span>!</h2>
        </div>
        <a href="logout" class="px-6 py-2 border-2 border-red-400 text-red-500 font-bold rounded-full hover:bg-red-400 hover:text-white transition">Keluar</a>
    </div>

    <div id="mood-response-message" class="mb-6"></div>

    <!-- Main Mood Card -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-b-8 border-fun-yellow" id="mood-panel">
        <div class="p-8 md:p-12 text-center">
            <?php if ($mood_today): ?>
                <div class="mood-submitted-view">
                    <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-circle text-5xl"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-green-600 mb-2">Terima kasih!</h4>
                    <p class="text-gray-500">Anda sudah mencatat suasana hati hari ini. Kembali lagi besok!</p>
                </div>
            <?php else: ?>
                <div id="mood-selector-ui">
                    <h4 class="text-2xl font-bold text-gray-800 mb-8">Bagaimana perasaanmu hari ini?</h4>
                    <div class="flex flex-wrap justify-center gap-4 md:gap-8">
                        <div class="mood-option group cursor-pointer text-center transition transform hover:-translate-y-2" data-mood-value="1">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-red-100 transition">
                                <i class="fas fa-face-frown text-3xl md:text-4xl text-gray-400 group-hover:text-red-500 transition"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-red-600">Sedih</span>
                        </div>
                        <div class="mood-option group cursor-pointer text-center transition transform hover:-translate-y-2" data-mood-value="2">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-gray-200 transition">
                                <i class="fas fa-face-meh text-3xl md:text-4xl text-gray-400 group-hover:text-gray-600 transition"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-800">Biasa</span>
                        </div>
                        <div class="mood-option group cursor-pointer text-center transition transform hover:-translate-y-2" data-mood-value="3">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-yellow-100 transition">
                                <i class="fas fa-face-smile text-3xl md:text-4xl text-gray-400 group-hover:text-yellow-500 transition"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-yellow-600">Senang</span>
                        </div>
                        <div class="mood-option group cursor-pointer text-center transition transform hover:-translate-y-2" data-mood-value="4">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-orange-100 transition">
                                <i class="fas fa-face-grin-beam text-3xl md:text-4xl text-gray-400 group-hover:text-fun-orange transition"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-fun-orange">Bersemangat</span>
                        </div>
                        <div class="mood-option group cursor-pointer text-center transition transform hover:-translate-y-2" data-mood-value="5">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-blue-100 transition">
                                <i class="fas fa-face-grin-stars text-3xl md:text-4xl text-gray-400 group-hover:text-fun-green transition"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-fun-green">Luar Biasa</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Other Actions for Students -->
    <?php if ($role === 'student'): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div class="bg-white p-8 rounded-3xl shadow-lg border-t-8 border-fun-pink relative group hover:shadow-xl transition">
            <div class="w-16 h-16 bg-red-100 text-fun-pink rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-bullhorn text-2xl"></i>
            </div>
            <h5 class="text-xl font-bold text-gray-800 mb-2">Lapor Perundungan</h5>
            <p class="text-gray-500 mb-6 text-sm">Laporkan perundungan secara rahasia untuk menjaga lingkungan sekolah tetap aman.</p>
            <a href="bullying_form" class="inline-block bg-fun-pink text-white font-bold px-6 py-2 rounded-xl hover:bg-opacity-90 transition stretched-link">Buat Laporan</a>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-lg border-t-8 border-fun-blue relative group hover:shadow-xl transition">
            <div class="w-16 h-16 bg-blue-100 text-fun-blue rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-lightbulb text-2xl"></i>
            </div>
            <h5 class="text-xl font-bold text-gray-800 mb-2">Kritik dan Saran</h5>
            <p class="text-gray-500 mb-6 text-sm">Punya ide atau masukan untuk membuat sekolah lebih baik? Sampaikan di sini.</p>
            <a href="feedback_form" class="inline-block bg-fun-blue text-white font-bold px-6 py-2 rounded-xl hover:bg-opacity-90 transition stretched-link">Beri Masukan</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Emergency Contacts Section -->
    <div class="bg-white rounded-3xl shadow-lg mt-8 overflow-hidden">
        <div class="bg-fun-green px-8 py-4 flex items-center">
            <i class="fas fa-life-ring text-white text-xl mr-3"></i>
            <h5 class="text-white font-bold">Kontak Darurat</h5>
        </div>
        <div class="p-4">
            <?php
            $contacts_result = $mysqli->query("SELECT * FROM emergency_contacts ORDER BY name ASC");
            if ($contacts_result && $contacts_result->num_rows > 0):
            ?>
                <div class="divide-y">
                    <?php while ($contact = $contacts_result->fetch_assoc()): ?>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 gap-4">
                            <div>
                                <strong class="block text-gray-800 text-lg"><?php echo htmlspecialchars($contact['name']); ?></strong>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($contact['description']); ?></p>
                            </div>
                            <a href="https://wa.me/<?php echo htmlspecialchars($contact['phone_number']); ?>" target="_blank" class="flex items-center bg-green-500 text-white font-bold px-5 py-2 rounded-full hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp mr-2 text-xl"></i>Hubungi
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-gray-400">Belum ada kontak darurat yang dikonfigurasi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
require_once 'templates/footer.php';
?>
