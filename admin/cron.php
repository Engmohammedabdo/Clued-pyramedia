<?php
/**
 * PYRAMEDIA - Cron Job Handler
 * Auto-publish scheduled posts
 *
 * Setup instructions:
 * Add to crontab (run every 5 minutes):
 * */5 * * * * /usr/bin/php /path/to/admin/cron.php >> /var/log/pyramedia-cron.log 2>&1
 */

declare(strict_types=1);

// Can be called from CLI or HTTP
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    // If called via HTTP, require authentication
    define('ADMIN_ACCESS', true);
    require_once __DIR__ . '/includes/auth.php';
    require_once __DIR__ . '/includes/config.php';

    $auth = getAuth();
    $auth->requireAdmin(); // Only admins can manually trigger cron

    header('Content-Type: application/json');
} else {
    // CLI mode
    require_once __DIR__ . '/includes/config.php';
}

$db = getDB();

try {
    // Find all scheduled posts that should be published
    $stmt = $db->getConnection()->query("
        SELECT id, title_en, scheduled_for
        FROM blog_posts
        WHERE status = 'scheduled'
        AND scheduled_for IS NOT NULL
        AND scheduled_for <= NOW()
        ORDER BY scheduled_for ASC
    ");

    $scheduledPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $publishedCount = 0;
    $publishedPosts = [];

    foreach ($scheduledPosts as $post) {
        // Update post to published status
        $updateStmt = $db->getConnection()->prepare("
            UPDATE blog_posts
            SET
                status = 'published',
                published_at = scheduled_for,
                updated_at = NOW()
            WHERE id = :id
        ");

        $updateStmt->execute(['id' => $post['id']]);
        $publishedCount++;

        $publishedPosts[] = [
            'id' => $post['id'],
            'title' => $post['title_en'],
            'scheduled_for' => $post['scheduled_for']
        ];

        // Log the action
        error_log(sprintf(
            "[PYRAMEDIA CRON] Published scheduled post #%d: %s (scheduled for %s)",
            $post['id'],
            $post['title_en'],
            $post['scheduled_for']
        ));
    }

    $result = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'published_count' => $publishedCount,
        'posts' => $publishedPosts,
        'message' => $publishedCount > 0
            ? "Successfully published {$publishedCount} scheduled post(s)"
            : "No posts to publish at this time"
    ];

    if ($isCli) {
        echo $result['message'] . "\n";
        if ($publishedCount > 0) {
            foreach ($publishedPosts as $p) {
                echo "  - #{$p['id']}: {$p['title']}\n";
            }
        }
    } else {
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    exit(0);

} catch (Exception $e) {
    $error = [
        'success' => false,
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => $e->getMessage()
    ];

    error_log("[PYRAMEDIA CRON ERROR] " . $e->getMessage());

    if ($isCli) {
        echo "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    } else {
        http_response_code(500);
        echo json_encode($error, JSON_PRETTY_PRINT);
        exit(1);
    }
}
