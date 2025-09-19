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

<h1 class="mb-4">Daftar Laporan Perundungan</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="alert alert-<?php echo $feedback['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo $feedback['message']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Semua Laporan yang Masuk
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Pelapor</th>
                        <th>Kelas</th>
                        <th>Deskripsi Singkat</th>
                        <th>Tanggal Laporan</th>
                        <th>Status</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_class']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['report_description'], 0, 100)); ?>...</td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['report_date'])); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($row['status']) {
                                        case 'Baru': $status_class = 'bg-primary'; break;
                                        case 'Diproses': $status_class = 'bg-warning text-dark'; break;
                                        case 'Selesai': $status_class = 'bg-success'; break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $row['status']; ?></span>
                                </td>
                                <td>
                                    <form action="bullying_reports.php" method="POST" class="d-flex">
                                        <input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
                                        <select name="new_status" class="form-select form-select-sm me-2">
                                            <option value="Baru" <?php if($row['status'] == 'Baru') echo 'selected'; ?>>Baru</option>
                                            <option value="Diproses" <?php if($row['status'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
                                            <option value="Selesai" <?php if($row['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada laporan perundungan yang ditemukan.</td>
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
