<?php
$page_title = 'Laporan Mood Guru';
require_once 'templates/header.php';

// --- Filter Logic ---
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Base query
$query = "SELECT
            t.full_name,
            mr.mood_value,
            mr.record_date
          FROM mood_records mr
          JOIN users u ON mr.user_id = u.id
          JOIN teachers t ON u.id = t.user_id
          WHERE u.role = 'teacher'";

// Append date filter if provided
$params = [];
$types = '';
if ($start_date && $end_date) {
    $query .= " AND mr.record_date BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $start_date;
    $params[] = $end_date;
}

$query .= " ORDER BY mr.record_date DESC, t.full_name ASC";

$stmt = $mysqli->prepare($query);
if ($start_date && $end_date) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">Laporan Entri Mood Guru</h1>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-header">Filter Laporan</div>
    <div class="card-body">
        <form action="report_teachers.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">
            </div>
            <div class="col-md-5">
                <label for="end_date" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date ?? date('Y-m-d')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-header">
        Daftar Semua Entri <?php if($start_date && $end_date) echo "dari " . htmlspecialchars($start_date) . " hingga " . htmlspecialchars($end_date); ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>Suasana Hati</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo get_mood_description($row['mood_value']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['record_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada entri suasana hati dari guru yang ditemukan untuk rentang tanggal yang dipilih.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
