-- ========================================
-- PYRAMEDIA - Blog System Database Schema
-- ========================================

-- Create database (if needed)
CREATE DATABASE IF NOT EXISTS pyramed1_final CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pyramed1_final;

-- ========================================
-- Categories Table
-- ========================================
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description_en TEXT,
    description_ar TEXT,
    icon VARCHAR(50),
    color VARCHAR(20) DEFAULT '#FF6B35',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tags Table
-- ========================================
CREATE TABLE IF NOT EXISTS blog_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(50) NOT NULL,
    name_ar VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Blog Posts Table
-- ========================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- English Content
    title_en VARCHAR(255) NOT NULL,
    slug_en VARCHAR(255) NOT NULL UNIQUE,
    excerpt_en TEXT,
    content_en LONGTEXT,
    meta_description_en VARCHAR(160),

    -- Arabic Content
    title_ar VARCHAR(255) NOT NULL,
    slug_ar VARCHAR(255) NOT NULL UNIQUE,
    excerpt_ar TEXT,
    content_ar LONGTEXT,
    meta_description_ar VARCHAR(160),

    -- Media
    featured_image VARCHAR(255),
    featured_image_alt VARCHAR(255),

    -- Organization
    category_id INT,
    author_id INT DEFAULT 1,

    -- SEO & Meta
    meta_keywords VARCHAR(255),
    read_time INT DEFAULT 5,

    -- Status & Visibility
    status ENUM('draft', 'published', 'scheduled', 'archived') DEFAULT 'draft',
    visibility ENUM('public', 'private', 'password') DEFAULT 'public',
    password VARCHAR(255),

    -- Scheduling
    published_at TIMESTAMP NULL,
    scheduled_for TIMESTAMP NULL,

    -- Statistics
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    shares INT DEFAULT 0,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_slug_en (slug_en),
    INDEX idx_slug_ar (slug_ar),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_published (published_at),
    INDEX idx_created (created_at),
    FULLTEXT idx_search_en (title_en, content_en),
    FULLTEXT idx_search_ar (title_ar, content_ar),

    -- Foreign Keys
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Post-Tags Pivot Table
-- ========================================
CREATE TABLE IF NOT EXISTS blog_post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_tag (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Comments Table
-- ========================================
CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    parent_id INT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(255) NOT NULL,
    author_website VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'pending',
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_post (post_id),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES blog_comments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Users/Authors Table (Simplified)
-- ========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    bio TEXT,
    avatar VARCHAR(255),
    role ENUM('admin', 'editor', 'author') DEFAULT 'author',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insert Default Data
-- ========================================

-- Default Admin User (password: admin123 - CHANGE THIS!)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@pyramedia.ae', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

-- Default Categories
INSERT INTO blog_categories (name_en, name_ar, slug, description_en, description_ar, icon) VALUES
('Marketing', 'التسويق', 'marketing', 'Digital marketing insights and strategies', 'رؤى واستراتيجيات التسويق الرقمي', 'fa-bullhorn'),
('Automation', 'الأتمتة', 'automation', 'Marketing automation best practices', 'أفضل ممارسات أتمتة التسويق', 'fa-robot'),
('AI & Technology', 'الذكاء الاصطناعي', 'ai-technology', 'AI-powered marketing solutions', 'حلول التسويق بالذكاء الاصطناعي', 'fa-brain'),
('Social Media', 'وسائل التواصل', 'social-media', 'Social media marketing tips', 'نصائح التسويق عبر وسائل التواصل', 'fa-share-alt'),
('SEO', 'تحسين محركات البحث', 'seo', 'Search engine optimization guides', 'أدلة تحسين محركات البحث', 'fa-search'),
('Content Marketing', 'تسويق المحتوى', 'content-marketing', 'Content creation and strategy', 'إنشاء المحتوى والاستراتيجية', 'fa-pen');

-- Default Tags
INSERT INTO blog_tags (name_en, name_ar, slug) VALUES
('Digital Strategy', 'الاستراتيجية الرقمية', 'digital-strategy'),
('Social Media', 'وسائل التواصل', 'social-media'),
('Content Creation', 'إنشاء المحتوى', 'content-creation'),
('Analytics', 'التحليلات', 'analytics'),
('Lead Generation', 'توليد العملاء', 'lead-generation'),
('Email Marketing', 'التسويق بالبريد', 'email-marketing'),
('Branding', 'العلامة التجارية', 'branding'),
('ROI', 'عائد الاستثمار', 'roi'),
('Growth Hacking', 'القرصنة النمو', 'growth-hacking'),
('Conversion', 'التحويل', 'conversion');

-- Sample Blog Posts
INSERT INTO blog_posts (
    title_en, slug_en, excerpt_en, content_en, meta_description_en,
    title_ar, slug_ar, excerpt_ar, content_ar, meta_description_ar,
    category_id, featured_image, read_time, status, published_at, views
) VALUES
(
    'The Future of Digital Marketing in 2025',
    'future-digital-marketing-2025',
    'Discover emerging trends and technologies shaping the future of digital marketing in the GCC region...',
    '<h2>Introduction</h2><p>The digital marketing landscape is evolving rapidly...</p>',
    'Explore the latest digital marketing trends for 2025 in the GCC region',
    'مستقبل التسويق الرقمي في 2025',
    'future-digital-marketing-2025-ar',
    'اكتشف الاتجاهات والتقنيات الناشئة التي تشكل مستقبل التسويق الرقمي في منطقة الخليج...',
    '<h2>المقدمة</h2><p>يتطور مشهد التسويق الرقمي بسرعة...</p>',
    'استكشف أحدث اتجاهات التسويق الرقمي لعام 2025 في منطقة الخليج',
    1,
    'https://images.unsplash.com/photo-1557838923-2985c318be48?w=1200&h=630&fit=crop',
    5,
    'published',
    NOW(),
    1250
),
(
    'Marketing Automation Best Practices',
    'marketing-automation-best-practices',
    'Learn how to implement effective marketing automation strategies that drive results and save time...',
    '<h2>Why Automation Matters</h2><p>Marketing automation is essential for modern businesses...</p>',
    'Implement effective marketing automation strategies for better ROI',
    'أفضل ممارسات أتمتة التسويق',
    'marketing-automation-best-practices-ar',
    'تعلم كيفية تطبيق استراتيجيات فعالة لأتمتة التسويق تحقق نتائج وتوفر الوقت...',
    '<h2>لماذا تهم الأتمتة</h2><p>أتمتة التسويق ضرورية للشركات الحديثة...</p>',
    'تطبيق استراتيجيات أتمتة التسويق الفعالة للحصول على عائد استثمار أفضل',
    2,
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&h=630&fit=crop',
    7,
    'published',
    DATE_SUB(NOW(), INTERVAL 3 DAY),
    890
),
(
    'AI-Powered Marketing: Complete Guide',
    'ai-powered-marketing-guide',
    'Explore how artificial intelligence is revolutionizing marketing strategies and customer experiences...',
    '<h2>The AI Revolution in Marketing</h2><p>AI is changing how we approach marketing...</p>',
    'Complete guide to AI-powered marketing strategies and tools',
    'التسويق بالذكاء الاصطناعي: دليل شامل',
    'ai-powered-marketing-guide-ar',
    'اكتشف كيف يُحدث الذكاء الاصطناعي ثورة في استراتيجيات التسويق وتجارب العملاء...',
    '<h2>ثورة الذكاء الاصطناعي في التسويق</h2><p>يغير الذكاء الاصطناعي طريقة تعاملنا مع التسويق...</p>',
    'دليل شامل لاستراتيجيات وأدوات التسويق بالذكاء الاصطناعي',
    3,
    'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=630&fit=crop',
    6,
    'published',
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    1567
);

-- Link posts to tags
INSERT INTO blog_post_tags (post_id, tag_id) VALUES
(1, 1), (1, 2), (1, 4),
(2, 5), (2, 6), (2, 8),
(3, 1), (3, 4), (3, 9);

-- Sample Comments
INSERT INTO blog_comments (post_id, author_name, author_email, content, status) VALUES
(1, 'Ahmed Mohammed', 'ahmed@example.com', 'Great article! Very insightful about the future trends.', 'approved'),
(1, 'Sara Ali', 'sara@example.com', 'Thanks for sharing this valuable information.', 'approved'),
(2, 'Mohammed Hassan', 'mohammed@example.com', 'This helped me understand automation better. Thank you!', 'approved');

-- ========================================
-- Useful Views
-- ========================================

-- Published Posts with Category Info
CREATE OR REPLACE VIEW published_posts AS
SELECT
    p.*,
    c.name_en as category_name_en,
    c.name_ar as category_name_ar,
    c.slug as category_slug,
    u.full_name as author_name,
    u.avatar as author_avatar
FROM blog_posts p
LEFT JOIN blog_categories c ON p.category_id = c.id
LEFT JOIN users u ON p.author_id = u.id
WHERE p.status = 'published' AND p.published_at <= NOW()
ORDER BY p.published_at DESC;

-- Post Statistics
CREATE OR REPLACE VIEW post_stats AS
SELECT
    p.id,
    p.title_en,
    p.views,
    p.likes,
    p.shares,
    COUNT(DISTINCT c.id) as comments_count,
    COUNT(DISTINCT pt.tag_id) as tags_count
FROM blog_posts p
LEFT JOIN blog_comments c ON p.id = c.post_id AND c.status = 'approved'
LEFT JOIN blog_post_tags pt ON p.id = pt.post_id
GROUP BY p.id;

-- ========================================
-- End of Schema
-- ========================================
