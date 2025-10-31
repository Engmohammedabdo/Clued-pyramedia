<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= ADMIN_TITLE ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6.4.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
        }

        .sidebar {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .nav-link {
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 107, 53, 0.1);
            transform: translateX(4px);
        }

        .nav-link.active {
            background-color: rgba(255, 107, 53, 0.15);
            border-left: 4px solid #FF6B35;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-white shadow-xl z-50 fixed md:relative h-full">
            <!-- Logo -->
            <div class="gradient-bg p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pyramid text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-xl font-bold">PYRAMEDIA</h1>
                        <p class="text-xs opacity-90">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 overflow-y-auto h-[calc(100vh-200px)]">
                <div class="space-y-1">
                    <a href="dashboard.php" class="nav-link <?= isCurrentPage('dashboard') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>

                    <a href="posts.php" class="nav-link <?= isCurrentPage('posts') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-file-alt w-5"></i>
                        <span class="ml-3 font-medium">Posts</span>
                    </a>

                    <a href="categories.php" class="nav-link <?= isCurrentPage('categories') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-folder w-5"></i>
                        <span class="ml-3 font-medium">Categories</span>
                    </a>

                    <a href="tags.php" class="nav-link <?= isCurrentPage('tags') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-tags w-5"></i>
                        <span class="ml-3 font-medium">Tags</span>
                    </a>

                    <?php if ($auth->isAdmin()): ?>
                    <a href="users.php" class="nav-link <?= isCurrentPage('users') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-3 font-medium">Users</span>
                    </a>
                    <?php endif; ?>

                    <hr class="my-4 border-gray-200">

                    <a href="media.php" class="nav-link <?= isCurrentPage('media') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-images w-5"></i>
                        <span class="ml-3 font-medium">Media Library</span>
                    </a>

                    <a href="settings.php" class="nav-link <?= isCurrentPage('settings') ? 'active' : '' ?> flex items-center px-4 py-3 text-gray-700 rounded-lg">
                        <i class="fas fa-cog w-5"></i>
                        <span class="ml-3 font-medium">Settings</span>
                    </a>
                </div>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center space-x-3">
                    <img src="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name'])) ?>"
                         alt="<?= e($user['name']) ?>"
                         class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate"><?= e($user['name']) ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= e($user['role']) ?></p>
                    </div>
                    <a href="logout.php" class="text-red-600 hover:text-red-800" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Header -->
            <div class="md:hidden bg-white shadow-sm px-4 py-3 flex items-center justify-between">
                <button id="mobileSidebarToggle" class="text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="font-bold text-gray-800">PYRAMEDIA Admin</h1>
                <a href="../blog.html" class="text-orange-500">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>

            <!-- Page Content (will be filled by each page) -->
