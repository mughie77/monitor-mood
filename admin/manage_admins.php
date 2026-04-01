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

<h1 class="text-3xl font-bold text-gray-800 mb-8">Kelola Admin</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="w-full <?php echo $feedback['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'; ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
    <p class="text-sm font-bold"><?php echo $feedback['message']; ?></p>
</div>
<?php endif; ?>

<?php if ($is_form_view): ?>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-t-8 border-fun-blue max-w-2xl">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700"><?php echo $action === 'edit' ? 'Ubah Admin' : 'Tambah Admin Baru'; ?></h5>
    </div>
    <div class="p-8">
        <form action="manage_admins" method="POST" class="space-y-5">
            <?php if ($action === 'edit' && $admin_data): ?>
                <input type="hidden" name="user_id" value="<?php echo $admin_data['id']; ?>">
            <?php endif; ?>
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengguna</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="username" name="username" value="<?php echo htmlspecialchars($admin_data['username'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="email" name="email" value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                <input type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit'): ?>
                    <p class="mt-1 text-xs text-gray-400 italic">Biarkan kosong untuk mempertahankan kata sandi saat ini.</p>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-fun-blue hover:bg-opacity-90 text-white font-bold px-6 py-2 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 active:scale-95"><?php echo $action === 'edit' ? 'Perbarui Admin' : 'Tambah Admin'; ?></button>
                <a href="manage_admins" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-2 rounded-xl transition">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="mb-6">
    <a href="?action=add" class="inline-flex items-center bg-fun-green hover:bg-opacity-90 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition transform hover:-translate-y-1 active:scale-95"><i class="fas fa-plus mr-2"></i>Tambah Admin Baru</a>
</div>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Daftar Admin</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Username</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Email</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $result = $mysqli->query("SELECT id, username, email FROM users WHERE role = 'admin' ORDER BY username ASC");
                if ($result && $result->num_rows > 0):
                    while ($admin = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td class="px-8 py-4 flex gap-2">
                                <a href="?action=edit&id=<?php echo $admin['id']; ?>" class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition" title="Ubah"><i class="fas fa-edit"></i></a>
                                <?php if ($admin['id'] !== $current_admin_id): // Prevent self-delete button from showing ?>
                                    <a href="?action=delete&id=<?php echo $admin['id']; ?>" class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition" onclick="return confirm('Apakah Anda yakin?');" title="Hapus"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="3" class="px-8 py-10 text-center text-gray-400 italic">Tidak ada admin yang ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
