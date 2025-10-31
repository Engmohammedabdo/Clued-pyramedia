-- ========================================
-- PYRAMEDIA Sample Blog Posts
-- Add sample posts for testing
-- ========================================

-- Sample Post 1: AI Marketing
INSERT INTO blog_posts (
    author_id,
    category_id,
    title_en,
    title_ar,
    slug_en,
    slug_ar,
    excerpt_en,
    excerpt_ar,
    content_en,
    content_ar,
    featured_image,
    meta_description_en,
    meta_description_ar,
    meta_keywords,
    status,
    published_at,
    read_time,
    views,
    created_at,
    updated_at
)
VALUES (
    1,
    1,
    'Complete Guide to AI-Powered Marketing in 2025',
    'دليل كامل للتسويق بالذكاء الاصطناعي في 2025',
    'ai-powered-marketing-guide-2025',
    'ai-powered-marketing-guide-2025-ar',
    'Discover how artificial intelligence is revolutionizing marketing strategies and helping businesses achieve unprecedented growth in 2025.',
    'اكتشف كيف يُحدث الذكاء الاصطناعي ثورة في استراتيجيات التسويق ويساعد الشركات على تحقيق نمو غير مسبوق في 2025.',
    '<h2>Introduction to AI Marketing</h2><p>Artificial Intelligence is transforming the marketing landscape in ways we never imagined. From personalized content creation to predictive analytics, AI is helping marketers achieve better results with less effort.</p><h3>Key Benefits of AI in Marketing</h3><ul><li>Enhanced customer personalization</li><li>Predictive analytics and insights</li><li>Automated content creation</li><li>Improved ROI tracking</li></ul><p>The future of marketing is here, and it''s powered by AI.</p>',
    '<h2>مقدمة للتسويق بالذكاء الاصطناعي</h2><p>يُحدث الذكاء الاصطناعي تحولاً في مشهد التسويق بطرق لم نكن نتخيلها. من إنشاء المحتوى المخصص إلى التحليلات التنبؤية، يساعد الذكاء الاصطناعي المسوقين على تحقيق نتائج أفضل بجهد أقل.</p>',
    'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=600&fit=crop',
    'Complete guide to using AI in marketing strategies for 2025. Learn how to leverage artificial intelligence for better ROI.',
    'دليل كامل لاستخدام الذكاء الاصطناعي في استراتيجيات التسويق لعام 2025',
    'AI, marketing, artificial intelligence, digital marketing, automation',
    'published',
    NOW(),
    8,
    1250,
    NOW(),
    NOW()
);

-- Sample Post 2: Social Media Strategy
INSERT INTO blog_posts (
    author_id,
    category_id,
    title_en,
    title_ar,
    slug_en,
    slug_ar,
    excerpt_en,
    excerpt_ar,
    content_en,
    content_ar,
    featured_image,
    meta_description_en,
    meta_description_ar,
    meta_keywords,
    status,
    published_at,
    read_time,
    views,
    created_at,
    updated_at
)
VALUES (
    1,
    1,
    '10 Social Media Marketing Strategies That Actually Work',
    '10 استراتيجيات تسويق عبر وسائل التواصل تعمل فعلاً',
    'social-media-marketing-strategies',
    'social-media-marketing-strategies-ar',
    'Proven social media strategies that will help you grow your brand, engage your audience, and increase conversions.',
    'استراتيجيات مجربة لوسائل التواصل الاجتماعي ستساعدك على تنمية علامتك التجارية وزيادة التفاعل.',
    '<h2>Why Social Media Marketing Matters</h2><p>In today''s digital age, social media is no longer optional for businesses. It''s a necessity. Here are 10 strategies that will transform your social media presence.</p><h3>1. Know Your Audience</h3><p>Understanding your target audience is the foundation of successful social media marketing.</p><h3>2. Create Valuable Content</h3><p>Content is king. Focus on creating value for your audience, not just promoting your products.</p>',
    '<h2>لماذا يهم التسويق عبر وسائل التواصل</h2><p>في العصر الرقمي اليوم، لم تعد وسائل التواصل الاجتماعي اختيارية للشركات. إنها ضرورة.</p>',
    'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=1200&h=600&fit=crop',
    '10 proven social media marketing strategies to grow your brand and increase engagement.',
    '10 استراتيجيات مجربة للتسويق عبر وسائل التواصل الاجتماعي',
    'social media, marketing, Instagram, Facebook, Twitter, LinkedIn',
    'published',
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    6,
    890,
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    DATE_SUB(NOW(), INTERVAL 2 DAY)
);

-- Sample Post 3: Content Marketing
INSERT INTO blog_posts (
    author_id,
    category_id,
    title_en,
    title_ar,
    slug_en,
    slug_ar,
    excerpt_en,
    excerpt_ar,
    content_en,
    content_ar,
    featured_image,
    meta_description_en,
    meta_description_ar,
    meta_keywords,
    status,
    published_at,
    read_time,
    views,
    created_at,
    updated_at
)
VALUES (
    1,
    2,
    'Content Marketing Best Practices for 2025',
    'أفضل ممارسات تسويق المحتوى لعام 2025',
    'content-marketing-best-practices',
    'content-marketing-best-practices-ar',
    'Master the art of content marketing with these proven best practices that drive results.',
    'أتقن فن تسويق المحتوى مع هذه الممارسات المجربة التي تحقق النتائج.',
    '<h2>The Power of Content Marketing</h2><p>Content marketing is one of the most effective ways to attract, engage, and convert your target audience. Here''s how to do it right.</p><h3>Create a Content Strategy</h3><p>Start with a clear strategy that aligns with your business goals.</p><h3>Focus on Quality Over Quantity</h3><p>One great piece of content is worth more than ten mediocre ones.</p>',
    '<h2>قوة تسويق المحتوى</h2><p>تسويق المحتوى هو أحد أكثر الطرق فعالية لجذب جمهورك المستهدف وإشراكه وتحويله.</p>',
    'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&h=600&fit=crop',
    'Learn content marketing best practices that will help you create engaging content and drive results.',
    'تعلم أفضل ممارسات تسويق المحتوى لإنشاء محتوى جذاب',
    'content marketing, blogging, SEO, content strategy',
    'published',
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    7,
    1150,
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    DATE_SUB(NOW(), INTERVAL 5 DAY)
);

