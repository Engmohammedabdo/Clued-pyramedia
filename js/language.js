// ========================================
// PYRAMEDIA - Language Management System
// ========================================

class LanguageManager {
    constructor() {
        this.defaultLanguage = 'en';
        this.currentLanguage = this.detectLanguage();
        this.init();
    }

    /**
     * Initialize language manager
     */
    init() {
        // Apply saved language on page load
        this.applyLanguage();

        // Listen for language changes
        this.setupLanguageListeners();

        // Auto-redirect if needed (optional)
        this.autoRedirect();
    }

    /**
     * Detect current language from URL, localStorage, or browser
     */
    detectLanguage() {
        // 1. Check localStorage
        const savedLang = localStorage.getItem('pyramedia_language');
        if (savedLang) {
            return savedLang;
        }

        // 2. Check current page URL
        const currentPage = window.location.pathname;
        if (currentPage.includes('index-ar.html') || currentPage.includes('/ar/')) {
            return 'ar';
        }

        // 3. Check browser language
        const browserLang = navigator.language || navigator.userLanguage;
        if (browserLang.startsWith('ar')) {
            return 'ar';
        }

        // 4. Default to English
        return this.defaultLanguage;
    }

    /**
     * Apply language settings
     */
    applyLanguage() {
        const html = document.documentElement;

        if (this.currentLanguage === 'ar') {
            html.setAttribute('lang', 'ar');
            html.setAttribute('dir', 'rtl');
            document.body.classList.add('rtl');
        } else {
            html.setAttribute('lang', 'en');
            html.setAttribute('dir', 'ltr');
            document.body.classList.remove('rtl');
        }

        // Save to localStorage
        localStorage.setItem('pyramedia_language', this.currentLanguage);
    }

    /**
     * Setup language switcher listeners
     */
    setupLanguageListeners() {
        const languageSwitchers = document.querySelectorAll('[data-language-switcher]');

        languageSwitchers.forEach(switcher => {
            switcher.addEventListener('click', (e) => {
                e.preventDefault();
                const targetLang = switcher.dataset.languageSwitcher;
                this.switchLanguage(targetLang);
            });
        });
    }

    /**
     * Switch to a specific language
     */
    switchLanguage(language) {
        if (language === this.currentLanguage) {
            return;
        }

        // Save preference
        localStorage.setItem('pyramedia_language', language);

        // Redirect to appropriate page
        if (language === 'ar') {
            window.location.href = 'index-ar.html';
        } else {
            window.location.href = 'index.html';
        }
    }

    /**
     * Auto-redirect based on saved preference (optional)
     */
    autoRedirect() {
        const savedLang = localStorage.getItem('pyramedia_language');
        const currentPage = window.location.pathname;

        // Only auto-redirect on homepage
        if (currentPage === '/' || currentPage === '/index.html' || currentPage.endsWith('/')) {
            if (savedLang === 'ar' && !currentPage.includes('-ar')) {
                // Redirect to Arabic version
                window.location.href = 'index-ar.html';
            }
        } else if (currentPage.includes('index-ar.html')) {
            if (savedLang === 'en') {
                // Redirect to English version
                window.location.href = 'index.html';
            }
        }
    }

    /**
     * Get current language
     */
    getCurrentLanguage() {
        return this.currentLanguage;
    }

    /**
     * Check if current language is RTL
     */
    isRTL() {
        return this.currentLanguage === 'ar';
    }
}

// ========================================
// Translations Object (for dynamic content)
// ========================================

