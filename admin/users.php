<?php
/**
 * PYRAMEDIA Admin - User Management
 * Manage authors and administrators
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireAdmin(); // Only admins can manage users

$db = getDB();
$currentUser = $auth->getCurrentUser();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    switch ($action) {
        case 'create':
        case 'edit':
            $email = trim($_POST['email']);
            $fullName = trim($_POST['full_name']);
            $role = $_POST['role'];
            $status = $_POST['status'];

            // Validate
            if (empty($email) || empty($fullName) || empty($role)) {
                $error = 'Please fill in all required fields';
                break;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address';
                break;
            }

            // Check if email exists (for create or different user)
            $stmt = $db->getConnection()->prepare("
                SELECT id FROM users WHERE email = :email AND id != :id
            ");
            $stmt->execute(['email' => $email, 'id' => $userId]);
            if ($stmt->fetch()) {
                $error = 'Email already exists';
                break;
            }

            $data = [
                'full_name' => $fullName,
                'email' => $email,
                'role' => $role,
                'status' => $status,
                'bio' => trim($_POST['bio'] ?? ''),
                'avatar' => trim($_POST['avatar'] ?? '')
            ];

            if ($action === 'create') {
                // Create new user
                $password = $_POST['password'] ?? '';
                if (strlen($password) < 8) {
                    $error = 'Password must be at least 8 characters';
                    break;
                }

                $data['password'] = password_hash($password, PASSWORD_ARGON2ID);
                $data['created_at'] = date('Y-m-d H:i:s');

                $newUserId = $db->insert('users', $data);
                $success = 'User created successfully!';
                $action = 'list';
            } else {
                // Update existing user
                $data['updated_at'] = date('Y-m-d H:i:s');

                // Change password if provided
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 8) {
                        $error = 'Password must be at least 8 characters';
                        break;
                    }
                    $data['password'] = password_hash($_POST['password'], PASSWORD_ARGON2ID);
                }

                $db->update('users', $data, 'id = :id', ['id' => $userId]);
                $success = 'User updated successfully!';
                $action = 'list';
            }
            break;

        case 'delete':
            if ($userId === $currentUser['id']) {
                $error = 'You cannot delete yourself!';
                break;
            }

            // Check if user has posts
            $stmt = $db->getConnection()->prepare("
                SELECT COUNT(*) as count FROM blog_posts WHERE author_id = :id
            ");
            $stmt->execute(['id' => $userId]);
            $postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($postCount > 0) {
                $error = "Cannot delete user with {$postCount} posts. Reassign posts first.";
                break;
            }

            $db->delete('users', 'id = :id', ['id' => $userId]);
            $success = 'User deleted successfully!';
            $action = 'list';
            break;
    }
}

// Get user for edit
$user = null;
if ($action === 'edit' && $userId > 0) {
    $stmt = $db->getConnection()->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all users
$stmt = $db->getConnection()->query("
    SELECT u.*, COUNT(p.id) as posts_count
    FROM users u
    LEFT JOIN blog_posts p ON u.id = p.author_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'User Management';
include __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-users text-orange-500 mr-3"></i>
                    User Management
                </h1>
                <p class="text-gray-600 mt-1"><?= count($users) ?> users</p>
            </div>
            <button onclick="showCreateForm()" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-user-plus mr-2"></i>
                Add New User
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <p class="text-red-700"><?= e($error) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert-success mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <p class="text-green-700"><?= e($success) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($action === 'create' || $action === 'edit'): ?>
        <!-- User Form -->
        <div class="bg-white rounded-xl shadow-md p-8 max-w-2xl mx-auto">
            <h3 class="text-2xl font-bold mb-6">
                <?= $action === 'edit' ? 'Edit User' : 'Create New User' ?>
            </h3>

            <form method="POST" action="users.php?action=<?= $action ?><?= $action === 'edit' ? '&id=' . $userId : '' ?>">
                <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" required
                               value="<?= e($user['full_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               value="<?= e($user['email'] ?? '') ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    </div>

                    <!-- Password -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            Password <?= $action === 'create' ? '<span class="text-red-500">*</span>' : '(leave blank to keep current)' ?>
                        </label>
                        <input type="password" name="password"
                               <?= $action === 'create' ? 'required' : '' ?>
                               minlength="8"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                               placeholder="<?= $action === 'edit' ? 'Leave blank to keep current password' : 'Minimum 8 characters' ?>">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <option value="author" <?= ($user['role'] ?? '') === 'author' ? 'selected' : '' ?>>Author</option>
                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Avatar URL -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            Avatar URL
                        </label>
                        <input type="url" name="avatar"
                               value="<?= e($user['avatar'] ?? '') ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                               placeholder="https://example.com/avatar.jpg">
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            Bio
                        </label>
                        <textarea name="bio" rows="4"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                                  placeholder="Short bio about the user..."><?= e($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" class="flex-1 gradient-bg text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        <i class="fas fa-save mr-2"></i>
                        <?= $action === 'edit' ? 'Update User' : 'Create User' ?>
                    </button>
                    <a href="users.php" class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        <?php else: ?>
        <!-- Users List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Posts</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="<?= e($u['avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($u['full_name'])) ?>"
                                     alt="<?= e($u['full_name']) ?>"
                                     class="w-10 h-10 rounded-full object-cover mr-3">
                                <div>
                                    <div class="font-semibold text-gray-800"><?= e($u['full_name']) ?></div>
                                    <?php if ($u['id'] === $currentUser['id']): ?>
                                    <div class="text-xs text-orange-500 font-semibold">You</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= e($u['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?> rounded-full">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm"><?= number_format($u['posts_count']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold <?= $u['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?> rounded-full">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?= $u['last_login'] ? timeAgo($u['last_login']) : 'Never' ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-3">
                                <a href="users.php?action=edit&id=<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($u['id'] !== $currentUser['id']): ?>
                                <form method="POST" action="users.php?action=delete&id=<?= $u['id'] ?>" class="inline"
                                      onsubmit="return confirm('Delete this user? This action cannot be undone.')">
                                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showCreateForm() {
    window.location.href = 'users.php?action=create';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