-- Sample Post 4: Email Marketing
INSERT INTO blog_posts (
    author_id,
    category_id,
    title_en,
    title_ar,
    slug_en,
    slug_ar,
    excerpt_en,
    excerpt_ar,
    content_en,
    content_ar,
    featured_image,
    meta_description_en,
    meta_description_ar,
    meta_keywords,
    status,
    published_at,
    read_time,
    views,
    created_at,
    updated_at
)
VALUES (
    1,
    2,
    'Email Marketing Automation: Complete Guide',
    'أتمتة التسويق عبر البريد الإلكتروني: دليل كامل',
    'email-marketing-automation-guide',
    'email-marketing-automation-guide-ar',
    'Learn how to automate your email marketing campaigns to save time and increase conversions.',
    'تعلم كيفية أتمتة حملات التسويق عبر البريد الإلكتروني لتوفير الوقت وزيادة التحويلات.',
    '<h2>Why Automate Email Marketing?</h2><p>Email marketing automation allows you to send the right message to the right person at the right time, without manual intervention.</p><h3>Benefits of Automation</h3><ul><li>Save time and resources</li><li>Increase personalization</li><li>Improve conversion rates</li><li>Better customer segmentation</li></ul>',
    '<h2>لماذا أتمتة التسويق عبر البريد الإلكتروني؟</h2><p>تتيح لك أتمتة التسويق عبر البريد الإلكتروني إرسال الرسالة الصحيحة للشخص المناسب في الوقت المناسب.</p>',
    'https://images.unsplash.com/photo-1596526131083-e8c633c948d2?w=1200&h=600&fit=crop',
    'Complete guide to email marketing automation. Learn how to automate campaigns and increase ROI.',
    'دليل كامل لأتمتة التسويق عبر البريد الإلكتروني',
    'email marketing, automation, email campaigns, marketing automation',
    'published',
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    9,
    780,
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    DATE_SUB(NOW(), INTERVAL 7 DAY)
);

-- Sample Post 5: SEO Guide
INSERT INTO blog_posts (
    author_id,
    category_id,
    title_en,
    title_ar,
    slug_en,
    slug_ar,
    excerpt_en,
    excerpt_ar,
    content_en,
    content_ar,
    featured_image,
    meta_description_en,
    meta_description_ar,
    meta_keywords,
    status,
    published_at,
    read_time,
    views,
    created_at,
    updated_at
)
VALUES (
    1,
    3,
    'SEO in 2025: Complete Guide to Ranking #1 on Google',
    'SEO في 2025: دليل كامل للوصول للمركز الأول في جوجل',
    'seo-guide-2025-ranking-google',
    'seo-guide-2025-ranking-google-ar',
    'Everything you need to know about SEO in 2025 to rank higher on Google and drive organic traffic.',
    'كل ما تحتاج معرفته عن SEO في 2025 للحصول على ترتيب أعلى في جوجل.',
    '<h2>SEO Fundamentals</h2><p>Search Engine Optimization (SEO) is constantly evolving. Here''s what you need to know in 2025.</p><h3>On-Page SEO</h3><p>Optimize your content, meta tags, and site structure for better rankings.</p><h3>Technical SEO</h3><p>Ensure your website is fast, mobile-friendly, and crawlable.</p><h3>Link Building</h3><p>Build high-quality backlinks to boost your domain authority.</p>',
    '<h2>أساسيات SEO</h2><p>تحسين محركات البحث (SEO) يتطور باستمرار. إليك ما تحتاج معرفته في 2025.</p>',
    'https://images.unsplash.com/photo-1562577309-4932fdd64cd1?w=1200&h=600&fit=crop',
    'Complete SEO guide for 2025. Learn how to rank #1 on Google and drive organic traffic to your website.',
    'دليل SEO كامل لعام 2025 للحصول على المركز الأول في جوجل',
    'SEO, search engine optimization, Google ranking, organic traffic',
    'published',
    DATE_SUB(NOW(), INTERVAL 10 DAY),
    12,
    2100,
    DATE_SUB(NOW(), INTERVAL 10 DAY),
    DATE_SUB(NOW(), INTERVAL 10 DAY)
);

-- Add tags to posts
INSERT INTO blog_post_tags (post_id, tag_id)
SELECT
    (SELECT id FROM blog_posts WHERE slug_en = 'ai-powered-marketing-guide-2025' LIMIT 1),
    id
FROM blog_tags
WHERE name_en IN ('AI', 'Marketing', 'Digital Marketing')
LIMIT 3;

INSERT INTO blog_post_tags (post_id, tag_id)
SELECT
    (SELECT id FROM blog_posts WHERE slug_en = 'social-media-marketing-strategies' LIMIT 1),
    id
FROM blog_tags
WHERE name_en IN ('Social Media', 'Marketing', 'Strategy')
LIMIT 3;

INSERT INTO blog_post_tags (post_id, tag_id)
SELECT
    (SELECT id FROM blog_posts WHERE slug_en = 'content-marketing-best-practices' LIMIT 1),
    id
FROM blog_tags
WHERE name_en IN ('Content Marketing', 'SEO', 'Strategy')
LIMIT 3;
