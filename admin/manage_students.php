<?php
$page_title = 'Kelola Siswa';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];

// --- CUD LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $class = sanitize_input($_POST['class']); // New class field
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

            $stmt_student = $mysqli->prepare("UPDATE students SET full_name = ?, class = ? WHERE user_id = ?");
            $stmt_student->bind_param("ssi", $full_name, $class, $user_id);
            $stmt_student->execute();
            $feedback = ['type' => 'success', 'message' => 'Siswa berhasil diperbarui!'];
        } else { // --- CREATE ---
            if (empty($password)) throw new Exception("Kata sandi diperlukan untuk pengguna baru.");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            $stmt_user = $mysqli->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt_user->bind_param("ssss", $username, $email, $password_hash, $role);
            $stmt_user->execute();

            $new_user_id = $mysqli->insert_id;

            $stmt_student = $mysqli->prepare("INSERT INTO students (user_id, full_name, class) VALUES (?, ?, ?)");
            $stmt_student->bind_param("iss", $new_user_id, $full_name, $class);
            $stmt_student->execute();
            $feedback = ['type' => 'success', 'message' => 'Siswa berhasil ditambahkan!'];
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

// Handle Delete Student
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $user_id_to_delete);
    if ($stmt->execute()) {
        $feedback = ['type' => 'success', 'message' => 'Siswa berhasil dihapus!'];
    } else {
        $feedback = ['type' => 'danger', 'message' => 'Gagal menghapus siswa.'];
    }
}

// --- VIEW LOGIC ---
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$is_form_view = ($action === 'add' || $action === 'edit');
$student_data = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $student_id_to_edit = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT u.id, u.username, u.email, s.full_name, s.class FROM users u JOIN students s ON u.id = s.user_id WHERE u.id = ? AND u.role = 'student'");
    $stmt->bind_param("i", $student_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Kelola Siswa</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="w-full <?php echo $feedback['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'; ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
    <p class="text-sm font-bold"><?php echo $feedback['message']; ?></p>
</div>
<?php endif; ?>

<?php if ($is_form_view): // --- ADD/EDIT FORM VIEW --- ?>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-t-8 border-fun-purple max-w-2xl">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700"><?php echo $action === 'edit' ? 'Ubah Siswa' : 'Tambah Siswa Baru'; ?></h5>
    </div>
    <div class="p-8">
        <form action="manage_students" method="POST" class="space-y-5">
            <?php if ($action === 'edit' && $student_data): ?>
                <input type="hidden" name="user_id" value="<?php echo $student_data['id']; ?>">
            <?php endif; ?>

            <div>
                <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student_data['full_name'] ?? ''); ?>" required>
            </div>
             <div>
                <label for="class" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="class" name="class" value="<?php echo htmlspecialchars($student_data['class'] ?? ''); ?>">
            </div>
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengguna</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="username" name="username" value="<?php echo htmlspecialchars($student_data['username'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="email" name="email" value="<?php echo htmlspecialchars($student_data['email'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                <input type="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-purple focus:border-transparent outline-none transition" id="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit'): ?>
                    <p class="mt-1 text-xs text-gray-400 italic">Biarkan kosong untuk mempertahankan kata sandi saat ini.</p>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-fun-purple hover:bg-opacity-90 text-white font-bold px-6 py-2 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 active:scale-95"><?php echo $action === 'edit' ? 'Perbarui Siswa' : 'Tambah Siswa'; ?></button>
                <a href="manage_students" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-2 rounded-xl transition">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php else: // --- LIST VIEW --- ?>
<div class="mb-6">
    <a href="?action=add" class="inline-flex items-center bg-fun-blue hover:bg-opacity-90 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition transform hover:-translate-y-1 active:scale-95"><i class="fas fa-plus mr-2"></i>Tambah Siswa Baru</a>
</div>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Daftar Siswa</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Kelas</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Username</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Email</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $result = $mysqli->query("SELECT u.id, u.username, u.email, s.full_name, s.class FROM users u JOIN students s ON u.id = s.user_id WHERE u.role = 'student' ORDER BY s.full_name ASC");
                if ($result && $result->num_rows > 0):
                    while ($student = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($student['class']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($student['username']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($student['email']); ?></td>
                            <td class="px-8 py-4 flex gap-2">
                                <a href="?action=edit&id=<?php echo $student['id']; ?>" class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition" title="Ubah"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $student['id']; ?>" class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition" onclick="return confirm('Apakah Anda yakin?');" title="Hapus"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="5" class="px-8 py-10 text-center text-gray-400 italic">Tidak ada siswa yang ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
