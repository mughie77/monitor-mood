<?php
$page_title = 'Kelola Guru';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];

// --- CUD LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    $mysqli->begin_transaction();
    try {
        if ($user_id) { // --- UPDATE ---
            $stmt_user = $mysqli->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt_user->bind_param("ssi", $username, $email, $user_id);
            $stmt_user->execute();

            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_pass = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt_pass->bind_param("si", $password_hash, $user_id);
                $stmt_pass->execute();
            }

            $stmt_teacher = $mysqli->prepare("UPDATE teachers SET full_name = ? WHERE user_id = ?");
            $stmt_teacher->bind_param("si", $full_name, $user_id);
            $stmt_teacher->execute();
            $feedback = ['type' => 'success', 'message' => 'Guru berhasil diperbarui!'];
        } else { // --- CREATE ---
            if (empty($password)) throw new Exception("Kata sandi diperlukan untuk pengguna baru.");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'teacher';

            $stmt_user = $mysqli->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt_user->bind_param("ssss", $username, $email, $password_hash, $role);
            $stmt_user->execute();

            $new_user_id = $mysqli->insert_id;

            $stmt_teacher = $mysqli->prepare("INSERT INTO teachers (user_id, full_name) VALUES (?, ?)");
            $stmt_teacher->bind_param("is", $new_user_id, $full_name);
            $stmt_teacher->execute();
            $feedback = ['type' => 'success', 'message' => 'Guru berhasil ditambahkan!'];
        }
        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        $feedback = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        if ($mysqli->errno === 1062) {
            $feedback['message'] = 'Nama pengguna atau email sudah ada.';
        }
    }
}

// Handle Delete Teacher
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'teacher'");
    $stmt->bind_param("i", $user_id_to_delete);
    if ($stmt->execute()) {
        $feedback = ['type' => 'success', 'message' => 'Guru berhasil dihapus!'];
    } else {
        $feedback = ['type' => 'danger', 'message' => 'Gagal menghapus guru.'];
    }
}

// --- VIEW LOGIC ---
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$is_form_view = ($action === 'add' || $action === 'edit');
$teacher_data = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $teacher_id_to_edit = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT u.id, u.username, u.email, t.full_name FROM users u JOIN teachers t ON u.id = t.user_id WHERE u.id = ? AND u.role = 'teacher'");
    $stmt->bind_param("i", $teacher_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher_data = $result->fetch_assoc();
}
?>

<h1 class="mb-4">Kelola Guru</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="alert alert-<?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
<?php endif; ?>

<?php if ($is_form_view): ?>
<div class="card">
    <div class="card-header"><h5><?php echo $action === 'edit' ? 'Ubah Guru' : 'Tambah Guru Baru'; ?></h5></div>
    <div class="card-body">
        <form action="manage_teachers" method="POST">
            <?php if ($action === 'edit' && $teacher_data): ?>
                <input type="hidden" name="user_id" value="<?php echo $teacher_data['id']; ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($teacher_data['full_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Nama Pengguna</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($teacher_data['username'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($teacher_data['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit'): ?><small class="form-text text-muted">Biarkan kosong untuk mempertahankan kata sandi saat ini.</small><?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Perbarui Guru' : 'Tambah Guru'; ?></button>
            <a href="manage_teachers" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="mb-3"><a href="?action=add" class="btn btn-success"><i class="fas fa-plus me-2"></i>Tambah Guru Baru</a></div>
<div class="card">
    <div class="card-header">Daftar Guru</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>Nama Lengkap</th><th>Nama Pengguna</th><th>Email</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $result = $mysqli->query("SELECT u.id, u.username, u.email, t.full_name FROM users u JOIN teachers t ON u.id = t.user_id WHERE u.role = 'teacher' ORDER BY t.full_name ASC");
                    if ($result && $result->num_rows > 0):
                        while ($teacher = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Ubah</a>
                                    <a href="?action=delete&id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?');"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="4" class="text-center">Tidak ada guru yang ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
