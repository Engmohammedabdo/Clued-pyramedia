<?php
/**
 * Posts List View
 */

// Get filter parameters
$status = $_GET['status'] ?? '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = ADMIN_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Build query
$where = ['1=1'];
$params = [];

if ($status) {
    $where[] = 'p.status = :status';
    $params['status'] = $status;
}

if ($category > 0) {
    $where[] = 'p.category_id = :category';
    $params['category'] = $category;
}

if ($search) {
    $where[] = '(p.title_en LIKE :search OR p.title_ar LIKE :search OR p.content_en LIKE :search)';
    $params['search'] = "%{$search}%";
}

// Only show own posts for authors
if ($user['role'] === 'author') {
    $where[] = 'p.author_id = :author_id';
    $params['author_id'] = $user['id'];
}

$whereClause = implode(' AND ', $where);

// Get total count
$stmt = $db->getConnection()->prepare("
    SELECT COUNT(*) as total
    FROM blog_posts p
    WHERE {$whereClause}
");
$stmt->execute($params);
$totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get posts
$stmt = $db->getConnection()->prepare("
    SELECT
        p.id, p.title_en, p.status, p.views, p.created_at, p.published_at,
        u.full_name as author_name,
        c.name_en as category_name
    FROM blog_posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN blog_categories c ON p.category_id = c.id
    WHERE {$whereClause}
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $value) {
    $stmt->bindValue(":{$key}", $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Top Bar -->
<div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-file-alt text-orange-500 mr-3"></i>
                Posts
            </h1>
            <p class="text-gray-600 mt-1"><?= number_format($totalPosts) ?> total posts</p>
        </div>
        <a href="posts.php?action=create" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
            <i class="fas fa-plus mr-2"></i>
            New Post
        </a>
    </div>
</div>

<!-- Content -->
<div class="p-6">
    <?php if ($error): ?>
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium"><?= e($error) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert-success mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium"><?= e($success) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert-success mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium">Post deleted successfully!</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" action="posts.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <input
                    type="text"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="Search posts..."
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="scheduled" <?= $status === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category === $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name_en']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <?php if (empty($posts)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">No posts found</p>
            <a href="posts.php?action=create" class="px-6 py-2 gradient-bg text-white rounded-lg font-semibold inline-block">
                Create Your First Post
            </a>
        </div>
        <?php else: ?>
        <form method="POST" action="posts.php?action=bulk" id="bulkForm">
            <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

            <!-- Bulk Actions -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-4">
                <select name="bulk_action" class="px-4 py-2 border-2 border-gray-200 rounded-lg">
                    <option value="">Bulk Actions</option>
                    <option value="publish">Publish</option>
                    <option value="draft">Move to Draft</option>
                    <option value="delete">Delete</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-800">
                    Apply
                </button>
                <span id="selectedCount" class="text-sm text-gray-600"></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="w-4 h-4">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($posts as $post): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="post_ids[]" value="<?= $post['id'] ?>" class="post-checkbox w-4 h-4">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">
                                    <?= e(truncate($post['title_en'], 60)) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= e($post['author_name'] ?? 'Unknown') ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold bg-purple-100 text-purple-700 rounded-full">
                                    <?= e($post['category_name'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusColors = [
                                    'published' => 'bg-green-100 text-green-700',
                                    'draft' => 'bg-yellow-100 text-yellow-700',
                                    'scheduled' => 'bg-blue-100 text-blue-700',
                                    'archived' => 'bg-gray-100 text-gray-700'
                                ];
                                $color = $statusColors[$post['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-3 py-1 text-xs font-semibold <?= $color ?> rounded-full">
                                    <?= $post['status'] === 'scheduled' ? '<i class="far fa-clock mr-1"></i>' : '' ?>
                                    <?= ucfirst($post['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <i class="far fa-eye text-gray-400 mr-1"></i>
                                <?= number_format($post['views']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= timeAgo($post['created_at']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="posts.php?action=edit&id=<?= $post['id'] ?>"
                                       class="text-blue-600 hover:text-blue-800"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($post['status'] === 'published'): ?>
                                    <a href="/ccode/blog-post.html?slug=<?= $post['slug_en'] ?>"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-800"
                                       title="View Live">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-gray-400" title="Not published">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                    <?php endif; ?>
                                    <form method="POST" action="posts.php?action=delete&id=<?= $post['id'] ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Showing <?= number_format($offset + 1) ?> to <?= number_format(min($offset + $perPage, $totalPosts)) ?> of <?= number_format($totalPosts) ?> posts
            </div>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&status=<?= e($status) ?>&category=<?= $category ?>&search=<?= e($search) ?>"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Previous
                </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?= $i ?>&status=<?= e($status) ?>&category=<?= $category ?>&search=<?= e($search) ?>"
                   class="px-4 py-2 <?= $i === $page ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> rounded-lg">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&status=<?= e($status) ?>&category=<?= $category ?>&search=<?= e($search) ?>"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Next
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.post-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.post-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.post-checkbox:checked').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) {
        countEl.textContent = count > 0 ? `${count} post(s) selected` : '';
    }
}

// Confirm bulk delete
document.getElementById('bulkForm')?.addEventListener('submit', function(e) {
    const action = this.querySelector('[name="bulk_action"]').value;
    if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected posts?')) {
            e.preventDefault();
        }
    }
});
</script>
