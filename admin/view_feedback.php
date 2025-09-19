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

<h1 class="mb-4">Daftar Kritik dan Saran dari Siswa</h1>

<div class="card">
    <div class="card-header">
        Semua Masukan yang Diterima
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 15%;">Siswa</th>
                        <th style="width: 10%;">Kelas</th>
                        <th style="width: 15%;">Tanggal Kirim</th>
                        <th>Isi Masukan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_class']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['submission_date'])); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['feedback_text'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada kritik atau saran yang masuk.</td>
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
