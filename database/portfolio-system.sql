-- =========================================
-- PYRAMEDIA - Portfolio Management System
-- Database Schema
-- Version: 1.0.0
-- Date: October 31, 2025
-- =========================================

-- ===================
-- 1. CATEGORIES TABLE
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE COMMENT 'URL-friendly identifier',
    name_en VARCHAR(255) NOT NULL COMMENT 'Category name in English',
    name_ar VARCHAR(255) NOT NULL COMMENT 'Category name in Arabic',
    description_en TEXT COMMENT 'Category description in English',
    description_ar TEXT COMMENT 'Category description in Arabic',
    icon VARCHAR(100) COMMENT 'FontAwesome icon class',
    color VARCHAR(7) DEFAULT '#FF6B35' COMMENT 'Hex color code for category',
    display_order INT DEFAULT 0 COMMENT 'Order for display',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Active/Inactive status',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Portfolio project categories';

-- ===================
-- 2. PROJECTS TABLE
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Basic Info
    slug VARCHAR(255) NOT NULL UNIQUE COMMENT 'URL-friendly identifier',
    category_id INT UNSIGNED NOT NULL COMMENT 'FK to portfolio_categories',

    -- Multilingual Content
    title_en VARCHAR(255) NOT NULL COMMENT 'Project title in English',
    title_ar VARCHAR(255) NOT NULL COMMENT 'Project title in Arabic',
    client_name_en VARCHAR(255) COMMENT 'Client name in English',
    client_name_ar VARCHAR(255) COMMENT 'Client name in Arabic',
    description_en TEXT NOT NULL COMMENT 'Project description in English',
    description_ar TEXT NOT NULL COMMENT 'Project description in Arabic',

    -- Project Details
    project_url VARCHAR(500) COMMENT 'Live project URL',
    project_year YEAR COMMENT 'Year of completion',
    project_duration VARCHAR(50) COMMENT 'Duration (e.g., "6 months")',
    budget_range VARCHAR(50) COMMENT 'Budget range (e.g., "$50K-$100K")',

    -- Images
    featured_image VARCHAR(500) NOT NULL COMMENT 'Main project image URL',
    thumbnail_image VARCHAR(500) COMMENT 'Thumbnail image URL',

    -- Flexible Data (JSON)
    services JSON COMMENT 'Array of services provided (e.g., ["SEO", "Design"])',
    technologies JSON COMMENT 'Array of technologies used',
    team_size JSON COMMENT 'Team composition object',

    -- Status & Visibility
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft' COMMENT 'Publication status',
    is_featured BOOLEAN DEFAULT FALSE COMMENT 'Show in featured section',
    is_case_study BOOLEAN DEFAULT FALSE COMMENT 'Has detailed case study',

    -- SEO
    meta_title_en VARCHAR(255) COMMENT 'SEO title in English',
    meta_title_ar VARCHAR(255) COMMENT 'SEO title in Arabic',
    meta_description_en TEXT COMMENT 'SEO description in English',
    meta_description_ar TEXT COMMENT 'SEO description in Arabic',

    -- Analytics
    view_count INT UNSIGNED DEFAULT 0 COMMENT 'Number of views',
    like_count INT UNSIGNED DEFAULT 0 COMMENT 'Number of likes',
    share_count INT UNSIGNED DEFAULT 0 COMMENT 'Number of shares',

    -- Ordering
    display_order INT DEFAULT 0 COMMENT 'Order for display',

    -- Timestamps & Soft Delete
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL COMMENT 'Publication date',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',

    -- Foreign Keys
    FOREIGN KEY (category_id) REFERENCES portfolio_categories(id) ON DELETE RESTRICT,

    -- Indexes
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_published (published_at),
    INDEX idx_deleted (deleted_at),
    INDEX idx_display_order (display_order),

    -- Full-text search
    FULLTEXT INDEX ft_search_en (title_en, client_name_en, description_en),
    FULLTEXT INDEX ft_search_ar (title_ar, client_name_ar, description_ar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Portfolio projects main table';

-- ===================
-- 3. METRICS TABLE
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_metrics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL COMMENT 'FK to portfolio_projects',

    -- Metric Data
    metric_key VARCHAR(100) NOT NULL COMMENT 'Metric identifier (e.g., "sales_growth")',
    metric_value VARCHAR(100) NOT NULL COMMENT 'Metric value (e.g., "300%")',
    metric_label_en VARCHAR(255) NOT NULL COMMENT 'Metric label in English',
    metric_label_ar VARCHAR(255) NOT NULL COMMENT 'Metric label in Arabic',
    metric_type ENUM('percentage', 'number', 'currency', 'duration', 'text') DEFAULT 'text' COMMENT 'Type of metric',

    -- Display
    display_order INT DEFAULT 0 COMMENT 'Order for display',
    is_highlighted BOOLEAN DEFAULT FALSE COMMENT 'Show prominently',
    icon VARCHAR(100) COMMENT 'FontAwesome icon class',
    color VARCHAR(7) DEFAULT '#FF6B35' COMMENT 'Hex color code',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_project (project_id),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Project performance metrics';

-- ===================
-- 4. TAGS/SKILLS TABLE
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE COMMENT 'URL-friendly identifier',
    name_en VARCHAR(100) NOT NULL COMMENT 'Tag name in English',
    name_ar VARCHAR(100) NOT NULL COMMENT 'Tag name in Arabic',
    type ENUM('service', 'technology', 'industry', 'skill') DEFAULT 'skill' COMMENT 'Tag category',
    icon VARCHAR(100) COMMENT 'FontAwesome icon class',
    color VARCHAR(7) DEFAULT '#FF6B35' COMMENT 'Hex color code',
    usage_count INT UNSIGNED DEFAULT 0 COMMENT 'Number of times used',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_type (type),
    INDEX idx_usage (usage_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Project tags and skills';

-- ===================
-- 5. PROJECT-TAGS PIVOT TABLE
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_project_tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES portfolio_tags(id) ON DELETE CASCADE,

    UNIQUE KEY unique_project_tag (project_id, tag_id),
    INDEX idx_project (project_id),
    INDEX idx_tag (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Many-to-many relationship between projects and tags';

-- ===================
-- 6. PROJECT IMAGES/GALLERY
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL COMMENT 'FK to portfolio_projects',

    -- Image Data
    image_url VARCHAR(500) NOT NULL COMMENT 'Image URL',
    image_type ENUM('screenshot', 'mockup', 'before', 'after', 'process', 'result') DEFAULT 'screenshot',
    title_en VARCHAR(255) COMMENT 'Image title in English',
    title_ar VARCHAR(255) COMMENT 'Image title in Arabic',
    caption_en TEXT COMMENT 'Image caption in English',
    caption_ar TEXT COMMENT 'Image caption in Arabic',
    alt_text_en VARCHAR(255) COMMENT 'Alt text for accessibility (English)',
    alt_text_ar VARCHAR(255) COMMENT 'Alt text for accessibility (Arabic)',

    -- Display
    display_order INT DEFAULT 0 COMMENT 'Order in gallery',
    is_featured BOOLEAN DEFAULT FALSE COMMENT 'Main project image',

    -- Technical
    file_size INT UNSIGNED COMMENT 'File size in bytes',
    dimensions VARCHAR(20) COMMENT 'Image dimensions (e.g., "1920x1080")',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,

    INDEX idx_project (project_id),
    INDEX idx_featured (is_featured),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Project images and gallery';

-- ===================
-- 7. TESTIMONIALS LINK (Optional)
-- ===================

CREATE TABLE IF NOT EXISTS portfolio_testimonials_link (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    testimonial_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (testimonial_id) REFERENCES testimonials(id) ON DELETE CASCADE,

    UNIQUE KEY unique_project_testimonial (project_id, testimonial_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Link projects to testimonials';

-- ==========================================
-- SAMPLE DATA INSERTION
-- ==========================================

-- Insert Categories
INSERT INTO portfolio_categories (slug, name_en, name_ar, description_en, description_ar, icon, color, display_order) VALUES
('ecommerce', 'E-Commerce', 'التجارة الإلكترونية', 'Online store development and optimization', 'تطوير وتحسين المتاجر الإلكترونية', 'fa-shopping-cart', '#FF6B35', 1),
('branding', 'Branding', 'العلامات التجارية', 'Brand identity and visual design', 'هوية العلامة التجارية والتصميم البصري', 'fa-palette', '#4A90E2', 2),
('automation', 'Marketing Automation', 'أتمتة التسويق', 'Marketing automation and workflow optimization', 'أتمتة التسويق وتحسين سير العمل', 'fa-robot', '#10B981', 3),
('social-media', 'Social Media', 'وسائل التواصل الاجتماعي', 'Social media marketing and management', 'تسويق وإدارة وسائل التواصل الاجتماعي', 'fa-share-alt', '#EC4899', 4),
('seo', 'SEO', 'تحسين محركات البحث', 'Search engine optimization and content strategy', 'تحسين محركات البحث واستراتيجية المحتوى', 'fa-search', '#8B5CF6', 5),
('video', 'Video Production', 'إنتاج الفيديو', 'Video content creation and marketing', 'إنشاء محتوى الفيديو والتسويق', 'fa-video', '#F59E0B', 6);

-- Insert Tags
INSERT INTO portfolio_tags (slug, name_en, name_ar, type) VALUES
('marketing-automation', 'Marketing Automation', 'أتمتة التسويق', 'service'),
('seo-optimization', 'SEO', 'تحسين محركات البحث', 'service'),
('social-media-marketing', 'Social Media Marketing', 'التسويق عبر وسائل التواصل', 'service'),
('email-campaigns', 'Email Campaigns', 'حملات البريد الإلكتروني', 'service'),
('brand-strategy', 'Brand Strategy', 'استراتيجية العلامة التجارية', 'service'),
('logo-design', 'Logo Design', 'تصميم الشعار', 'service'),
('visual-identity', 'Visual Identity', 'الهوية البصرية', 'service'),
('hubspot', 'HubSpot', 'هب سبوت', 'technology'),
('zapier', 'Zapier', 'زابير', 'technology'),
('google-analytics', 'Google Analytics', 'جوجل أناليتكس', 'technology'),
('wordpress', 'WordPress', 'ووردبريس', 'technology'),
('shopify', 'Shopify', 'شوبيفاي', 'technology'),
('instagram', 'Instagram', 'انستغرام', 'technology'),
('tiktok', 'TikTok', 'تيك توك', 'technology'),
('youtube', 'YouTube', 'يوتيوب', 'technology');

-- Insert Sample Project #1
INSERT INTO portfolio_projects (
    slug, category_id,
    title_en, title_ar,
    client_name_en, client_name_ar,
    description_en, description_ar,
    featured_image, project_year, project_duration,
    services, technologies,
    status, is_featured, display_order
) VALUES (
    '300-ecommerce-growth-gulf-retail',
    (SELECT id FROM portfolio_categories WHERE slug = 'ecommerce'),
    '300% E-Commerce Growth',
    'نمو 300% في التجارة الإلكترونية',
    'Gulf Retail Group',
    'مجموعة الخليج للتجزئة',
    'Transformed online presence for leading UAE retailer, implementing advanced marketing automation and personalized customer journeys. We rebuilt their entire e-commerce infrastructure, integrated AI-powered recommendations, and created a seamless omnichannel experience that resulted in unprecedented growth.',
    'حولنا الحضور الرقمي لأكبر بائع تجزئة في الإمارات، من خلال تطبيق أتمتة تسويقية متقدمة ورحلات عملاء مخصصة. أعدنا بناء البنية التحتية الكاملة للتجارة الإلكترونية، ودمجنا توصيات مدعومة بالذكاء الاصطناعي، وأنشأنا تجربة متعددة القنوات سلسة أدت إلى نمو غير مسبوق.',
    'https://images.unsplash.com/photo-1556742111-a301076d9d18?w=800&h=600&fit=crop',
    2024,
    '6 months',
    '["Marketing Automation", "SEO", "Social Media", "Email Campaigns"]',
    '["Shopify", "HubSpot", "Google Analytics"]',
    'published',
    TRUE,
    1
);

-- Get the last inserted project ID
SET @last_project_id = LAST_INSERT_ID();

-- Insert Metrics for Project #1
INSERT INTO portfolio_metrics (project_id, metric_key, metric_value, metric_label_en, metric_label_ar, metric_type, display_order) VALUES
(@last_project_id, 'sales_growth', '300%', 'Sales Growth', 'نمو المبيعات', 'percentage', 1),
(@last_project_id, 'timeline', '6M', 'Timeline', 'المدة الزمنية', 'duration', 2),
(@last_project_id, 'new_customers', '50K+', 'New Customers', 'عملاء جدد', 'number', 3),
(@last_project_id, 'roi', '450%', 'ROI', 'عائد الاستثمار', 'percentage', 4);

-- Link Tags to Project #1
INSERT INTO portfolio_project_tags (project_id, tag_id) VALUES
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'marketing-automation')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'seo-optimization')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'social-media-marketing')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'email-campaigns')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'shopify')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'hubspot')),
(@last_project_id, (SELECT id FROM portfolio_tags WHERE slug = 'google-analytics'));

