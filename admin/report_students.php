<?php
$page_title = 'Laporan Mood Siswa';
require_once 'templates/header.php';

// Fetch all mood records for students
$query = "SELECT
            s.full_name,
            s.class,
            mr.mood_value,
            mr.record_date
          FROM mood_records mr
          JOIN users u ON mr.user_id = u.id
          JOIN students s ON u.id = s.user_id
          WHERE u.role = 'student'
          ORDER BY mr.record_date DESC, s.full_name ASC";

$result = $mysqli->query($query);
?>

<h1 class="mb-4">Laporan Entri Mood Siswa</h1>

<div class="card">
    <div class="card-header">
        Daftar Semua Entri
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Suasana Hati</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['class']); ?></td>
                                <td><?php echo get_mood_description($row['mood_value']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['record_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada entri suasana hati dari siswa yang ditemukan.</td>
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
