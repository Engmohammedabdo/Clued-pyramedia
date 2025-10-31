<?php
/**
 * PYRAMEDIA Admin - Image Upload Handler
 * Handles image uploads for blog posts and media library
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$auth = getAuth();

// Verify user is logged in
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        throw new Exception($errors[$file['error']] ?? 'Unknown upload error');
    }

    // Validate file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new Exception('File size exceeds ' . formatFileSize(UPLOAD_MAX_SIZE));
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
        throw new Exception('Invalid file type. Allowed: JPEG, PNG, GIF, WebP');
    }

    // Validate image dimensions (optional - prevent extremely large images)
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('Invalid image file');
    }

    [$width, $height] = $imageInfo;
    if ($width > 5000 || $height > 5000) {
        throw new Exception('Image dimensions too large (max 5000x5000)');
    }

    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_PATH;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename
    $originalName = basename($file['name']);
    $filename = generateUniqueFilename($originalName);
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Optimize image (optional - reduce file size)
    optimizeImage($uploadPath, $mimeType);

    // Generate thumbnail (optional)
    $thumbnailPath = null;
    if (isset($_POST['create_thumbnail']) && $_POST['create_thumbnail'] === 'true') {
        $thumbnailPath = createThumbnail($uploadPath, $filename);
    }

    // Get file info
    $fileSize = filesize($uploadPath);
    $dimensions = getimagesize($uploadPath);

    // Log upload to database (optional - for media library)
    if (isset($_POST['save_to_library']) && $_POST['save_to_library'] === 'true') {
        $db = getDB();
        $user = $auth->getCurrentUser();

        $stmt = $db->getConnection()->prepare("
            INSERT INTO media_library
            (filename, original_name, mime_type, file_size, width, height, uploaded_by, created_at)
            VALUES (:filename, :original_name, :mime_type, :file_size, :width, :height, :uploaded_by, NOW())
        ");

        $stmt->execute([
            'filename' => $filename,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'uploaded_by' => $user['id']
        ]);
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'file' => [
            'url' => UPLOAD_URL . $filename,
            'filename' => $filename,
            'original_name' => $originalName,
            'size' => $fileSize,
            'size_formatted' => formatFileSize($fileSize),
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'mime_type' => $mimeType,
            'thumbnail' => $thumbnailPath ? UPLOAD_URL . $thumbnailPath : null
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Optimize image to reduce file size
 */
function optimizeImage(string $path, string $mimeType): void {
    try {
        $quality = 85; // JPEG quality (0-100)

        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                if ($image) {
                    imagejpeg($image, $path, $quality);
                    imagedestroy($image);
                }
                break;

            case 'image/png':
                $image = imagecreatefrompng($path);
                if ($image) {
                    imagepng($image, $path, 9); // PNG compression (0-9)
                    imagedestroy($image);
                }
                break;

            case 'image/gif':
                // GIF optimization is limited
                break;

            case 'image/webp':
                $image = imagecreatefromwebp($path);
                if ($image) {
                    imagewebp($image, $path, $quality);
                    imagedestroy($image);
                }
                break;
        }
    } catch (Exception $e) {
        error_log("Image optimization failed: " . $e->getMessage());
    }
}

/**
 * Create thumbnail for image
 */
function createThumbnail(string $sourcePath, string $filename): ?string {
    try {
        $thumbWidth = 300;
        $thumbHeight = 300;

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return null;
        }

        [$width, $height, $type] = $imageInfo;

        // Don't create thumbnail if image is already small
        if ($width <= $thumbWidth && $height <= $thumbHeight) {
            return null;
        }

        // Create image resource
        $source = match($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => null
        };

        if (!$source) {
            return null;
        }

        // Calculate thumbnail dimensions (maintain aspect ratio)
        $ratio = min($thumbWidth / $width, $thumbHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create thumbnail
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save thumbnail
        $thumbFilename = 'thumb_' . $filename;
        $thumbPath = UPLOAD_PATH . $thumbFilename;

        $success = match($type) {
            IMAGETYPE_JPEG => imagejpeg($thumbnail, $thumbPath, 85),
            IMAGETYPE_PNG => imagepng($thumbnail, $thumbPath, 9),
            IMAGETYPE_GIF => imagegif($thumbnail, $thumbPath),
            IMAGETYPE_WEBP => imagewebp($thumbnail, $thumbPath, 85),
            default => false
        };

        imagedestroy($source);
        imagedestroy($thumbnail);

        return $success ? $thumbFilename : null;

    } catch (Exception $e) {
        error_log("Thumbnail creation failed: " . $e->getMessage());
        return null;
    }
}