const translations = {
    en: {
        // Navigation
        home: 'Home',
        about: 'About',
        services: 'Services',
        caseStudies: 'Case Studies',
        blog: 'Blog',
        contact: 'Contact',

        // Buttons
        bookConsultation: 'Book Free Consultation',
        learnMore: 'Learn More',
        readMore: 'Read More',
        viewAll: 'View All',
        sendMessage: 'Send Message',
        submit: 'Submit',

        // Hero
        heroTagline: 'BE WITH US, BE UNIQUE',
        heroTitle1: 'Transform Your',
        heroTitle2: 'Digital Presence',
        heroDescription: 'Leading marketing & media agency in the GCC region. We empower youth through the power of media and innovation.',

        // Stats
        projectsCompleted: 'Projects Completed',
        happyClients: 'Happy Clients',
        yearsExperience: 'Years Experience',
        clientSatisfaction: 'Client Satisfaction',

        // About
        aboutTitle: 'About PYRAMEDIA',
        ourVision: 'Our Vision',
        visionText: 'We aim to position ourselves as one of the leading media and marketing agencies in the GCC region by 2025',
        ourMission: 'Our Mission',
        missionText: 'To empower the youth through the power of media and innovation, bringing your brand to the people and the people to your brand.',

        // Footer
        copyrightText: '© 2025 PYRAMEDIA. All rights reserved.',
        privacyPolicy: 'Privacy Policy',
        termsOfService: 'Terms of Service',

        // Forms
        name: 'Your Name',
        email: 'Email Address',
        phone: 'Phone Number',
        message: 'Your Message',
        companyName: 'Company Name',

        // Messages
        successMessage: 'Thank you! We will get back to you soon.',
        errorMessage: 'An error occurred. Please try again.',
        validationError: 'Please fill all required fields.',
    },
    ar: {
        // Navigation
        home: 'الرئيسية',
        about: 'من نحن',
        services: 'الخدمات',
        caseStudies: 'دراسات الحالة',
        blog: 'المدونة',
        contact: 'التواصل',

        // Buttons
        bookConsultation: 'احجز استشارة مجانية',
        learnMore: 'اعرف المزيد',
        readMore: 'اقرأ المزيد',
        viewAll: 'عرض الكل',
        sendMessage: 'إرسال الرسالة',
        submit: 'إرسال',

        // Hero
        heroTagline: 'كن معنا، كن مميزاً',
        heroTitle1: 'حوّل حضورك',
        heroTitle2: 'الرقمي',
        heroDescription: 'وكالة رائدة في التسويق والإعلام في منطقة الخليج. نمكّن الشباب من خلال قوة الإعلام والابتكار.',

        // Stats
        projectsCompleted: 'مشروع مكتمل',
        happyClients: 'عميل سعيد',
        yearsExperience: 'سنة خبرة',
        clientSatisfaction: 'رضا العملاء',

        // About
        aboutTitle: 'عن بيراميديا',
        ourVision: 'رؤيتنا',
        visionText: 'نهدف إلى أن نكون من الوكالات الرائدة في مجال الإعلام والتسويق في منطقة الخليج بحلول عام 2025',
        ourMission: 'رسالتنا',
        missionText: 'تمكين الشباب من خلال قوة الإعلام والابتكار، نوصل علامتك التجارية للناس والناس لعلامتك التجارية.',

        // Footer
        copyrightText: '© 2025 بيراميديا. جميع الحقوق محفوظة.',
        privacyPolicy: 'سياسة الخصوصية',
        termsOfService: 'شروط الخدمة',

        // Forms
        name: 'الاسم',
        email: 'البريد الإلكتروني',
        phone: 'رقم الهاتف',
        message: 'رسالتك',
        companyName: 'اسم الشركة',

        // Messages
        successMessage: 'شكراً لك! سنتواصل معك قريباً.',
        errorMessage: 'حدث خطأ. يرجى المحاولة مرة أخرى.',
        validationError: 'يرجى ملء جميع الحقول المطلوبة.',
    }
};

// ========================================
// Translation Helper Functions
// ========================================

/**
 * Get translation for a key
 */
function t(key, lang = null) {
    const language = lang || languageManager.getCurrentLanguage();
    return translations[language][key] || key;
}

/**
 * Translate all elements with data-i18n attribute
 */
function translatePage() {
    const lang = languageManager.getCurrentLanguage();
    const elements = document.querySelectorAll('[data-i18n]');

    elements.forEach(element => {
        const key = element.dataset.i18n;
        if (translations[lang][key]) {
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.placeholder = translations[lang][key];
            } else {
                element.textContent = translations[lang][key];
            }
        }
    });
}

/**
 * Format numbers based on language
 */
function formatNumber(number, lang = null) {
    const language = lang || languageManager.getCurrentLanguage();

    if (language === 'ar') {
        // Convert to Arabic-Indic numerals
        const arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        return String(number).replace(/\d/g, digit => arabicNumerals[digit]);
    }

    return String(number);
}

/**
 * Format date based on language
 */
function formatDate(date, lang = null) {
    const language = lang || languageManager.getCurrentLanguage();
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
    };

    const locale = language === 'ar' ? 'ar-AE' : 'en-US';
    return new Intl.DateTimeFormat(locale, options).format(date);
}

// ========================================
// Initialize Language Manager
// ========================================

let languageManager;

document.addEventListener('DOMContentLoaded', () => {
    languageManager = new LanguageManager();

    // Translate dynamic content (if needed)
    // translatePage();

    console.log(`Language initialized: ${languageManager.getCurrentLanguage()}`);
});

// ========================================
// Export for use in other scripts
// ========================================

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        LanguageManager,
        translations,
        t,
        translatePage,
        formatNumber,
        formatDate
    };
}
