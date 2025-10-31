<?php
/**
 * PYRAMEDIA Admin - Tags Management
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireLogin();

$db = getDB();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$tagId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    switch ($action) {
        case 'create':
        case 'edit':
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_ar' => trim($_POST['name_ar'] ?? $_POST['name_en']),
                'slug' => generateSlug($_POST['slug'] ?? $_POST['name_en'])
            ];

            if ($tagId > 0) {
                $db->update('blog_tags', $data, 'id = :id', ['id' => $tagId]);
                $success = 'Tag updated successfully!';
            } else {
                $db->insert('blog_tags', $data);
                $success = 'Tag created successfully!';
            }
            $action = 'list';
            break;

        case 'delete':
            if ($auth->isAdmin()) {
                $db->delete('blog_post_tags', 'tag_id = :id', ['id' => $tagId]);
                $db->delete('blog_tags', 'id = :id', ['id' => $tagId]);
                $success = 'Tag deleted successfully!';
                $action = 'list';
            }
            break;
    }
}

// Get tag for edit
$tag = null;
if ($action === 'edit' && $tagId > 0) {
    $stmt = $db->getConnection()->prepare("SELECT * FROM blog_tags WHERE id = :id");
    $stmt->execute(['id' => $tagId]);
    $tag = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all tags
$stmt = $db->getConnection()->query("
    SELECT t.*, COUNT(pt.post_id) as posts_count
    FROM blog_tags t
    LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id
    GROUP BY t.id
    ORDER BY t.name_en
");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Tags';
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
                    <i class="fas fa-tags text-orange-500 mr-3"></i>
                    Tags
                </h1>
                <p class="text-gray-600 mt-1"><?= count($tags) ?> tags</p>
            </div>
            <button onclick="showForm()" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-plus mr-2"></i>
                New Tag
            </button>
        </div>
    </div>

    <div class="p-6">
        <?php if ($success): ?>
        <div class="alert-success mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <p class="text-green-700"><?= e($success) ?></p>
        </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="bg-white rounded-xl shadow-md p-6" id="tagForm" style="<?= $action === 'list' ? 'display:none' : '' ?>">
                <h3 class="text-xl font-bold mb-6"><?= $action === 'edit' ? 'Edit Tag' : 'Add New Tag' ?></h3>
                <form method="POST" action="tags.php?action=<?= $action === 'edit' ? 'edit&id=' . $tagId : 'create' ?>">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Name (English) *</label>
                        <input type="text" name="name_en" required value="<?= e($tag['name_en'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Name (Arabic)</label>
                        <input type="text" name="name_ar" value="<?= e($tag['name_ar'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none" dir="rtl">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Slug</label>
                        <input type="text" name="slug" value="<?= e($tag['slug'] ?? '') ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
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
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tag</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Posts</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($tags as $t): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold"><?= e($t['name_en']) ?></div>
                                    <?php if ($t['name_ar']): ?>
                                    <div class="text-sm text-gray-500"><?= e($t['name_ar']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?= e($t['slug']) ?></td>
                                <td class="px-6 py-4 text-sm"><?= number_format($t['posts_count']) ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-3">
                                        <a href="tags.php?action=edit&id=<?= $t['id'] ?>" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($auth->isAdmin()): ?>
                                        <form method="POST" action="tags.php?action=delete&id=<?= $t['id'] ?>" class="inline"
                                              onsubmit="return confirm('Delete this tag?')">
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
    document.getElementById('tagForm').style.display = 'block';
}
function hideForm() {
    window.location.href = 'tags.php';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
