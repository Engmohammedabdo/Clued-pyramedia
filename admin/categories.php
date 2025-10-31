<?php
/**
 * PYRAMEDIA Admin - Categories Management
 * Simple CRUD for blog categories
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireLogin();

$db = getDB();
$user = $auth->getCurrentUser();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    switch ($action) {
        case 'create':
        case 'edit':
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_ar' => trim($_POST['name_ar'] ?? $_POST['name_en']),
                'slug' => generateSlug($_POST['slug'] ?? $_POST['name_en']),
                'color' => trim($_POST['color'] ?? '#FF6B35')
            ];

            if ($categoryId > 0) {
                $db->update('blog_categories', $data, 'id = :id', ['id' => $categoryId]);
                $success = 'Category updated successfully!';
            } else {
                $db->insert('blog_categories', $data);
                $success = 'Category created successfully!';
            }
            $action = 'list';
            break;

        case 'delete':
            if ($auth->isAdmin()) {
                $db->delete('blog_categories', 'id = :id', ['id' => $categoryId]);
                $success = 'Category deleted successfully!';
                $action = 'list';
            }
            break;
    }
}

// Get category for edit
$category = null;
if ($action === 'edit' && $categoryId > 0) {
    $stmt = $db->getConnection()->prepare("SELECT * FROM blog_categories WHERE id = :id");
    $stmt->execute(['id' => $categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all categories
$stmt = $db->getConnection()->query("
    SELECT c.*, COUNT(p.id) as posts_count
    FROM blog_categories c
    LEFT JOIN blog_posts p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name_en
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Categories';
include __DIR__ . '/includes/header.php';

function generateSlug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    return preg_replace('/[\s-]+/', '-', trim($text, '-'));
}
?>

<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-folder text-orange-500 mr-3"></i>
                    Categories
                </h1>
                <p class="text-gray-600 mt-1"><?= count($categories) ?> categories</p>
            </div>
            <button onclick="showForm()" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-plus mr-2"></i>
                New Category
            </button>
        </div>
    </div>

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

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="bg-white rounded-xl shadow-md p-6" id="categoryForm" style="<?= $action === 'list' ? 'display:none' : '' ?>">
                <h3 class="text-xl font-bold mb-6"><?= $action === 'edit' ? 'Edit Category' : 'Add New Category' ?></h3>
                <form method="POST" action="categories.php?action=<?= $action === 'edit' ? 'edit&id=' . $categoryId : 'create' ?>">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Name (English) *</label>
                        <input type="text" name="name_en" required value="<?= e($category['name_en'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Name (Arabic)</label>
                        <input type="text" name="name_ar" value="<?= e($category['name_ar'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none" dir="rtl">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Slug</label>
                        <input type="text" name="slug" value="<?= e($category['slug'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Color</label>
                        <input type="color" name="color" value="<?= e($category['color'] ?? '#FF6B35') ?>"
                               class="w-full h-12 border-2 border-gray-200 rounded-lg">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 gradient-bg text-white py-2 rounded-lg font-semibold">
                            <?= $action === 'edit' ? 'Update' : 'Create' ?>
                        </button>
                        <button type="button" onclick="hideForm()" class="px-6 bg-gray-200 text-gray-700 py-2 rounded-lg font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Posts</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full mr-3" style="background-color: <?= e($cat['color']) ?>"></div>
                                        <div>
                                            <div class="font-semibold"><?= e($cat['name_en']) ?></div>
                                            <?php if ($cat['name_ar']): ?>
                                            <div class="text-sm text-gray-500"><?= e($cat['name_ar']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?= e($cat['slug']) ?></td>
                                <td class="px-6 py-4 text-sm"><?= number_format($cat['posts_count']) ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-3">
                                        <a href="categories.php?action=edit&id=<?= $cat['id'] ?>" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($auth->isAdmin()): ?>
                                        <form method="POST" action="categories.php?action=delete&id=<?= $cat['id'] ?>" class="inline"
                                              onsubmit="return confirm('Delete this category?')">
                                            <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800">
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
            </div>
        </div>
    </div>
</div>

<script>
function showForm() {
    document.getElementById('categoryForm').style.display = 'block';
}
function hideForm() {
    window.location.href = 'categories.php';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
