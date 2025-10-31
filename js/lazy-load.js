// ========================================
// PYRAMEDIA - Advanced Image Optimization
// Lazy Loading + Responsive Images
// ========================================

class ImageOptimizer {
    constructor() {
        this.options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.01
        };
        this.init();
    }

    init() {
        // Native lazy loading support check
        if ('loading' in HTMLImageElement.prototype) {
            this.useNativeLazyLoading();
        } else {
            this.useIntersectionObserver();
        }

        // Setup responsive images
        this.setupResponsiveImages();

        // Setup background image lazy loading
        this.setupBackgroundLazyLoad();
    }

    /**
     * Use native lazy loading (modern browsers)
     */
    useNativeLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');

        images.forEach(img => {
            img.src = img.dataset.src;
            img.loading = 'lazy';

            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }

            img.classList.remove('lazy');
            img.classList.add('lazy-loaded');
        });
    }

    /**
     * Use Intersection Observer (fallback for older browsers)
     */
    useIntersectionObserver() {
        const images = document.querySelectorAll('img[data-src]');

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    this.loadImage(img);
                    observer.unobserve(img);
                }
            });
        }, this.options);

        images.forEach(img => imageObserver.observe(img));
    }

    /**
     * Load single image
     */
    loadImage(img) {
        // Create a temporary image to preload
        const tempImg = new Image();

        tempImg.onload = () => {
            img.src = img.dataset.src;

            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }

            img.classList.remove('lazy');
            img.classList.add('lazy-loaded');

            // Fade in animation
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.transition = 'opacity 0.3s ease-in-out';
                img.style.opacity = '1';
            }, 10);
        };

        tempImg.onerror = () => {
            console.error('Failed to load image:', img.dataset.src);
            img.classList.add('lazy-error');
        };

        tempImg.src = img.dataset.src;
    }

    /**
     * Setup responsive images with srcset
     */
    setupResponsiveImages() {
        const images = document.querySelectorAll('img[data-sizes]');

        images.forEach(img => {
            if (img.dataset.sizes) {
                img.sizes = img.dataset.sizes;
            }
        });
    }

    /**
     * Lazy load background images
     */
    setupBackgroundLazyLoad() {
        const elements = document.querySelectorAll('[data-bg]');

        const bgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    element.style.backgroundImage = `url('${element.dataset.bg}')`;
                    element.classList.add('bg-loaded');
                    observer.unobserve(element);
                }
            });
        }, this.options);

        elements.forEach(el => bgObserver.observe(el));
    }
}

// ==========================================
// IMAGE PRELOADER
// ==========================================

class ImagePreloader {
    constructor() {
        this.priorityImages = [];
    }

    /**
     * Preload critical images
     */
    preloadCritical() {
        const criticalImages = document.querySelectorAll('img[data-critical="true"]');

        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.dataset.src || img.src;

            if (img.dataset.srcset) {
                link.imagesrcset = img.dataset.srcset;
            }

            document.head.appendChild(link);
        });
    }

    /**
     * Preload next page images (for better UX)
     */
    preloadNext(urls) {
        urls.forEach(url => {
            const img = new Image();
            img.src = url;
        });
    }
}

// ==========================================
// PROGRESSIVE IMAGE LOADER
// ==========================================

class ProgressiveImageLoader {
    /**
     * Load images progressively (blur to sharp)
     */
    static loadProgressive(container) {
        const images = container.querySelectorAll('.progressive-img');

        images.forEach(img => {
            const placeholder = img.dataset.placeholder;
            const fullSize = img.dataset.full;

            // Load placeholder first
            if (placeholder) {
                img.src = placeholder;
                img.style.filter = 'blur(10px)';
            }

            // Load full size
            const fullImg = new Image();
            fullImg.onload = () => {
                img.src = fullSize;
                img.style.filter = 'blur(0)';
                img.style.transition = 'filter 0.3s ease-in-out';
            };
            fullImg.src = fullSize;
        });
    }
}

