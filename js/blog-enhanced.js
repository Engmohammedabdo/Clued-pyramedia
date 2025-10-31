// ========================================
// PYRAMEDIA - Enhanced Blog Features
// Advanced blog functionality
// ========================================

class BlogEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.setupReadingProgress();
        this.setupTableOfContents();
        this.setupCodeHighlighting();
        this.setupSocialSharing();
        this.setupBookmarking();
        this.setupPrintMode();
    }

    // ==========================================
    // READING PROGRESS BAR
    // ==========================================
    setupReadingProgress() {
        const progressBar = document.getElementById('readingProgress');
        if (!progressBar) return;

        window.addEventListener('scroll', () => {
            const article = document.querySelector('article');
            if (!article) return;

            const windowHeight = window.innerHeight;
            const articleHeight = article.scrollHeight;
            const scrollTop = window.scrollY;
            const articleTop = article.offsetTop;

            const progress = ((scrollTop - articleTop) / (articleHeight - windowHeight)) * 100;
            const clampedProgress = Math.min(Math.max(progress, 0), 100);

            progressBar.style.width = clampedProgress + '%';
        });
    }

    // ==========================================
    // AUTOMATIC TABLE OF CONTENTS
    // ==========================================
    setupTableOfContents() {
        const article = document.querySelector('.article-content');
        const tocContainer = document.getElementById('tableOfContents');

        if (!article || !tocContainer) return;

        const headings = article.querySelectorAll('h2, h3');
        if (headings.length < 3) {
            tocContainer.style.display = 'none';
            return;
        }

        let tocHTML = '<ul class="space-y-2">';
        headings.forEach((heading, index) => {
            const id = `heading-${index}`;
            heading.id = id;

            const level = heading.tagName === 'H2' ? '' : 'ml-4';
            tocHTML += `
                <li class="${level}">
                    <a href="#${id}" class="text-gray-700 hover:text-orange-500 transition-colors">
                        ${heading.textContent}
                    </a>
                </li>
            `;
        });
        tocHTML += '</ul>';

        tocContainer.innerHTML = `
            <div class="bg-orange-50 rounded-lg p-6 mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-list text-orange-500 mr-2"></i>
                    Table of Contents
                </h3>
                ${tocHTML}
            </div>
        `;

        // Smooth scroll to headings
        tocContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    // ==========================================
    // CODE SYNTAX HIGHLIGHTING
    // ==========================================
    setupCodeHighlighting() {
        const codeBlocks = document.querySelectorAll('pre code');
        codeBlocks.forEach(block => {
            // Add copy button
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const copyBtn = document.createElement('button');
            copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
            copyBtn.className = 'absolute top-2 right-2 px-3 py-1 bg-gray-700 text-white text-sm rounded hover:bg-gray-600 transition-colors';

            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(block.textContent).then(() => {
                    copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                    }, 2000);
                });
            });

            block.parentNode.parentNode.insertBefore(wrapper, block.parentNode);
            wrapper.appendChild(copyBtn);
            wrapper.appendChild(block.parentNode);
        });
    }

    // ==========================================
    // ENHANCED SOCIAL SHARING
    // ==========================================
    setupSocialSharing() {
        this.setupShareButtons();
        this.setupNativeShare();
    }

    setupShareButtons() {
        // Twitter share
        window.shareOnTwitter = () => {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.querySelector('h1').textContent);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
        };

        // Facebook share
        window.shareOnFacebook = () => {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        };

        // LinkedIn share
        window.shareOnLinkedIn = () => {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
        };

        // WhatsApp share
        window.shareOnWhatsApp = () => {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.querySelector('h1').textContent);
            window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
        };

        // Copy link
        window.copyLink = () => {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const btn = event.target.closest('button');
                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                btn.classList.add('bg-green-600');
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.remove('bg-green-600');
                }, 2000);
            });
        };
    }

    setupNativeShare() {
        if (navigator.share) {
            const shareBtn = document.getElementById('nativeShareBtn');
            if (shareBtn) {
                shareBtn.classList.remove('hidden');
                shareBtn.addEventListener('click', async () => {
                    try {
                        await navigator.share({
                            title: document.querySelector('h1').textContent,
                            text: document.querySelector('meta[name="description"]')?.content || '',
                            url: window.location.href
                        });
                    } catch (err) {
                        console.log('Share cancelled or failed');
                    }
                });
            }
        }
    }

    // ==========================================
    // BOOKMARKING SYSTEM
    // ==========================================
    setupBookmarking() {
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        if (!bookmarkBtn) return;

        const postSlug = new URLSearchParams(window.location.search).get('slug');
        const bookmarks = JSON.parse(localStorage.getItem('blog_bookmarks') || '[]');

        const updateBookmarkUI = (isBookmarked) => {
            if (isBookmarked) {
                bookmarkBtn.innerHTML = '<i class="fas fa-bookmark mr-2"></i>Bookmarked';
                bookmarkBtn.classList.add('bg-orange-500', 'text-white');
                bookmarkBtn.classList.remove('bg-gray-200', 'text-gray-700');
            } else {
                bookmarkBtn.innerHTML = '<i class="far fa-bookmark mr-2"></i>Bookmark';
                bookmarkBtn.classList.add('bg-gray-200', 'text-gray-700');
                bookmarkBtn.classList.remove('bg-orange-500', 'text-white');
            }
        };

        updateBookmarkUI(bookmarks.includes(postSlug));

        bookmarkBtn.addEventListener('click', () => {
            const index = bookmarks.indexOf(postSlug);
            if (index > -1) {
                bookmarks.splice(index, 1);
                updateBookmarkUI(false);
            } else {
                bookmarks.push(postSlug);
                updateBookmarkUI(true);
            }
            localStorage.setItem('blog_bookmarks', JSON.stringify(bookmarks));
        });
    }

    // ==========================================
    // PRINT MODE
    // ==========================================
    setupPrintMode() {
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', () => {
                window.print();
            });
        }
    }
}

// ==========================================
// ESTIMATED READING TIME
// ==========================================
function calculateReadingTime() {
    const article = document.querySelector('.article-content');
    if (!article) return;

    const text = article.textContent;
    const wordsPerMinute = 200;
    const words = text.trim().split(/\s+/).length;
    const readTime = Math.ceil(words / wordsPerMinute);

    const readTimeElement = document.getElementById('readTime');
    if (readTimeElement) {
        readTimeElement.textContent = `${readTime} min read`;
    }
}

// ==========================================
// LAZY LOADING IMAGES
// ==========================================
function setupLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// ==========================================
// BACK TO TOP BUTTON
// ==========================================
function setupBackToTop() {
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'fixed bottom-8 right-8 w-12 h-12 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 transition-all opacity-0 pointer-events-none';
    backToTopBtn.id = 'backToTop';
    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
        }
    });

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ==========================================
// INITIALIZE
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    new BlogEnhancements();
    calculateReadingTime();
    setupLazyLoading();
    setupBackToTop();
});
