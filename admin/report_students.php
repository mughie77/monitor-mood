<?php
$page_title = 'Laporan Mood Siswa';
require_once 'templates/header.php';

// --- Filter Logic ---
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Default to today

// Base query
$query = "SELECT
            s.full_name,
            s.class,
            mr.mood_value,
            mr.record_date
          FROM mood_records mr
          JOIN users u ON mr.user_id = u.id
          JOIN students s ON u.id = s.user_id
          WHERE u.role = 'student'";

// Append date filter if provided
$params = [];
$types = '';
if ($start_date && $end_date) {
    $query .= " AND mr.record_date BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $start_date;
    $params[] = $end_date;
} elseif ($end_date) {
    // If only end date is provided, maybe filter up to that date. For now, we require both for a range.
    // Or let's just filter by end_date if start_date is missing.
    // Let's stick to requiring a start and end for a range filter to be active.
}

$query .= " ORDER BY mr.record_date DESC, s.full_name ASC";

$stmt = $mysqli->prepare($query);
if ($start_date && $end_date) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Laporan Mood Siswa</h1>

<!-- Filter Form -->
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-t-8 border-fun-purple mb-8">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Filter Laporan</h5>
    </div>
    <div class="p-8">
        <form action="report_students.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date ?? date('Y-m-d')); ?>">
            </div>
            <button type="submit" class="bg-fun-purple hover:bg-opacity-90 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 active:scale-95">
                Filter
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">
            Daftar Semua Entri <?php if($start_date && $end_date) echo "<span class='text-fun-purple font-normal ml-2'>dari " . htmlspecialchars($start_date) . " hingga " . htmlspecialchars($end_date) . "</span>"; ?>
        </h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Kelas</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Suasana Hati</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($row['class']); ?></td>
                            <td class="px-8 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-opacity-10 <?php
                                    echo match((int)$row['mood_value']) {
                                        1 => 'bg-red-500 text-red-600',
                                        2 => 'bg-gray-500 text-gray-600',
                                        3 => 'bg-yellow-500 text-yellow-600',
                                        4 => 'bg-orange-500 text-orange-600',
                                        5 => 'bg-green-500 text-green-600',
                                        default => 'bg-gray-500 text-gray-600'
                                    };
                                ?>">
                                    <?php echo get_mood_description($row['mood_value']); ?>
                                </span>
                            </td>
                            <td class="px-8 py-4 text-gray-600"><?php echo date('d M Y', strtotime($row['record_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">Tidak ada entri suasana hati dari siswa yang ditemukan untuk rentang tanggal yang dipilih.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
