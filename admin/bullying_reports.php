<?php
$page_title = 'Laporan Perundungan';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $report_id = (int)$_POST['report_id'];
    $new_status = $_POST['new_status'];

    // Validate status
    if (in_array($new_status, ['Baru', 'Diproses', 'Selesai'])) {
        $stmt = $mysqli->prepare("UPDATE bullying_reports SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $report_id);
        if ($stmt->execute()) {
            $feedback = ['type' => 'success', 'message' => 'Status laporan berhasil diperbarui.'];
        } else {
            $feedback = ['type' => 'danger', 'message' => 'Gagal memperbarui status.'];
        }
    } else {
        $feedback = ['type' => 'danger', 'message' => 'Status tidak valid.'];
    }
}


// Fetch all bullying reports
$query = "SELECT
            br.id,
            br.report_description,
            br.report_date,
            br.status,
            s.full_name AS student_name,
            s.class AS student_class
          FROM bullying_reports br
          JOIN users u ON br.student_user_id = u.id
          JOIN students s ON u.id = s.user_id
          ORDER BY br.report_date DESC";

$result = $mysqli->query($query);
?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Laporan Perundungan</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="w-full <?php echo $feedback['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'; ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
    <p class="text-sm font-bold"><?php echo $feedback['message']; ?></p>
</div>
<?php endif; ?>

<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Semua Laporan yang Masuk</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Pelapor</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Kelas</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Deskripsi Singkat</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Tanggal</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Status</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($row['student_class']); ?></td>
                            <td class="px-8 py-4 text-gray-600 text-sm italic">"<?php echo htmlspecialchars(substr($row['report_description'], 0, 100)); ?>..."</td>
                            <td class="px-8 py-4 text-gray-600 text-xs"><?php echo date('d M Y, H:i', strtotime($row['report_date'])); ?></td>
                            <td class="px-8 py-4">
                                <?php
                                $status_classes = '';
                                switch ($row['status']) {
                                    case 'Baru': $status_classes = 'bg-blue-100 text-blue-600'; break;
                                    case 'Diproses': $status_classes = 'bg-yellow-100 text-yellow-600'; break;
                                    case 'Selesai': $status_classes = 'bg-green-100 text-green-600'; break;
                                }
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $status_classes; ?>"><?php echo $row['status']; ?></span>
                            </td>
                            <td class="px-8 py-4">
                                <form action="bullying_reports.php" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status" class="text-sm border rounded-lg px-2 py-1 outline-none focus:ring-2 focus:ring-fun-purple">
                                        <option value="Baru" <?php if($row['status'] == 'Baru') echo 'selected'; ?>>Baru</option>
                                        <option value="Diproses" <?php if($row['status'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
                                        <option value="Selesai" <?php if($row['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                    <button type="submit" name="update_status" class="bg-fun-purple text-white p-2 rounded-lg hover:bg-opacity-90 transition" title="Update Status">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-8 py-10 text-center text-gray-400 italic">Tidak ada laporan perundungan yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
