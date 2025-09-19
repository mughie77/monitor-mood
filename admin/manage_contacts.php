<?php
$page_title = 'Kelola Kontak Darurat';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];

// --- CUD LOGIC ---
// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $name = sanitize_input($_POST['name']);
    $phone_number = sanitize_input($_POST['phone_number']);
    $description = sanitize_input($_POST['description']);

    try {
        if ($id) { // --- UPDATE ---
            $stmt = $mysqli->prepare("UPDATE emergency_contacts SET name = ?, phone_number = ?, description = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $phone_number, $description, $id);
            $stmt->execute();
            $feedback = ['type' => 'success', 'message' => 'Kontak berhasil diperbarui!'];
        } else { // --- CREATE ---
            $stmt = $mysqli->prepare("INSERT INTO emergency_contacts (name, phone_number, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $phone_number, $description);
            $stmt->execute();
            $feedback = ['type' => 'success', 'message' => 'Kontak berhasil ditambahkan!'];
        }
    } catch (Exception $e) {
        $feedback = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM emergency_contacts WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $feedback = ['type' => 'success', 'message' => 'Kontak berhasil dihapus!'];
    } else {
        $feedback = ['type' => 'danger', 'message' => 'Gagal menghapus kontak.'];
    }
}

// --- VIEW LOGIC ---
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$contact_data = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $id_to_edit = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM emergency_contacts WHERE id = ?");
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact_data = $result->fetch_assoc();
}
?>

<h1 class="mb-4">Kelola Kontak Darurat</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="alert alert-<?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header"><h5><?php echo $action === 'edit' ? 'Ubah Kontak' : 'Tambah Kontak Baru'; ?></h5></div>
    <div class="card-body">
        <form action="manage_contacts" method="POST">
            <input type="hidden" name="save_contact" value="1">
            <?php if ($action === 'edit' && $contact_data): ?>
                <input type="hidden" name="id" value="<?php echo $contact_data['id']; ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kontak</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($contact_data['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($contact_data['phone_number'] ?? ''); ?>" required placeholder="Contoh: 6281234567890">
                <small class="form-text text-muted">Gunakan format internasional tanpa tanda '+' atau spasi.</small>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi (Opsional)</label>
                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($contact_data['description'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Perbarui' : 'Tambah'; ?></button>
            <a href="manage_contacts" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="mb-3"><a href="?action=add" class="btn btn-success"><i class="fas fa-plus me-2"></i>Tambah Kontak Baru</a></div>
<div class="card">
    <div class="card-header">Daftar Kontak</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>Nama</th><th>Nomor WhatsApp</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $result = $mysqli->query("SELECT * FROM emergency_contacts ORDER BY name ASC");
                    if ($result && $result->num_rows > 0):
                        while ($contact = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td><?php echo htmlspecialchars($contact['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($contact['description']); ?></td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Ubah</a>
                                    <a href="?action=delete&id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?');"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="4" class="text-center">Belum ada kontak darurat yang ditambahkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