-- ==========================================
-- VIEWS FOR COMMON QUERIES
-- ==========================================

-- View: Published Projects with Category Info
CREATE OR REPLACE VIEW vw_published_projects AS
SELECT
    p.id,
    p.slug,
    p.title_en,
    p.title_ar,
    p.client_name_en,
    p.client_name_ar,
    p.description_en,
    p.description_ar,
    p.featured_image,
    p.is_featured,
    p.view_count,
    p.published_at,
    c.slug AS category_slug,
    c.name_en AS category_name_en,
    c.name_ar AS category_name_ar,
    c.color AS category_color
FROM portfolio_projects p
INNER JOIN portfolio_categories c ON p.category_id = c.id
WHERE p.status = 'published'
  AND p.deleted_at IS NULL
  AND c.is_active = TRUE;

-- View: Project Metrics Summary
CREATE OR REPLACE VIEW vw_project_metrics AS
SELECT
    p.id AS project_id,
    p.slug,
    p.title_en,
    COUNT(DISTINCT m.id) AS metrics_count,
    COUNT(DISTINCT pt.tag_id) AS tags_count,
    COUNT(DISTINCT pi.id) AS images_count,
    p.view_count,
    p.like_count,
    p.share_count
FROM portfolio_projects p
LEFT JOIN portfolio_metrics m ON p.id = m.project_id
LEFT JOIN portfolio_project_tags pt ON p.id = pt.project_id
LEFT JOIN portfolio_images pi ON p.id = pi.project_id
WHERE p.deleted_at IS NULL
GROUP BY p.id;

