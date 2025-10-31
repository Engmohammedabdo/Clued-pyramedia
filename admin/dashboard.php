<?php
/**
 * PYRAMEDIA Admin - Dashboard Home
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireLogin();

$db = getDB();
$user = $auth->getCurrentUser();

// Get statistics
try {
    // Total posts
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM blog_posts");
    $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Published posts
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published'");
    $publishedPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Draft posts
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'draft'");
    $draftPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total categories
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM blog_categories");
    $totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total tags
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM blog_tags");
    $totalTags = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total views
    $stmt = $db->getConnection()->query("SELECT SUM(views) as total FROM blog_posts");
    $totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Recent posts
    $stmt = $db->getConnection()->prepare("
        SELECT
            p.id, p.title_en, p.status, p.views, p.created_at,
            u.full_name as author_name,
            c.name_en as category_name
        FROM blog_posts p
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN blog_categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $totalPosts = $publishedPosts = $draftPosts = 0;
    $totalCategories = $totalTags = $totalViews = 0;
    $recentPosts = [];
}

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<!-- Main Content -->
<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-tachometer-alt text-orange-500 mr-3"></i>
                    Dashboard
                </h1>
                <p class="text-gray-600 mt-1">Welcome back, <?= e($user['name']) ?>!</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="location.href='../blog.html'" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View Site
                </button>
                <button onclick="location.href='posts.php?action=create'" class="px-6 py-2 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    New Post
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="p-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Posts -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase">Total Posts</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalPosts) ?></p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-2xl text-blue-500"></i>
                    </div>
                </div>
            </div>

            <!-- Published Posts -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase">Published</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($publishedPosts) ?></p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-500"></i>
                    </div>
                </div>
            </div>

            <!-- Draft Posts -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase">Drafts</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($draftPosts) ?></p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-edit text-2xl text-yellow-500"></i>
                    </div>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase">Total Views</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalViews) ?></p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-eye text-2xl text-orange-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Categories -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-folder text-purple-500 mr-2"></i>
                        Categories
                    </h3>
                    <a href="categories.php" class="text-sm text-orange-500 hover:text-orange-600 font-semibold">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <p class="text-4xl font-bold text-gray-800"><?= number_format($totalCategories) ?></p>
                <p class="text-sm text-gray-600 mt-2">Active categories</p>
            </div>

            <!-- Tags -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-tags text-indigo-500 mr-2"></i>
                        Tags
                    </h3>
                    <a href="tags.php" class="text-sm text-orange-500 hover:text-orange-600 font-semibold">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <p class="text-4xl font-bold text-gray-800"><?= number_format($totalTags) ?></p>
                <p class="text-sm text-gray-600 mt-2">Available tags</p>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    Recent Posts
                </h3>
                <a href="posts.php" class="text-sm text-orange-500 hover:text-orange-600 font-semibold">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <?php if (empty($recentPosts)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No posts yet</p>
                <button onclick="location.href='posts.php?action=create'" class="mt-4 px-6 py-2 gradient-bg text-white rounded-lg font-semibold">
                    Create Your First Post
                </button>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Views</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recentPosts as $post): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">
                                    <?= e(truncate($post['title_en'], 50)) ?>
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
                                    'archived' => 'bg-gray-100 text-gray-700'
                                ];
                                $color = $statusColors[$post['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-3 py-1 text-xs font-semibold <?= $color ?> rounded-full">
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
                                <div class="flex items-center gap-2">
                                    <a href="posts.php?action=edit&id=<?= $post['id'] ?>"
                                       class="text-blue-600 hover:text-blue-800"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($post['status'] === 'published'): ?>
                                    <a href="/blog-post.html?slug=<?= $post['slug_en'] ?>"
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
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="posts.php?action=create" class="block bg-gradient-to-r from-orange-500 to-red-500 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-plus-circle text-3xl mb-3"></i>
                <h4 class="text-xl font-bold">Create New Post</h4>
                <p class="text-sm opacity-90 mt-2">Write and publish a new blog post</p>
            </a>

            <a href="categories.php" class="block bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-folder-plus text-3xl mb-3"></i>
                <h4 class="text-xl font-bold">Manage Categories</h4>
                <p class="text-sm opacity-90 mt-2">Organize your content categories</p>
            </a>

            <a href="settings.php" class="block bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-cog text-3xl mb-3"></i>
                <h4 class="text-xl font-bold">Settings</h4>
                <p class="text-sm opacity-90 mt-2">Configure your admin preferences</p>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
