<?php
/**
 * PYRAMEDIA Admin - Media Library Browser
 * Visual gallery for managing uploaded images
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
$mediaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    switch ($action) {
        case 'update':
            $stmt = $db->getConnection()->prepare("
                UPDATE media_library
                SET alt_text = :alt_text, caption = :caption
                WHERE id = :id
            ");
            $stmt->execute([
                'alt_text' => trim($_POST['alt_text'] ?? ''),
                'caption' => trim($_POST['caption'] ?? ''),
                'id' => $mediaId
            ]);
            $success = 'Media updated successfully!';
            $action = 'list';
            break;

        case 'delete':
            // Get filename
            $stmt = $db->getConnection()->prepare("SELECT filename FROM media_library WHERE id = :id");
            $stmt->execute(['id' => $mediaId]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($media) {
                // Delete file
                $filePath = UPLOAD_PATH . $media['filename'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Delete thumbnail
                $thumbPath = UPLOAD_PATH . 'thumb_' . $media['filename'];
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }

                // Delete from database
                $db->delete('media_library', 'id = :id', ['id' => $mediaId]);
                $success = 'Media deleted successfully!';
            }
            $action = 'list';
            break;

        case 'bulk_delete':
            $mediaIds = $_POST['media_ids'] ?? [];
            foreach ($mediaIds as $id) {
                $id = (int)$id;
                $stmt = $db->getConnection()->prepare("SELECT filename FROM media_library WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $media = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($media) {
                    $filePath = UPLOAD_PATH . $media['filename'];
                    if (file_exists($filePath)) unlink($filePath);

                    $thumbPath = UPLOAD_PATH . 'thumb_' . $media['filename'];
                    if (file_exists($thumbPath)) unlink($thumbPath);

                    $db->delete('media_library', 'id = :id', ['id' => $id]);
                }
            }
            $success = count($mediaIds) . ' item(s) deleted successfully!';
            $action = 'list';
            break;
    }
}

// Get media for edit
$media = null;
if ($action === 'edit' && $mediaId > 0) {
    $stmt = $db->getConnection()->prepare("
        SELECT m.*, u.full_name as uploaded_by_name
        FROM media_library m
        LEFT JOIN users u ON m.uploaded_by = u.id
        WHERE m.id = :id
    ");
    $stmt->execute(['id' => $mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all media with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 24; // 4x6 grid
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM media_library");
$totalMedia = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalMedia / $perPage);

// Get media items
$stmt = $db->getConnection()->prepare("
    SELECT m.*, u.full_name as uploaded_by_name
    FROM media_library m
    LEFT JOIN users u ON m.uploaded_by = u.id
    ORDER BY m.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Media Library';
include __DIR__ . '/includes/header.php';
?>

<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-images text-orange-500 mr-3"></i>
                    Media Library
                </h1>
                <p class="text-gray-600 mt-1"><?= number_format($totalMedia) ?> items</p>
            </div>
            <button onclick="openUploadModal()" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-cloud-upload-alt mr-2"></i>
                Upload Images
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

        <?php if (empty($mediaItems)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <i class="fas fa-image text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Images Yet</h3>
            <p class="text-gray-600 mb-6">Upload your first image to get started</p>
            <button onclick="openUploadModal()" class="px-8 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-cloud-upload-alt mr-2"></i>
                Upload Images
            </button>
        </div>
        <?php else: ?>
        <!-- Bulk Actions -->
        <form method="POST" action="media.php?action=bulk_delete" id="bulkForm">
            <input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">

            <div class="bg-white rounded-xl shadow-md p-4 mb-6 flex items-center gap-4">
                <button type="button" onclick="selectAll()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-check-square mr-2"></i>
                    Select All
                </button>
                <button type="button" onclick="deselectAll()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-square mr-2"></i>
                    Deselect All
                </button>
                <button type="submit" onclick="return confirm('Delete selected items?')" class="px-6 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Selected
                </button>
                <span id="selectedCount" class="text-sm text-gray-600"></span>
            </div>

            <!-- Media Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <?php foreach ($mediaItems as $item): ?>
                <div class="media-item bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow relative group">
                    <!-- Selection Checkbox -->
                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" name="media_ids[]" value="<?= $item['id'] ?>"
                               class="media-checkbox w-5 h-5 rounded border-2 border-white shadow-lg">
                    </div>

                    <!-- Image -->
                    <div class="aspect-square bg-gray-100 relative overflow-hidden cursor-pointer"
                         onclick="viewMedia(<?= $item['id'] ?>)">
                        <img src="<?= UPLOAD_URL . $item['filename'] ?>"
                             alt="<?= e($item['alt_text'] ?: $item['filename']) ?>"
                             class="w-full h-full object-cover">

                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <button type="button" onclick="event.stopPropagation(); viewMedia(<?= $item['id'] ?>)"
                                        class="px-3 py-2 bg-white text-gray-800 rounded-lg hover:bg-gray-100" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); editMedia(<?= $item['id'] ?>)"
                                        class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); deleteMedia(<?= $item['id'] ?>)"
                                        class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-800 truncate" title="<?= e($item['filename']) ?>">
                            <?= e($item['original_name']) ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?= formatFileSize($item['file_size']) ?> • <?= $item['width'] ?>×<?= $item['height'] ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <?= timeAgo($item['created_at']) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center">
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-left mr-1"></i> Previous
                </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="px-4 py-2 <?= $i === $page ? 'gradient-bg text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' ?> rounded-lg">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-2xl font-bold">
                <i class="fas fa-cloud-upload-alt text-orange-500 mr-2"></i>
                Upload Images
            </h3>
            <button type="button" onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="p-6">
            <div id="uploadArea" class="border-4 border-dashed border-gray-300 rounded-xl p-12 text-center hover:border-orange-500 transition-colors cursor-pointer">
                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                <p class="text-lg font-semibold text-gray-700 mb-2">Drop files here or click to upload</p>
                <p class="text-sm text-gray-500">Maximum file size: <?= formatFileSize(UPLOAD_MAX_SIZE) ?></p>
                <input type="file" id="fileInput" accept="image/*" multiple class="hidden">
            </div>

            <div id="uploadProgress" class="mt-6 hidden space-y-2"></div>
        </div>
    </div>
</div>

<!-- View/Edit Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-2xl font-bold">Image Details</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="mediaDetails" class="p-6"></div>
    </div>
</div>

<script>
// Modal functions
function openUploadModal() {
    document.getElementById('uploadModal').classList.add('active');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('active');
    document.getElementById('uploadProgress').innerHTML = '';
    document.getElementById('uploadProgress').classList.add('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

// Upload handling
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

uploadArea.addEventListener('click', () => fileInput.click());
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-orange-500');
});
uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('border-orange-500'));
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-orange-500');
    if (e.dataTransfer.files.length > 0) {
        uploadFiles(e.dataTransfer.files);
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        uploadFiles(fileInput.files);
    }
});

function uploadFiles(files) {
    const progress = document.getElementById('uploadProgress');
    progress.classList.remove('hidden');
    progress.innerHTML = '';

    Array.from(files).forEach((file, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'bg-gray-50 rounded-lg p-4';
        itemDiv.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold truncate flex-1">${file.name}</span>
                <span class="upload-status-${index} text-sm text-gray-500">Uploading...</span>
            </div>
            <div class="bg-gray-200 rounded-full h-2">
                <div class="upload-bar-${index} gradient-bg h-2 rounded-full transition-all" style="width: 0%"></div>
            </div>
        `;
        progress.appendChild(itemDiv);

        uploadFile(file, index);
    });
}

function uploadFile(file, index) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('save_to_library', 'true');
    formData.append('create_thumbnail', 'true');

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const bar = document.querySelector(`.upload-bar-${index}`);
        const status = document.querySelector(`.upload-status-${index}`);

        if (data.success) {
            bar.style.width = '100%';
            status.textContent = '✓ Complete';
            status.className = `upload-status-${index} text-sm text-green-600`;

            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            status.textContent = '✗ Failed';
            status.className = `upload-status-${index} text-sm text-red-600`;
        }
    })
    .catch(error => {
        const status = document.querySelector(`.upload-status-${index}`);
        status.textContent = '✗ Error';
        status.className = `upload-status-${index} text-sm text-red-600`;
    });
}

// View media
function viewMedia(id) {
    fetch(`media.php?action=view&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showMediaDetails(data.media);
            }
        });
}

// Delete media
function deleteMedia(id) {
    if (confirm('Delete this image?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `media.php?action=delete&id=${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="<?= $auth->generateCsrfToken() ?>">`;
        document.body.appendChild(form);
        form.submit();
    }
}

// Edit media
function editMedia(id) {
    window.location.href = `media.php?action=edit&id=${id}`;
}

// Selection
function selectAll() {
    document.querySelectorAll('.media-checkbox').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.media-checkbox').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

document.querySelectorAll('.media-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.media-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count > 0 ? `${count} selected` : '';
}
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
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