-- ==========================================
-- STORED PROCEDURES
-- ==========================================

DELIMITER //

-- Procedure: Increment View Count
CREATE PROCEDURE sp_increment_view_count(IN p_project_id INT)
BEGIN
    UPDATE portfolio_projects
    SET view_count = view_count + 1
    WHERE id = p_project_id AND deleted_at IS NULL;
END //

-- Procedure: Get Featured Projects
CREATE PROCEDURE sp_get_featured_projects(IN p_lang VARCHAR(2), IN p_limit INT)
BEGIN
    IF p_lang = 'ar' THEN
        SELECT
            p.id, p.slug,
            p.title_ar AS title,
            p.client_name_ar AS client_name,
            p.description_ar AS description,
            p.featured_image,
            c.name_ar AS category_name,
            c.color AS category_color
        FROM portfolio_projects p
        INNER JOIN portfolio_categories c ON p.category_id = c.id
        WHERE p.is_featured = TRUE
          AND p.status = 'published'
          AND p.deleted_at IS NULL
        ORDER BY p.display_order ASC, p.published_at DESC
        LIMIT p_limit;
    ELSE
        SELECT
            p.id, p.slug,
            p.title_en AS title,
            p.client_name_en AS client_name,
            p.description_en AS description,
            p.featured_image,
            c.name_en AS category_name,
            c.color AS category_color
        FROM portfolio_projects p
        INNER JOIN portfolio_categories c ON p.category_id = c.id
        WHERE p.is_featured = TRUE
          AND p.status = 'published'
          AND p.deleted_at IS NULL
        ORDER BY p.display_order ASC, p.published_at DESC
        LIMIT p_limit;
    END IF;
