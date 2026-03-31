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

<h1 class="text-3xl font-bold text-gray-800 mb-8">Kelola Kontak Darurat</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="w-full <?php echo $feedback['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'; ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
    <p class="text-sm font-bold"><?php echo $feedback['message']; ?></p>
</div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-t-8 border-fun-blue max-w-2xl">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700"><?php echo $action === 'edit' ? 'Ubah Kontak' : 'Tambah Kontak Baru'; ?></h5>
    </div>
    <div class="p-8">
        <form action="manage_contacts" method="POST" class="space-y-5">
            <input type="hidden" name="save_contact" value="1">
            <?php if ($action === 'edit' && $contact_data): ?>
                <input type="hidden" name="id" value="<?php echo $contact_data['id']; ?>">
            <?php endif; ?>
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kontak</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="name" name="name" value="<?php echo htmlspecialchars($contact_data['name'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($contact_data['phone_number'] ?? ''); ?>" required placeholder="6281234567890">
                <p class="mt-1 text-xs text-gray-400 italic">Gunakan format internasional tanpa tanda '+' atau spasi.</p>
            </div>
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-fun-blue focus:border-transparent outline-none transition" id="description" name="description" value="<?php echo htmlspecialchars($contact_data['description'] ?? ''); ?>">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-fun-blue hover:bg-opacity-90 text-white font-bold px-6 py-2 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 active:scale-95"><?php echo $action === 'edit' ? 'Perbarui' : 'Tambah'; ?></button>
                <a href="manage_contacts" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-2 rounded-xl transition">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="mb-6">
    <a href="?action=add" class="inline-flex items-center bg-fun-blue hover:bg-opacity-90 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition transform hover:-translate-y-1 active:scale-95"><i class="fas fa-plus mr-2"></i>Tambah Kontak Baru</a>
</div>
<div class="bg-white rounded-3xl shadow-sm overflow-hidden border-b-4 border-gray-200">
    <div class="bg-gray-50 px-8 py-4 border-b">
        <h5 class="font-bold text-gray-700">Daftar Kontak</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Nama</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">WhatsApp</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Deskripsi</th>
                    <th class="px-8 py-4 font-bold text-gray-600 text-sm uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $result = $mysqli->query("SELECT * FROM emergency_contacts ORDER BY name ASC");
                if ($result && $result->num_rows > 0):
                    while ($contact = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-4 text-gray-800 font-medium"><?php echo htmlspecialchars($contact['name']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($contact['phone_number']); ?></td>
                            <td class="px-8 py-4 text-gray-600"><?php echo htmlspecialchars($contact['description']); ?></td>
                            <td class="px-8 py-4 flex gap-2">
                                <a href="?action=edit&id=<?php echo $contact['id']; ?>" class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition" title="Ubah"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $contact['id']; ?>" class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition" onclick="return confirm('Apakah Anda yakin?');" title="Hapus"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">Belum ada kontak darurat yang ditambahkan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
