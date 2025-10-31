-- ========================================
-- PYRAMEDIA - Testimonials System
-- Complete testimonials management
-- ========================================

-- Create testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    client_position VARCHAR(255),
    client_company VARCHAR(255),
    client_email VARCHAR(255),
    client_avatar VARCHAR(500),
    rating INT DEFAULT 5,
    testimonial_text TEXT NOT NULL,
    testimonial_text_ar TEXT,
    project_type VARCHAR(100),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_featured (featured),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample testimonials data
INSERT INTO testimonials (
    client_name,
    client_position,
    client_company,
    client_email,
    client_avatar,
    rating,
    testimonial_text,
    testimonial_text_ar,
    project_type,
    status,
    featured,
    display_order
) VALUES
(
    'Ahmed Al-Mansouri',
    'CEO',
    'TechVision UAE',
    'ahmed@techvision.ae',
    'https://ui-avatars.com/api/?name=Ahmed+Al-Mansouri&background=FF6B35&color=fff&size=200',
    5,
    'PYRAMEDIA transformed our digital presence completely. Their expertise in marketing automation helped us achieve 300% ROI in just 6 months. Highly recommended!',
    'حوّلت بيراميديا حضورنا الرقمي بالكامل. خبرتهم في أتمتة التسويق ساعدتنا على تحقيق عائد استثمار 300% في 6 أشهر فقط. موصى بهم بشدة!',
    'Marketing Automation',
    'approved',
    TRUE,
    1
),
(
    'Sarah Johnson',
    'Marketing Director',
    'Gulf Retail Group',
    'sarah@gulfretail.com',
    'https://ui-avatars.com/api/?name=Sarah+Johnson&background=4A90E2&color=fff&size=200',
    5,
    'Outstanding service! The team at PYRAMEDIA delivered beyond our expectations. Our social media engagement increased by 250% within 3 months.',
    'خدمة متميزة! فريق بيراميديا تجاوز توقعاتنا. زاد تفاعلنا على وسائل التواصل بنسبة 250% في 3 أشهر.',
    'Social Media Marketing',
    'approved',
    TRUE,
    2
),
(
    'Mohammed Al-Hashimi',
    'Founder',
    'StartupHub Dubai',
    'mohammed@startuphub.ae',
    'https://ui-avatars.com/api/?name=Mohammed+Al-Hashimi&background=10B981&color=fff&size=200',
    5,
    'The branding strategy they created for us was phenomenal. Our brand recognition in the market improved dramatically. Great team to work with!',
    'كانت استراتيجية العلامة التجارية التي أنشأوها لنا رائعة. تحسن تميز علامتنا التجارية في السوق بشكل كبير. فريق رائع للعمل معه!',
    'Brand Strategy',
    'approved',
    TRUE,
    3
),
(
    'Lisa Anderson',
    'E-commerce Manager',
    'Fashion Boutique',
    'lisa@fashionboutique.com',
    'https://ui-avatars.com/api/?name=Lisa+Anderson&background=EC4899&color=fff&size=200',
    5,
    'PYRAMEDIA helped us grow our e-commerce business from scratch. Their digital marketing strategies are top-notch. Sales increased by 400%!',
    'ساعدتنا بيراميديا في تنمية أعمال التجارة الإلكترونية من الصفر. استراتيجيات التسويق الرقمي لديهم من الدرجة الأولى. زادت المبيعات بنسبة 400%!',
    'E-commerce Growth',
    'approved',
    TRUE,
    4
),
(
    'Khalid Al-Kuwari',
    'Operations Manager',
    'Qatar Tech Solutions',
    'khalid@qatartech.qa',
    'https://ui-avatars.com/api/?name=Khalid+Al-Kuwari&background=8B5CF6&color=fff&size=200',
    5,
    'Professional, creative, and results-driven. PYRAMEDIA is our go-to agency for all marketing needs. They truly understand the GCC market.',
    'محترفون ومبدعون ومركزون على النتائج. بيراميديا هي وكالتنا المفضلة لجميع احتياجات التسويق. يفهمون حقاً سوق الخليج.',
    'Full Marketing Package',
    'approved',
    TRUE,
    5
),
(
    'Emily Chen',
    'Brand Manager',
    'Luxury Lifestyle',
    'emily@luxurylifestyle.ae',
    'https://ui-avatars.com/api/?name=Emily+Chen&background=F59E0B&color=fff&size=200',
    5,
    'The video production and content creation services are exceptional. Our engagement rates skyrocketed after their campaign. Worth every dirham!',
    'خدمات إنتاج الفيديو وإنشاء المحتوى استثنائية. ارتفعت معدلات تفاعلنا بشكل كبير بعد حملتهم. تستحق كل درهم!',
    'Video Production',
    'approved',
    FALSE,
    6
),
(
    'Abdullah Al-Suwaidi',
    'Business Owner',
    'Real Estate Dubai',
    'abdullah@realestateDXB.ae',
    'https://ui-avatars.com/api/?name=Abdullah+Al-Suwaidi&background=DC2626&color=fff&size=200',
    4,
    'Great experience working with PYRAMEDIA. Their AI-powered marketing solutions helped us target the right audience efficiently.',
    'تجربة رائعة في العمل مع بيراميديا. ساعدتنا حلول التسويق المدعومة بالذكاء الاصطناعي على استهداف الجمهور المناسب بكفاءة.',
    'AI Marketing',
    'approved',
    FALSE,
    7
);

-- Create testimonials_analytics table for tracking
CREATE TABLE IF NOT EXISTS testimonials_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    testimonial_id INT NOT NULL,
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    shares INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (testimonial_id) REFERENCES testimonials(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert analytics for sample testimonials
INSERT INTO testimonials_analytics (testimonial_id, views, likes, shares)
SELECT id, FLOOR(RAND() * 1000) + 500, FLOOR(RAND() * 50) + 10, FLOOR(RAND() * 20) + 5
FROM testimonials;
