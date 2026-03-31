<?php
$page_title = 'Kritik dan Saran';
require_once 'templates/header.php';

// Fetch all feedback records
$query = "SELECT
            f.id,
            f.feedback_text,
            f.submission_date,
            s.full_name AS student_name,
            s.class AS student_class
          FROM feedback f
          JOIN users u ON f.student_user_id = u.id
          JOIN students s ON u.id = s.user_id
          ORDER BY f.submission_date DESC";

$result = $mysqli->query($query);
?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Kritik dan Saran</h1>

<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Semua Masukan yang Diterima</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider w-1/6">Siswa</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider w-1/12">Kelas</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider w-1/6">Tanggal</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Isi Masukan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($row['student_class']); ?></td>
                            <td class="px-8 py-4 text-gray-600 text-xs"><?php echo date('d M Y, H:i', strtotime($row['submission_date'])); ?></td>
                            <td class="px-8 py-4 text-gray-700 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($row['feedback_text'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">Belum ada kritik atau saran yang masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
