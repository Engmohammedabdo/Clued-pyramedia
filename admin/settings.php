<?php
/**
 * PYRAMEDIA Admin - Settings
 * Site configuration and preferences
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireAdmin(); // Only admins can access settings

$error = '';
$success = '';

// Settings file path
$settingsFile = __DIR__ . '/../config/settings.json';

// Load current settings
$defaultSettings = [
    'site_name' => 'PYRAMEDIA',
    'site_tagline' => 'Marketing & Media Solutions',
    'site_url' => 'https://pyramedia.ae',
    'admin_email' => 'info@pyramedia.ae',
    'posts_per_page' => 12,
    'default_language' => 'en',
    'allow_comments' => false,
    'tinymce_api_key' => TINYMCE_API_KEY,
    'upload_max_size' => 5,
    'default_category' => 1,
    'timezone' => 'Asia/Dubai',
    'date_format' => 'M j, Y',
    'time_format' => 'g:i A',
    'maintenance_mode' => false
];

if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true) ?: $defaultSettings;
} else {
    $settings = $defaultSettings;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $tab = $_POST['tab'] ?? 'general';

    switch ($tab) {
        case 'general':
            $settings['site_name'] = trim($_POST['site_name']);
            $settings['site_tagline'] = trim($_POST['site_tagline']);
            $settings['site_url'] = trim($_POST['site_url']);
            $settings['admin_email'] = trim($_POST['admin_email']);
            $settings['timezone'] = $_POST['timezone'];
            break;

        case 'blog':
            $settings['posts_per_page'] = (int)$_POST['posts_per_page'];
            $settings['default_language'] = $_POST['default_language'];
            $settings['default_category'] = (int)$_POST['default_category'];
            $settings['allow_comments'] = isset($_POST['allow_comments']);
            break;

        case 'editor':
            $settings['tinymce_api_key'] = trim($_POST['tinymce_api_key']);
            break;

        case 'uploads':
            $settings['upload_max_size'] = (int)$_POST['upload_max_size'];
            break;

        case 'advanced':
            $settings['maintenance_mode'] = isset($_POST['maintenance_mode']);
            $settings['date_format'] = trim($_POST['date_format']);
            $settings['time_format'] = trim($_POST['time_format']);
            break;
    }

    // Save settings
    if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT))) {
        $success = 'Settings saved successfully!';
    } else {
        $error = 'Failed to save settings. Check file permissions.';
    }
}

// Get categories for dropdown
$db = getDB();
$stmt = $db->getConnection()->query("SELECT id, name_en FROM blog_categories ORDER BY name_en");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-cog text-orange-500 mr-3"></i>
            Settings
        </h1>
        <p class="text-gray-600 mt-1">Configure your site settings and preferences</p>
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

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" id="settingsTabs">
                    <button onclick="showTab('general')" class="tab-button active py-4 px-2 border-b-2 border-orange-500 font-semibold text-gray-800">
                        <i class="fas fa-globe mr-2"></i>General
                    </button>
                    <button onclick="showTab('blog')" class="tab-button py-4 px-2 border-b-2 border-transparent font-semibold text-gray-600 hover:text-gray-800">
                        <i class="fas fa-blog mr-2"></i>Blog
                    </button>
                    <button onclick="showTab('editor')" class="tab-button py-4 px-2 border-b-2 border-transparent font-semibold text-gray-600 hover:text-gray-800">
                        <i class="fas fa-edit mr-2"></i>Editor
                    </button>
                    <button onclick="showTab('uploads')" class="tab-button py-4 px-2 border-b-2 border-transparent font-semibold text-gray-600 hover:text-gray-800">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>Uploads
                    </button>
                    <button onclick="showTab('advanced')" class="tab-button py-4 px-2 border-b-2 border-transparent font-semibold text-gray-600 hover:text-gray-800">
                        <i class="fas fa-cogs mr-2"></i>Advanced
                    </button>
                </nav>
            </div>

            <!-- General Tab -->
            <div id="tab-general" class="tab-content p-8">
                <h3 class="text-2xl font-bold mb-6">General Settings</h3>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                    <input type="hidden" name="tab" value="general">

                    <div class="space-y-6 max-w-2xl">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Site Name</label>
                            <input type="text" name="site_name" value="<?= e($settings['site_name']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Site Tagline</label>
                            <input type="text" name="site_tagline" value="<?= e($settings['site_tagline']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Site URL</label>
                            <input type="url" name="site_url" value="<?= e($settings['site_url']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Admin Email</label>
                            <input type="email" name="admin_email" value="<?= e($settings['admin_email']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Timezone</label>
                            <select name="timezone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                                <option value="Asia/Dubai" <?= $settings['timezone'] === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (UTC+4)</option>
                                <option value="Asia/Riyadh" <?= $settings['timezone'] === 'Asia/Riyadh' ? 'selected' : '' ?>>Asia/Riyadh (UTC+3)</option>
                                <option value="UTC" <?= $settings['timezone'] === 'UTC' ? 'selected' : '' ?>>UTC</option>
                            </select>
                        </div>

                        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Blog Tab -->
            <div id="tab-blog" class="tab-content p-8 hidden">
                <h3 class="text-2xl font-bold mb-6">Blog Settings</h3>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                    <input type="hidden" name="tab" value="blog">

                    <div class="space-y-6 max-w-2xl">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Posts Per Page</label>
                            <input type="number" name="posts_per_page" min="1" max="100"
                                   value="<?= $settings['posts_per_page'] ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Default Language</label>
                            <select name="default_language" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                                <option value="en" <?= $settings['default_language'] === 'en' ? 'selected' : '' ?>>English</option>
                                <option value="ar" <?= $settings['default_language'] === 'ar' ? 'selected' : '' ?>>Arabic</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Default Category</label>
                            <select name="default_category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $settings['default_category'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name_en']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_comments"
                                       <?= $settings['allow_comments'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-orange-500 rounded">
                                <span class="ml-3 text-sm font-semibold">Allow Comments (Future Feature)</span>
                            </label>
                        </div>

                        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Editor Tab -->
            <div id="tab-editor" class="tab-content p-8 hidden">
                <h3 class="text-2xl font-bold mb-6">Editor Settings</h3>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                    <input type="hidden" name="tab" value="editor">

                    <div class="space-y-6 max-w-2xl">
                        <div>
                            <label class="block text-sm font-semibold mb-2">TinyMCE API Key</label>
                            <input type="text" name="tinymce_api_key"
                                   value="<?= e($settings['tinymce_api_key']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none font-mono">
                            <p class="text-sm text-gray-600 mt-2">
                                Get your free API key from <a href="https://www.tiny.cloud/" target="_blank" class="text-orange-500 hover:underline">TinyMCE Cloud</a>
                            </p>
                        </div>

                        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Uploads Tab -->
            <div id="tab-uploads" class="tab-content p-8 hidden">
                <h3 class="text-2xl font-bold mb-6">Upload Settings</h3>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                    <input type="hidden" name="tab" value="uploads">

                    <div class="space-y-6 max-w-2xl">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Maximum Upload Size (MB)</label>
                            <input type="number" name="upload_max_size" min="1" max="50"
                                   value="<?= $settings['upload_max_size'] ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <p class="text-sm text-gray-600 mt-2">
                                Server max: <?= formatFileSize(UPLOAD_MAX_SIZE) ?>
                            </p>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                Allowed file types: JPEG, PNG, GIF, WebP
                            </p>
                        </div>

                        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Advanced Tab -->
            <div id="tab-advanced" class="tab-content p-8 hidden">
                <h3 class="text-2xl font-bold mb-6">Advanced Settings</h3>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">
                    <input type="hidden" name="tab" value="advanced">

                    <div class="space-y-6 max-w-2xl">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Date Format</label>
                            <input type="text" name="date_format"
                                   value="<?= e($settings['date_format']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <p class="text-sm text-gray-600 mt-2">
                                Example: <?= date($settings['date_format']) ?>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Time Format</label>
                            <input type="text" name="time_format"
                                   value="<?= e($settings['time_format']) ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <p class="text-sm text-gray-600 mt-2">
                                Example: <?= date($settings['time_format']) ?>
                            </p>
                        </div>

                        <hr class="my-6">

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="maintenance_mode"
                                       <?= $settings['maintenance_mode'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-orange-500 rounded">
                                <span class="ml-3 text-sm font-semibold text-red-600">
                                    Enable Maintenance Mode (Coming Soon)
                                </span>
                            </label>
                            <p class="text-sm text-gray-600 mt-2 ml-8">
                                When enabled, site will show a "under maintenance" page to visitors
                            </p>
                        </div>

                        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Info -->
        <div class="mt-6 bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold mb-4">System Information</h3>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">PHP Version:</span>
                    <span class="font-semibold ml-2"><?= PHP_VERSION ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Server:</span>
                    <span class="font-semibold ml-2"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></span>
                </div>
                <div>
                    <span class="text-gray-600">MySQL Version:</span>
                    <span class="font-semibold ml-2"><?= $db->getConnection()->query('SELECT VERSION()')->fetchColumn() ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-orange-500', 'text-gray-800');
        btn.classList.add('border-transparent', 'text-gray-600');
    });

    // Show selected tab
    document.getElementById('tab-' + tabName).classList.remove('hidden');

    // Activate button
    event.target.classList.add('border-orange-500', 'text-gray-800');
    event.target.classList.remove('border-transparent', 'text-gray-600');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
