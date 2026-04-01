<?php
$page_title = 'Kritik dan Saran';
require_once 'templates/header.php';

// Only students can access this page
protect_page(['student']);

// Check for any feedback messages from the submission script
$feedback_message = '';
if (isset($_SESSION['feedback'])) {
    $feedback_message = $_SESSION['feedback']['message'];
    $feedback_type = $_SESSION['feedback']['type'];
    unset($_SESSION['feedback']); // Clear the message after displaying
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-3xl font-extrabold text-gray-800">Formulir Kritik dan Saran</h2>
        <a href="dashboard" class="px-6 py-2 border-2 border-gray-300 text-gray-600 font-bold rounded-full hover:bg-gray-100 transition">Kembali ke Dasbor</a>
    </div>

    <?php if (!empty($feedback_message)): ?>
        <div class="w-full <?php echo $feedback_type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'; ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
            <p class="font-bold"><?php echo ucfirst($feedback_type); ?></p>
            <p><?php echo $feedback_message; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-t-8 border-fun-blue">
        <div class="bg-gray-50 px-8 py-4 border-b">
            <p class="text-gray-600">Kami menghargai masukan Anda untuk membuat sekolah menjadi tempat yang lebih baik.</p>
        </div>
        <div class="p-8">
            <form action="app/submit_feedback" method="POST">
                <div class="mb-6">
                    <label for="feedback_text" class="block text-sm font-bold text-gray-700 mb-2">Kritik atau Saran Anda</label>
                    <textarea class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="feedback_text" name="feedback_text" rows="8" required placeholder="Tuliskan masukan Anda di sini..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-fun-blue hover:bg-opacity-90 text-white font-bold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1 active:scale-95 flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Masukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