END //

DELIMITER ;

-- ==========================================
-- TRIGGERS
-- ==========================================

DELIMITER //

-- Trigger: Auto-publish timestamp
CREATE TRIGGER trg_project_published
BEFORE UPDATE ON portfolio_projects
FOR EACH ROW
BEGIN
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        SET NEW.published_at = CURRENT_TIMESTAMP;
    END IF;
END //

-- Trigger: Update tag usage count
CREATE TRIGGER trg_tag_usage_increment
AFTER INSERT ON portfolio_project_tags
FOR EACH ROW
BEGIN
    UPDATE portfolio_tags
    SET usage_count = usage_count + 1
    WHERE id = NEW.tag_id;
END //

CREATE TRIGGER trg_tag_usage_decrement
AFTER DELETE ON portfolio_project_tags
FOR EACH ROW
BEGIN
    UPDATE portfolio_tags
    SET usage_count = usage_count - 1
    WHERE id = OLD.tag_id;
END //

DELIMITER ;

-- ==========================================
-- INDEXES FOR PERFORMANCE
-- ==========================================

-- Additional composite indexes for common queries
CREATE INDEX idx_status_featured ON portfolio_projects(status, is_featured, display_order);
CREATE INDEX idx_category_status ON portfolio_projects(category_id, status, published_at);

-- ==========================================
-- PERMISSIONS (Optional - for security)
-- ==========================================

-- Create read-only user for API
-- CREATE USER 'portfolio_api'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT ON pyramedia_db.portfolio_* TO 'portfolio_api'@'localhost';
-- GRANT SELECT ON pyramedia_db.vw_* TO 'portfolio_api'@'localhost';
-- GRANT EXECUTE ON PROCEDURE pyramedia_db.sp_increment_view_count TO 'portfolio_api'@'localhost';

-- Create admin user for management
-- CREATE USER 'portfolio_admin'@'localhost' IDENTIFIED BY 'admin_password_here';
-- GRANT ALL PRIVILEGES ON pyramedia_db.portfolio_* TO 'portfolio_admin'@'localhost';

-- ==========================================
-- COMPLETION MESSAGE
-- ==========================================

SELECT 'Portfolio database schema created successfully!' AS Status;
SELECT 'Tables created: 7' AS Info;
SELECT 'Views created: 2' AS Info;
SELECT 'Stored procedures created: 2' AS Info;
SELECT 'Triggers created: 3' AS Info;
SELECT 'Sample data: 1 project with metrics and tags' AS Info;