// ==========================================
// IMAGE ERROR HANDLING
// ==========================================

class ImageErrorHandler {
    static setup() {
        document.addEventListener('error', (e) => {
            if (e.target.tagName === 'IMG') {
                ImageErrorHandler.handleImageError(e.target);
            }
        }, true);
    }

    static handleImageError(img) {
        // Add error class
        img.classList.add('img-error');

        // Try fallback image
        if (img.dataset.fallback) {
            img.src = img.dataset.fallback;
            return;
        }

        // Use placeholder
        img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="400" height="300" fill="%23f0f0f0"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" fill="%23999" font-size="18"%3EImage not available%3C/text%3E%3C/svg%3E';

        // Log error
        console.warn('Image load failed:', img.dataset.src || img.src);
    }
}

// ==========================================
// WEBP SUPPORT DETECTION
// ==========================================

class WebPSupport {
    static check(callback) {
        const webP = new Image();

        webP.onload = webP.onerror = function () {
            callback(webP.height === 2);
        };

        webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
    }

    static addSupportClass() {
        WebPSupport.check((hasWebP) => {
            document.documentElement.classList.add(hasWebP ? 'webp' : 'no-webp');
        });
    }
}

// ==========================================
// RESPONSIVE IMAGE HELPER
// ==========================================

class ResponsiveImageHelper {
    /**
     * Generate srcset attribute
     */
    static generateSrcset(baseUrl, sizes = [320, 640, 768, 1024, 1280, 1920]) {
        return sizes.map(size => `${baseUrl}?w=${size} ${size}w`).join(', ');
    }

    /**
     * Generate sizes attribute
     */
    static generateSizes(breakpoints = {
        mobile: '100vw',
        tablet: '50vw',
        desktop: '33vw'
    }) {
        return `
            (max-width: 640px) ${breakpoints.mobile},
            (max-width: 1024px) ${breakpoints.tablet},
            ${breakpoints.desktop}
        `.trim();
    }

    /**
     * Convert image to WebP (if supported)
     */
    static toWebP(src) {
        if (document.documentElement.classList.contains('webp')) {
            return src.replace(/\.(jpg|jpeg|png)$/i, '.webp');
        }
        return src;
    }
}

// ==========================================
// PERFORMANCE MONITORING
// ==========================================

class ImagePerformanceMonitor {
    static measureImageLoad() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.initiatorType === 'img') {
                        console.log('Image loaded:', {
                            url: entry.name,
                            duration: entry.duration.toFixed(2) + 'ms',
                            size: entry.transferSize
                        });
                    }
                }
            });

            observer.observe({ entryTypes: ['resource'] });
        }
    }
}

// ==========================================
// INITIALIZE
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize lazy loading
    const imageOptimizer = new ImageOptimizer();

    // Preload critical images
    const preloader = new ImagePreloader();
    preloader.preloadCritical();

    // Setup error handling
    ImageErrorHandler.setup();

    // Detect WebP support
    WebPSupport.addSupportClass();

    // Monitor performance (development only)
    if (window.location.hostname === 'localhost' || window.location.search.includes('debug')) {
        ImagePerformanceMonitor.measureImageLoad();
    }

    console.log('✅ Image optimization initialized');
});

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

/**
 * Convert regular image to lazy loadable
 */
function convertToLazyImage(img) {
    if (img.src) {
        img.dataset.src = img.src;
        img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E';
        img.classList.add('lazy');
    }
}

/**
 * Batch convert images to lazy load
 */
function batchConvertToLazy(selector = 'img:not([data-critical="true"])') {
    const images = document.querySelectorAll(selector);
    images.forEach(convertToLazyImage);
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ImageOptimizer,
        ImagePreloader,
        ProgressiveImageLoader,
        ResponsiveImageHelper,
        WebPSupport
    };
}
