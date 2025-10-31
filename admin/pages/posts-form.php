<?php
/**
 * Posts Create/Edit Form
 */

$isEdit = ($action === 'edit');
$post = $post ?? [];
?>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/<?= TINYMCE_API_KEY ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Top Bar -->
<div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?> text-orange-500 mr-3"></i>
                <?= $isEdit ? 'Edit Post' : 'Create New Post' ?>
            </h1>
            <?php if ($isEdit): ?>
            <p class="text-gray-600 mt-1">ID: <?= $postId ?> | Created: <?= date('M j, Y', strtotime($post['created_at'])) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <a href="posts.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Posts
            </a>
            <?php if ($isEdit && $post['status'] === 'published'): ?>
            <a href="../blog-post.html?slug=<?= $post['slug_en'] ?>" target="_blank" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-external-link-alt mr-2"></i>
                View Live
            </a>
            <?php endif; ?>
        </div>
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

    <form method="POST" action="posts.php?action=<?= $action ?><?= $isEdit ? '&id=' . $postId : '' ?>" id="postForm" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (Left Column) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- English Content -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-flag text-blue-500 mr-2"></i>
                        English Content
                    </h3>

                    <!-- Title EN -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Title (English) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title_en"
                            required
                            value="<?= e($post['title_en'] ?? '') ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="Enter post title..."
                        >
                    </div>

                    <!-- Slug EN -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            URL Slug (English)
                        </label>
                        <input
                            type="text"
                            name="slug_en"
                            value="<?= e($post['slug_en'] ?? '') ?>"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="auto-generated-from-title"
                        >
                        <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate from title</p>
                    </div>

                    <!-- Content EN -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Content (English) <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="content_en"
                            id="content_en"
                            class="tinymce"
                            rows="15"
                        ><?= $post['content_en'] ?? '' ?></textarea>
                    </div>

                    <!-- Excerpt EN -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Excerpt (English)
                        </label>
                        <textarea
                            name="excerpt_en"
                            rows="3"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="Short summary..."
                        ><?= e($post['excerpt_en'] ?? '') ?></textarea>
                    </div>

                    <!-- Meta Description EN -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Meta Description (English)
                        </label>
                        <textarea
                            name="meta_description_en"
                            rows="2"
                            maxlength="160"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="SEO description (max 160 characters)..."
                        ><?= e($post['meta_description_en'] ?? '') ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">For search engines (160 characters max)</p>
                    </div>
                </div>

                <!-- Arabic Content -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-flag text-green-500 mr-2"></i>
                        Arabic Content (محتوى عربي)
                    </h3>

                    <!-- Title AR -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Title (Arabic) / العنوان
                        </label>
                        <input
                            type="text"
                            name="title_ar"
                            value="<?= e($post['title_ar'] ?? '') ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="أدخل العنوان..."
                            dir="rtl"
                        >
                    </div>

                    <!-- Slug AR -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            URL Slug (Arabic)
                        </label>
                        <input
                            type="text"
                            name="slug_ar"
                            value="<?= e($post['slug_ar'] ?? '') ?>"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="arabic-slug"
                        >
                    </div>

                    <!-- Content AR -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Content (Arabic) / المحتوى
                        </label>
                        <textarea
                            name="content_ar"
                            id="content_ar"
                            class="tinymce"
                            rows="15"
                        ><?= $post['content_ar'] ?? '' ?></textarea>
                    </div>

                    <!-- Excerpt AR -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Excerpt (Arabic) / الملخص
                        </label>
                        <textarea
                            name="excerpt_ar"
                            rows="3"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="ملخص قصير..."
                            dir="rtl"
                        ><?= e($post['excerpt_ar'] ?? '') ?></textarea>
                    </div>

                    <!-- Meta Description AR -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Meta Description (Arabic)
                        </label>
                        <textarea
                            name="meta_description_ar"
                            rows="2"
                            maxlength="160"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="وصف SEO (بحد أقصى 160 حرف)..."
                            dir="rtl"
                        ><?= e($post['meta_description_ar'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- SEO & Advanced -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-search text-purple-500 mr-2"></i>
                        SEO & Advanced Settings
                    </h3>

                    <!-- Meta Keywords -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Meta Keywords
                        </label>
                        <input
                            type="text"
                            name="meta_keywords"
                            value="<?= e($post['meta_keywords'] ?? '') ?>"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="keyword1, keyword2, keyword3"
                        >
                        <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                    </div>

                    <!-- Reading Time -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Reading Time (minutes)
                        </label>
                        <input
                            type="number"
                            name="read_time"
                            value="<?= $post['read_time'] ?? '' ?>"
                            min="1"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none"
                            placeholder="Auto-calculated"
                        >
                        <p class="text-xs text-gray-500 mt-1">Leave blank to auto-calculate from content</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right Column) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Publish Box -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-paper-plane text-orange-500 mr-2"></i>
                        Publish
                    </h3>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" id="postStatus" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                            <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publish Now</option>
                            <option value="scheduled" <?= ($post['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Schedule for Later</option>
                            <option value="archived" <?= ($post['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>

                    <!-- Schedule Date/Time (shown when scheduled) -->
                    <div id="scheduleSection" class="mb-4 <?= ($post['status'] ?? '') !== 'scheduled' ? 'hidden' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="far fa-clock mr-1"></i>
                            Publish Date & Time
                        </label>
                        <?php
                        $scheduledFor = $post['scheduled_for'] ?? '';
                        if ($scheduledFor) {
                            $scheduledDate = date('Y-m-d\TH:i', strtotime($scheduledFor));
                        } else {
                            // Default to 1 hour from now
                            $scheduledDate = date('Y-m-d\TH:i', strtotime('+1 hour'));
                        }
                        ?>
                        <input
                            type="datetime-local"
                            name="scheduled_for"
                            id="scheduledFor"
                            value="<?= $scheduledDate ?>"
                            min="<?= date('Y-m-d\TH:i') ?>"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none text-sm"
                        >
                        <p class="text-xs text-gray-500 mt-1">Post will be automatically published at this time</p>
                    </div>

                    <!-- Auto-save Indicator -->
                    <div class="mb-4 min-h-[20px]">
                        <p id="autoSaveIndicator" class="text-xs text-gray-500 text-center">
                            <i class="fas fa-circle-notch fa-spin mr-1"></i>
                            Auto-save enabled
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <button type="submit" name="submit_action" value="save" class="w-full px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i>
                            <?= $isEdit ? 'Update Post' : 'Create Post' ?>
                        </button>
                        <a href="posts.php" class="block w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold text-center hover:bg-gray-300 transition-colors">
                            Cancel
                        </a>
                    </div>

                    <?php if ($isEdit): ?>
                    <hr class="my-4">
                    <?php if ($post['status'] === 'scheduled' && $post['scheduled_for']): ?>
                    <p class="text-xs text-gray-600 mb-2">
                        <i class="far fa-clock mr-1"></i>
                        Scheduled for: <?= date('M j, Y \a\t g:i A', strtotime($post['scheduled_for'])) ?>
                    </p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-600">
                        Last updated: <?= timeAgo($post['updated_at']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Category -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-folder text-purple-500 mr-2"></i>
                        Category <span class="text-red-500 ml-1">*</span>
                    </h3>
                    <select name="category_id" required class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name_en']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="categories.php" target="_blank" class="text-xs text-orange-500 hover:text-orange-600 mt-2 inline-block">
                        <i class="fas fa-plus mr-1"></i>
                        Add New Category
                    </a>
                </div>

                <!-- Tags -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-tags text-indigo-500 mr-2"></i>
                        Tags
                    </h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <?php
                        $selectedTags = array_column($post['tags'] ?? [], 'id');
                        foreach ($tags as $tag):
                        ?>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="<?= $tag['id'] ?>"
                                <?= in_array($tag['id'], $selectedTags) ? 'checked' : '' ?>
                                class="w-4 h-4 text-orange-500"
                            >
                            <span class="ml-2 text-sm text-gray-700"><?= e($tag['name_en']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <a href="tags.php" target="_blank" class="text-xs text-orange-500 hover:text-orange-600 mt-3 inline-block">
                        <i class="fas fa-plus mr-1"></i>
                        Add New Tag
                    </a>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        Featured Image
                    </h3>

                    <div id="featuredImagePreview" class="mb-4 <?= empty($post['featured_image']) ? 'hidden' : '' ?>">
                        <img src="<?= e($post['featured_image'] ?? '') ?>" alt="Featured Image" class="w-full rounded-lg">
                        <button type="button" onclick="removeFeaturedImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-times mr-1"></i>
                            Remove Image
                        </button>
                    </div>

                    <input type="hidden" name="featured_image" id="featuredImageUrl" value="<?= e($post['featured_image'] ?? '') ?>">

                    <button type="button" onclick="openMediaLibrary()" class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-orange-500 hover:text-orange-500 transition-colors">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Image
                    </button>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Alt Text
                        </label>
                        <input
                            type="text"
                            name="featured_image_alt"
                            value="<?= e($post['featured_image_alt'] ?? '') ?>"
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:outline-none"
                            placeholder="Image description..."
                        >
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Media Library Modal -->
<div id="mediaModal" class="modal">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-2xl font-bold">
                <i class="fas fa-images text-orange-500 mr-2"></i>
                Upload Image
            </h3>
            <button type="button" onclick="closeMediaLibrary()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="p-6">
            <div id="uploadArea" class="border-4 border-dashed border-gray-300 rounded-xl p-12 text-center hover:border-orange-500 transition-colors cursor-pointer">
                <i class="fas fa-cloud-upload text-6xl text-gray-400 mb-4"></i>
                <p class="text-lg font-semibold text-gray-700 mb-2">Drop files here or click to upload</p>
                <p class="text-sm text-gray-500">Maximum file size: <?= formatFileSize(UPLOAD_MAX_SIZE) ?></p>
                <input type="file" id="fileInput" accept="image/*" class="hidden">
            </div>

            <div id="uploadProgress" class="mt-6 hidden">
                <div class="bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="gradient-bg h-2 rounded-full transition-all" style="width: 0%"></div>
                </div>
                <p id="uploadStatus" class="text-sm text-gray-600 mt-2 text-center"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize TinyMCE
tinymce.init({
    selector: '.tinymce',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code fullscreen',
    content_style: 'body { font-family: Poppins, sans-serif; font-size: 16px; line-height: 1.6; }',
    images_upload_url: 'upload.php',
    automatic_uploads: true,
    images_upload_handler: function (blobInfo, success, failure) {
        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('save_to_library', 'true');

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                success(data.file.url);
            } else {
                failure(data.error || 'Upload failed');
            }
        })
        .catch(error => {
            failure('Upload error: ' + error.message);
        });
    }
});

// Media Library
function openMediaLibrary() {
    document.getElementById('mediaModal').classList.add('active');
}

function closeMediaLibrary() {
    document.getElementById('mediaModal').classList.remove('active');
}

// Upload handling
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-orange-500');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('border-orange-500');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-orange-500');
    if (e.dataTransfer.files.length > 0) {
        uploadFile(e.dataTransfer.files[0]);
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        uploadFile(fileInput.files[0]);
    }
});

function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('save_to_library', 'true');
    formData.append('create_thumbnail', 'true');

    const progress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const status = document.getElementById('uploadStatus');

    progress.classList.remove('hidden');
    status.textContent = 'Uploading...';

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            progressBar.style.width = '100%';
            status.textContent = 'Upload complete!';

            // Set featured image
            document.getElementById('featuredImageUrl').value = data.file.url;
            document.getElementById('featuredImagePreview').innerHTML = `
                <img src="${data.file.url}" alt="Featured Image" class="w-full rounded-lg">
                <button type="button" onclick="removeFeaturedImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-times mr-1"></i>
                    Remove Image
                </button>
            `;
            document.getElementById('featuredImagePreview').classList.remove('hidden');

            setTimeout(() => {
                closeMediaLibrary();
                progress.classList.add('hidden');
                progressBar.style.width = '0%';
            }, 1000);
        } else {
            status.textContent = 'Error: ' + data.error;
            progressBar.style.width = '0%';
        }
    })
    .catch(error => {
        status.textContent = 'Upload failed: ' + error.message;
        progressBar.style.width = '0%';
    });
}

