<?php
$page_title = 'Manage Students';
require_once 'templates/header.php';

$feedback = ['type' => '', 'message' => ''];

// --- CUD LOGIC ---
// Handle Add/Edit Student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password, it will be hashed

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

            $stmt_student = $mysqli->prepare("UPDATE students SET full_name = ? WHERE user_id = ?");
            $stmt_student->bind_param("si", $full_name, $user_id);
            $stmt_student->execute();
            $feedback = ['type' => 'success', 'message' => 'Student updated successfully!'];
        } else { // --- CREATE ---
            if (empty($password)) throw new Exception("Password is required for new users.");
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            $stmt_user = $mysqli->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt_user->bind_param("ssss", $username, $email, $password_hash, $role);
            $stmt_user->execute();

            $new_user_id = $mysqli->insert_id;

            $stmt_student = $mysqli->prepare("INSERT INTO students (user_id, full_name) VALUES (?, ?)");
            $stmt_student->bind_param("is", $new_user_id, $full_name);
            $stmt_student->execute();
            $feedback = ['type' => 'success', 'message' => 'Student added successfully!'];
        }
        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        $feedback = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        if ($mysqli->errno === 1062) { // Duplicate entry
            $feedback['message'] = 'Username or email already exists.';
        }
    }
}

// Handle Delete Student
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $user_id_to_delete);
    if ($stmt->execute()) {
        $feedback = ['type' => 'success', 'message' => 'Student deleted successfully!'];
    } else {
        $feedback = ['type' => 'danger', 'message' => 'Failed to delete student.'];
    }
}

// --- VIEW LOGIC ---
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$is_form_view = ($action === 'add' || $action === 'edit');
$student_data = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $student_id_to_edit = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT u.id, u.username, u.email, s.full_name FROM users u JOIN students s ON u.id = s.user_id WHERE u.id = ? AND u.role = 'student'");
    $stmt->bind_param("i", $student_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
}
?>

<h1 class="mb-4">Manage Students</h1>

<?php if (!empty($feedback['message'])): ?>
<div class="alert alert-<?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
<?php endif; ?>

<?php if ($is_form_view): // --- ADD/EDIT FORM VIEW --- ?>
<div class="card">
    <div class="card-header">
        <h5><?php echo $action === 'edit' ? 'Edit Student' : 'Add New Student'; ?></h5>
    </div>
    <div class="card-body">
        <form action="manage_students.php" method="POST">
            <?php if ($action === 'edit' && $student_data): ?>
                <input type="hidden" name="user_id" value="<?php echo $student_data['id']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student_data['full_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($student_data['username'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student_data['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit'): ?>
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Update Student' : 'Add Student'; ?></button>
            <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php else: // --- LIST VIEW --- ?>
<div class="mb-3">
    <a href="?action=add" class="btn btn-success"><i class="fas fa-plus me-2"></i>Add New Student</a>
</div>
<div class="card">
    <div class="card-header">Student List</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr><th>Full Name</th><th>Username</th><th>Email</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php
                    $result = $mysqli->query("SELECT u.id, u.username, u.email, s.full_name FROM users u JOIN students s ON u.id = s.user_id WHERE u.role = 'student' ORDER BY s.full_name ASC");
                    if ($result && $result->num_rows > 0):
                        while ($student = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?action=delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="4" class="text-center">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
