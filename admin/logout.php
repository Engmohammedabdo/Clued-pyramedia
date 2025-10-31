<?php
/**
 * PYRAMEDIA Admin - Logout
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';

$auth = getAuth();
$auth->logout();

header('Location: index.php');
exit;
