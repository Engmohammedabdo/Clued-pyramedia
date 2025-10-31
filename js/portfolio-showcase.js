// ========================================
// PYRAMEDIA - Portfolio Showcase Manager
// Modern, filterable portfolio with lightbox
// ========================================

class PortfolioShowcase {
    constructor() {
        this.currentFilter = 'all';
        this.currentLang = this.detectLanguage();
        this.lightbox = null;
        this.projects = this.getProjects();
        this.init();
    }

    detectLanguage() {
        const path = window.location.pathname;
        return path.includes('-ar.html') || path.includes('/ar/') ? 'ar' : 'en';
    }

    getProjects() {
        const projects = [
            {
                id: 1,
                title: {
                    en: "300% E-Commerce Growth",
                    ar: "نمو 300% في التجارة الإلكترونية"
                },
                client: {
                    en: "Gulf Retail Group",
                    ar: "مجموعة الخليج للتجزئة"
                },
                category: "ecommerce",
                categoryName: {
                    en: "E-Commerce",
                    ar: "التجارة الإلكترونية"
                },
                description: {
                    en: "Transformed online presence for leading UAE retailer, implementing advanced marketing automation and personalized customer journeys.",
                    ar: "حولنا الحضور الرقمي لأكبر بائع تجزئة في الإمارات، من خلال تطبيق أتمتة تسويقية متقدمة ورحلات عملاء مخصصة."
                },
                metrics: [
                    { label: { en: "Sales Growth", ar: "نمو المبيعات" }, value: "300%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "6M" },
                    { label: { en: "New Customers", ar: "عملاء جدد" }, value: "50K+" },
                    { label: { en: "ROI", ar: "عائد الاستثمار" }, value: "450%" }
                ],
                image: "https://images.unsplash.com/photo-1556742111-a301076d9d18?w=800&h=600&fit=crop",
                tags: ["Marketing Automation", "SEO", "Social Media", "Email Campaigns"]
            },
            {
                id: 2,
                title: {
                    en: "Complete Brand Transformation",
                    ar: "تحويل العلامة التجارية الكامل"
                },
                client: {
                    en: "TechVision Startup",
                    ar: "شركة تيك فيجن الناشئة"
                },
                category: "branding",
                categoryName: {
                    en: "Branding",
                    ar: "العلامات التجارية"
                },
                description: {
                    en: "Elevated brand identity for innovative tech startup, creating cohesive visual language across all touchpoints.",
                    ar: "رفعنا هوية العلامة التجارية لشركة تقنية مبتكرة، وأنشأنا لغة بصرية متماسكة عبر جميع نقاط الاتصال."
                },
                metrics: [
                    { label: { en: "Brand Awareness", ar: "الوعي بالعلامة" }, value: "250%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "12M" },
                    { label: { en: "Market Position", ar: "المركز السوقي" }, value: "#1" },
                    { label: { en: "Engagement", ar: "التفاعل" }, value: "+180%" }
                ],
                image: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop",
                tags: ["Brand Strategy", "Logo Design", "Visual Identity", "Brand Guidelines"]
            },
            {
                id: 3,
                title: {
                    en: "Marketing Automation Success",
                    ar: "نجاح أتمتة التسويق"
                },
                client: {
                    en: "CloudTech SaaS",
                    ar: "كلاود تيك ساس"
                },
                category: "automation",
                categoryName: {
                    en: "Automation",
                    ar: "الأتمتة"
                },
                description: {
                    en: "Implemented comprehensive marketing automation system, reducing manual work by 80% while increasing conversion rates.",
                    ar: "نفذنا نظام أتمتة تسويقية شامل، مما قلل العمل اليدوي بنسبة 80٪ مع زيادة معدلات التحويل."
                },
                metrics: [
                    { label: { en: "Time Saved", ar: "الوقت الموفر" }, value: "80%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "3M" },
                    { label: { en: "Cost Savings", ar: "التوفير" }, value: "$500K" },
                    { label: { en: "Conversion", ar: "التحويل" }, value: "+220%" }
                ],
                image: "https://images.unsplash.com/photo-1518186285589-2f7649de83e0?w=800&h=600&fit=crop",
                tags: ["HubSpot", "Zapier", "Email Automation", "Lead Scoring"]
            },
            {
                id: 4,
                title: {
                    en: "Social Media Dominance",
                    ar: "الهيمنة على وسائل التواصل"
                },
                client: {
                    en: "Fashion Forward Boutique",
                    ar: "بوتيك فاشن فورورد"
                },
                category: "social",
                categoryName: {
                    en: "Social Media",
                    ar: "وسائل التواصل"
                },
                description: {
                    en: "Created viral social media campaigns that transformed a local boutique into regional fashion influencer.",
                    ar: "أنشأنا حملات وسائل تواصل اجتماعي انتشرت بسرعة حولت بوتيك محلي إلى مؤثر أزياء إقليمي."
                },
                metrics: [
                    { label: { en: "Followers", ar: "المتابعون" }, value: "500K+" },
                    { label: { en: "Engagement", ar: "التفاعل" }, value: "+380%" },
                    { label: { en: "Reach", ar: "الوصول" }, value: "2M+" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "8M" }
                ],
                image: "https://images.unsplash.com/photo-1611926653458-09294b3142bf?w=800&h=600&fit=crop",
                tags: ["Instagram", "TikTok", "Influencer Marketing", "Content Creation"]
            },
            {
                id: 5,
                title: {
                    en: "SEO & Content Strategy Victory",
                    ar: "انتصار استراتيجية SEO والمحتوى"
                },
                client: {
                    en: "Real Estate Dubai",
                    ar: "دبي للعقارات"
                },
                category: "seo",
                categoryName: {
                    en: "SEO",
                    ar: "تحسين محركات البحث"
                },
                description: {
                    en: "Dominated search rankings for competitive real estate keywords, driving massive organic traffic growth.",
                    ar: "سيطرنا على تصنيفات البحث لكلمات مفتاحية عقارية تنافسية، مما أدى إلى نمو هائل في حركة المرور العضوية."
                },
                metrics: [
                    { label: { en: "Keywords Top 3", ar: "كلمات في المراكز الأولى" }, value: "150+" },
                    { label: { en: "Organic Traffic", ar: "حركة المرور" }, value: "+420%" },
                    { label: { en: "Leads", ar: "العملاء المحتملون" }, value: "3K+/mo" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "10M" }
                ],
                image: "https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop",
                tags: ["Technical SEO", "Content Marketing", "Link Building", "Local SEO"]
            },
            {
                id: 6,
                title: {
                    en: "Video Marketing Excellence",
                    ar: "التميز في التسويق بالفيديو"
                },
                client: {
                    en: "Luxury Lifestyle UAE",
                    ar: "نمط الحياة الفاخر الإمارات"
                },
                category: "video",
                categoryName: {
                    en: "Video Production",
                    ar: "إنتاج الفيديو"
                },
                description: {
                    en: "Produced high-end video content that generated millions of views and transformed brand perception.",
                    ar: "أنتجنا محتوى فيديو راقي حقق ملايين المشاهدات وحوّل صورة العلامة التجارية."
                },
                metrics: [
                    { label: { en: "Total Views", ar: "مجموع المشاهدات" }, value: "5M+" },
                    { label: { en: "Videos Produced", ar: "فيديوهات منتجة" }, value: "50+" },
                    { label: { en: "Engagement Rate", ar: "معدل التفاعل" }, value: "12%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "12M" }
                ],
                image: "https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800&h=600&fit=crop",
                tags: ["Video Production", "Storytelling", "YouTube Marketing", "Editing"]
            },
            {
                id: 7,
                title: {
                    en: "AI-Powered Campaign",
                    ar: "حملة مدعومة بالذكاء الاصطناعي"
                },
                client: {
                    en: "FinTech Innovators",
                    ar: "مبتكرو التكنولوجيا المالية"
                },
                category: "automation",
                categoryName: {
                    en: "AI & Automation",
                    ar: "الذكاء الاصطناعي والأتمتة"
                },
                description: {
                    en: "Leveraged AI and machine learning to optimize ad spend and targeting, achieving unprecedented ROI.",
                    ar: "استفدنا من الذكاء الاصطناعي والتعلم الآلي لتحسين الإنفاق الإعلاني والاستهداف، محققين عائد استثمار غير مسبوق."
                },
                metrics: [
                    { label: { en: "ROI", ar: "عائد الاستثمار" }, value: "680%" },
                    { label: { en: "CPA Reduction", ar: "تخفيض التكلفة" }, value: "-65%" },
                    { label: { en: "Conversions", ar: "التحويلات" }, value: "+340%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "5M" }
                ],
                image: "https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop",
                tags: ["AI Marketing", "Machine Learning", "Predictive Analytics", "ChatGPT"]
            },
            {
                id: 8,
                title: {
                    en: "Multi-Channel Integration",
                    ar: "التكامل متعدد القنوات"
                },
                client: {
                    en: "Retail Chain Qatar",
                    ar: "سلسلة التجزئة قطر"
                },
                category: "ecommerce",
                categoryName: {
                    en: "Omnichannel",
                    ar: "متعدد القنوات"
                },
                description: {
                    en: "Unified online and offline experiences, creating seamless customer journey across all touchpoints.",
                    ar: "وحدنا التجارب عبر الإنترنت وخارجها، مما أنشأ رحلة عملاء سلسة عبر جميع نقاط الاتصال."
                },
                metrics: [
                    { label: { en: "Customer Retention", ar: "الاحتفاظ بالعملاء" }, value: "+85%" },
                    { label: { en: "Avg. Order Value", ar: "متوسط قيمة الطلب" }, value: "+120%" },
                    { label: { en: "Store Visits", ar: "زيارات المتجر" }, value: "+200%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "14M" }
                ],
                image: "https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop",
                tags: ["Omnichannel", "CRM Integration", "POS Systems", "Mobile App"]
            },
            {
                id: 9,
                title: {
                    en: "Influencer Marketing Triumph",
                    ar: "انتصار التسويق بالمؤثرين"
                },
                client: {
                    en: "Beauty & Wellness Brand",
                    ar: "علامة الجمال والعافية"
                },
                category: "social",
                categoryName: {
                    en: "Influencer Marketing",
                    ar: "التسويق بالمؤثرين"
                },
                description: {
                    en: "Partnered with regional influencers to create authentic campaigns that resonated with target audience.",
                    ar: "شراكة مع مؤثرين إقليميين لإنشاء حملات أصيلة لاقت صدى لدى الجمهور المستهدف."
                },
                metrics: [
                    { label: { en: "Campaign Reach", ar: "وصول الحملة" }, value: "8M+" },
                    { label: { en: "Influencers", ar: "المؤثرون" }, value: "25+" },
                    { label: { en: "Product Sales", ar: "مبيعات المنتج" }, value: "+450%" },
                    { label: { en: "Timeline", ar: "المدة الزمنية" }, value: "6M" }
                ],
                image: "https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=600&fit=crop",
                tags: ["Influencer Partnerships", "Campaign Management", "ROI Tracking", "Content"]
            }
        ];

        return projects;
    }

    init() {
        this.renderFilters();
        this.renderProjects();
        this.setupEventListeners();
        this.createLightbox();
    }

    renderFilters() {
        const container = document.getElementById('portfolioFilters');
        if (!container) return;

        const categories = [
            { id: 'all', name: { en: 'All Projects', ar: 'جميع المشاريع' } },
            { id: 'ecommerce', name: { en: 'E-Commerce', ar: 'التجارة الإلكترونية' } },
            { id: 'branding', name: { en: 'Branding', ar: 'العلامات التجارية' } },
            { id: 'automation', name: { en: 'Automation', ar: 'الأتمتة' } },
            { id: 'social', name: { en: 'Social Media', ar: 'وسائل التواصل' } },
            { id: 'seo', name: { en: 'SEO', ar: 'تحسين محركات البحث' } },
            { id: 'video', name: { en: 'Video', ar: 'الفيديو' } }
        ];

        const html = categories.map(cat => `
            <button
                class="filter-btn ${cat.id === 'all' ? 'active' : ''} px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:shadow-lg"
                data-filter="${cat.id}">
                ${cat.name[this.currentLang]}
            </button>
        `).join('');

        container.innerHTML = html;
    }

    renderProjects(filter = 'all') {
        const container = document.getElementById('portfolioGrid');
        if (!container) return;

        const filteredProjects = filter === 'all'
            ? this.projects
            : this.projects.filter(p => p.category === filter);

        const html = filteredProjects.map(project => `
            <div class="portfolio-item" data-category="${project.category}" data-id="${project.id}">
                <div class="portfolio-card-modern group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <!-- Image Section -->
                    <div class="relative overflow-hidden h-64">
                        <img
                            src="${project.image}"
                            alt="${project.title[this.currentLang]}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">

                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                            <button class="view-project-btn transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500 bg-white text-orange-500 px-6 py-3 rounded-full font-semibold hover:bg-orange-500 hover:text-white flex items-center gap-2">
                                <i class="fas fa-eye"></i>
                                <span>${this.currentLang === 'en' ? 'View Details' : 'عرض التفاصيل'}</span>
                            </button>
                        </div>

                        <!-- Category Badge -->
                        <span class="absolute top-4 ${this.currentLang === 'ar' ? 'right-4' : 'left-4'} gradient-bg text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                            ${project.categoryName[this.currentLang]}
                        </span>
                    </div>

                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- Client -->
                        <p class="text-orange-500 text-sm font-semibold mb-2">
                            ${project.client[this.currentLang]}
                        </p>

                        <!-- Title -->
                        <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-orange-500 transition-colors">
                            ${project.title[this.currentLang]}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            ${project.description[this.currentLang]}
                        </p>

                        <!-- Metrics -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            ${project.metrics.slice(0, 2).map(metric => `
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-3 rounded-lg text-center">
                                    <p class="text-xl font-bold text-orange-600">${metric.value}</p>
                                    <p class="text-xs text-gray-600">${metric.label[this.currentLang]}</p>
                                </div>
                            `).join('')}
                        </div>

                        <!-- View Button -->
                        <button class="view-project-btn w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-arrow-right ${this.currentLang === 'ar' ? 'rotate-180' : ''}"></i>
                            ${this.currentLang === 'en' ? 'View Case Study' : 'عرض دراسة الحالة'}
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Animate out
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';

        setTimeout(() => {
            container.innerHTML = html;
            // Animate in
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
        }, 300);
    }

    setupEventListeners() {
        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;

                // Update active state
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');

                // Filter projects
                this.currentFilter = filter;
                this.renderProjects(filter);
            });
        });

        // View project buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.view-project-btn')) {
                const card = e.target.closest('.portfolio-item');
                const projectId = parseInt(card.dataset.id);
                this.showProjectDetail(projectId);
            }
        });
    }

    createLightbox() {
        const lightboxHTML = `
            <div id="portfolioLightbox" class="fixed inset-0 bg-black/90 z-[9999] hidden items-center justify-center p-4 backdrop-blur-sm">
                <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto relative animate-scale-in">
                    <!-- Close Button -->
                    <button class="close-lightbox absolute top-4 ${this.currentLang === 'ar' ? 'left-4' : 'right-4'} w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all z-10">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    <!-- Content -->
                    <div id="lightboxContent"></div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
        this.lightbox = document.getElementById('portfolioLightbox');

        // Close button
        this.lightbox.querySelector('.close-lightbox').addEventListener('click', () => {
            this.hideLightbox();
        });

        // Close on backdrop click
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.hideLightbox();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.lightbox.classList.contains('hidden')) {
                this.hideLightbox();
            }
        });
    }

    showProjectDetail(projectId) {
        const project = this.projects.find(p => p.id === projectId);
        if (!project) return;

        const content = document.getElementById('lightboxContent');
        content.innerHTML = `
            <!-- Hero Image -->
            <div class="relative h-80 overflow-hidden">
                <img src="${project.image}" alt="${project.title[this.currentLang]}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-8 ${this.currentLang === 'ar' ? 'right-8' : 'left-8'}">
                    <span class="gradient-bg text-white px-4 py-2 rounded-full text-sm font-semibold inline-block mb-3">
                        ${project.categoryName[this.currentLang]}
                    </span>
                    <h2 class="text-4xl font-bold text-white">${project.title[this.currentLang]}</h2>
                    <p class="text-white/90 text-lg mt-2">${project.client[this.currentLang]}</p>
                </div>
            </div>

            <!-- Details -->
            <div class="p-8">
                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold mb-4">${this.currentLang === 'en' ? 'Project Overview' : 'نظرة عامة على المشروع'}</h3>
                    <p class="text-gray-700 text-lg leading-relaxed">${project.description[this.currentLang]}</p>
                </div>

                <!-- Metrics Grid -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold mb-4">${this.currentLang === 'en' ? 'Key Results' : 'النتائج الرئيسية'}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        ${project.metrics.map(metric => `
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl text-center">
                                <p class="text-3xl font-bold text-orange-600 mb-2">${metric.value}</p>
                                <p class="text-sm text-gray-700 font-semibold">${metric.label[this.currentLang]}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <!-- Tags -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold mb-4">${this.currentLang === 'en' ? 'Services Provided' : 'الخدمات المقدمة'}</h3>
                    <div class="flex flex-wrap gap-3">
                        ${project.tags.map(tag => `
                            <span class="bg-orange-100 text-orange-600 px-4 py-2 rounded-full text-sm font-semibold">
                                ${tag}
                            </span>
                        `).join('')}
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center pt-6 border-t">
                    <p class="text-gray-600 mb-4">${this.currentLang === 'en' ? 'Want similar results for your business?' : 'تريد نتائج مماثلة لعملك؟'}</p>
                    <a href="#contact" class="inline-block gradient-bg text-white px-8 py-4 rounded-lg font-semibold text-lg hover:shadow-2xl transition-all transform hover:scale-105">
                        <i class="fas fa-comments ${this.currentLang === 'ar' ? 'ml-2' : 'mr-2'}"></i>
                        ${this.currentLang === 'en' ? 'Start Your Project' : 'ابدأ مشروعك'}
                    </a>
                </div>
            </div>
        `;

        this.lightbox.classList.remove('hidden');
        this.lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    hideLightbox() {
        this.lightbox.classList.add('hidden');
        this.lightbox.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('portfolioGrid')) {
        window.portfolioShowcase = new PortfolioShowcase();
    }
});