function removeFeaturedImage() {
    document.getElementById('featuredImageUrl').value = '';
    document.getElementById('featuredImagePreview').classList.add('hidden');
}

// Form validation
document.getElementById('postForm').addEventListener('submit', function(e) {
    const titleEn = document.querySelector('[name="title_en"]').value.trim();
    const contentEn = tinymce.get('content_en').getContent();
    const category = document.querySelector('[name="category_id"]').value;

    if (!titleEn || !contentEn || !category) {
        e.preventDefault();
        alert('Please fill in all required fields (Title EN, Content EN, Category)');
        return false;
    }
});

// Auto-save draft
let autoSaveTimer;
let lastAutoSave = 0;
const AUTO_SAVE_INTERVAL = 30000; // 30 seconds

function autoSaveDraft() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        performAutoSave();
    }, AUTO_SAVE_INTERVAL);
}

function performAutoSave() {
    const now = Date.now();
    if (now - lastAutoSave < AUTO_SAVE_INTERVAL) {
        return; // Too soon, skip
    }

    const form = document.getElementById('postForm');
    const postIdInput = form.querySelector('[name="post_id"]');
    const postId = <?= $postId ?? 0 ?>;

    // Collect form data
    const formData = {
        post_id: postId,
        title_en: form.querySelector('[name="title_en"]')?.value || '',
        title_ar: form.querySelector('[name="title_ar"]')?.value || '',
        slug_en: form.querySelector('[name="slug_en"]')?.value || '',
        slug_ar: form.querySelector('[name="slug_ar"]')?.value || '',
        content_en: tinymce.get('content_en')?.getContent() || '',
        content_ar: tinymce.get('content_ar')?.getContent() || '',
        excerpt_en: form.querySelector('[name="excerpt_en"]')?.value || '',
        excerpt_ar: form.querySelector('[name="excerpt_ar"]')?.value || '',
        category_id: form.querySelector('[name="category_id"]')?.value || '',
        featured_image: form.querySelector('[name="featured_image"]')?.value || '',
        featured_image_alt: form.querySelector('[name="featured_image_alt"]')?.value || '',
        meta_description_en: form.querySelector('[name="meta_description_en"]')?.value || '',
        meta_description_ar: form.querySelector('[name="meta_description_ar"]')?.value || '',
        meta_keywords: form.querySelector('[name="meta_keywords"]')?.value || ''
    };

    // Only auto-save if we have some content
    if (!formData.title_en && !formData.content_en) {
        return;
    }

    // Show saving indicator
    const indicator = document.getElementById('autoSaveIndicator');
    if (indicator) {
        indicator.textContent = 'Saving...';
        indicator.className = 'text-xs text-gray-500';
    }

    // Send to auto-save endpoint
    fetch('autosave.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lastAutoSave = now;
            const time = new Date().toLocaleTimeString();

            if (indicator) {
                indicator.textContent = `Draft saved at ${time}`;
                indicator.className = 'text-xs text-green-600';
            }

            // If this was a new post, update the URL to edit mode
            if (data.action === 'created' && postId === 0) {
                const newUrl = `posts.php?action=edit&id=${data.post_id}`;
                window.history.replaceState({}, '', newUrl);
                console.log('New draft created with ID:', data.post_id);
            }
        } else {
            if (indicator) {
                indicator.textContent = 'Auto-save failed';
                indicator.className = 'text-xs text-red-600';
            }
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
        if (indicator) {
            indicator.textContent = 'Auto-save failed';
            indicator.className = 'text-xs text-red-600';
        }
    });
}

// Trigger auto-save on content change
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-save timer on any form input change
    const form = document.getElementById('postForm');
    if (form) {
        form.addEventListener('input', autoSaveDraft);
    }

    // Also trigger on TinyMCE content change
    setTimeout(() => {
        tinymce.get('content_en')?.on('change', autoSaveDraft);
        tinymce.get('content_ar')?.on('change', autoSaveDraft);
    }, 1000); // Wait for TinyMCE to initialize
});

// Toggle schedule section based on status
const postStatus = document.getElementById('postStatus');
const scheduleSection = document.getElementById('scheduleSection');

postStatus?.addEventListener('change', function() {
    if (this.value === 'scheduled') {
        scheduleSection.classList.remove('hidden');
    } else {
        scheduleSection.classList.add('hidden');
    }
});
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
