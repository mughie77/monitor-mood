<?php
$page_title = 'Kelola Admin';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];
$current_admin_id = $_SESSION['user_id'];

// --- CUD LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    try {
        if ($user_id) { // --- UPDATE ---
            $stmt_user = $mysqli->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'admin'");
            $stmt_user->bind_param("ssi", $username, $email, $user_id);
            $stmt_user->execute();

            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_pass = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt_pass->bind_param("si", $password_hash, $user_id);
                $stmt_pass->execute();
            }
            $feedback = ['type' => 'success', 'message' => 'Admin berhasil diperbarui!'];
        } else { // --- CREATE ---
            if (empty($password)) throw new Exception("Kata sandi diperlukan untuk pengguna baru.");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'admin';

            $stmt_user = $mysqli->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt_user->bind_param("ssss", $username, $email, $password_hash, $role);
            $stmt_user->execute();
            $feedback = ['type' => 'success', 'message' => 'Admin berhasil ditambahkan!'];
        }
    } catch (Exception $e) {
        $feedback = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        if ($mysqli->errno === 1062) {
            $feedback['message'] = 'Nama pengguna atau email sudah ada.';
        }
    }
}

// Handle Delete Admin
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];
    if ($user_id_to_delete === $current_admin_id) {
        $feedback = ['type' => 'danger', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'];
    } else {
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
        $stmt->bind_param("i", $user_id_to_delete);
        if ($stmt->execute()) {
            $feedback = ['type' => 'success', 'message' => 'Admin berhasil dihapus!'];
        } else {
            $feedback = ['type' => 'danger', 'message' => 'Gagal menghapus admin.'];
        }
    }
}

// --- VIEW LOGIC ---
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$is_form_view = ($action === 'add' || $action === 'edit');
$admin_data = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $admin_id_to_edit = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT id, username, email FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $admin_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();
}
?>

<h1 class="mb-4">Kelola Admin</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="alert alert-<?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
<?php endif; ?>

<?php if ($is_form_view): ?>
<div class="card">
    <div class="card-header"><h5><?php echo $action === 'edit' ? 'Ubah Admin' : 'Tambah Admin Baru'; ?></h5></div>
    <div class="card-body">
        <form action="manage_admins.php" method="POST">
            <?php if ($action === 'edit' && $admin_data): ?>
                <input type="hidden" name="user_id" value="<?php echo $admin_data['id']; ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Nama Pengguna</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin_data['username'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit'): ?><small class="form-text text-muted">Biarkan kosong untuk mempertahankan kata sandi saat ini.</small><?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Perbarui Admin' : 'Tambah Admin'; ?></button>
            <a href="manage_admins.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="mb-3"><a href="?action=add" class="btn btn-success"><i class="fas fa-plus me-2"></i>Tambah Admin Baru</a></div>
<div class="card">
    <div class="card-header">Daftar Admin</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>Nama Pengguna</th><th>Email</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $result = $mysqli->query("SELECT id, username, email FROM users WHERE role = 'admin' ORDER BY username ASC");
                    if ($result && $result->num_rows > 0):
                        while ($admin = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $admin['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Ubah</a>
                                    <?php if ($admin['id'] !== $current_admin_id): // Prevent self-delete button from showing ?>
                                    <a href="?action=delete&id=<?php echo $admin['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?');"><i class="fas fa-trash"></i> Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="3" class="text-center">Tidak ada admin yang ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
