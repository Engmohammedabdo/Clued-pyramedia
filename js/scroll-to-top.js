// ========================================
// PYRAMEDIA - Scroll to Top Button
// ========================================

class ScrollToTop {
    constructor(options = {}) {
        this.options = {
            showAt: options.showAt || 300,
            scrollDuration: options.scrollDuration || 1000,
            position: options.position || 'bottom-right', // bottom-right, bottom-left, bottom-center
            showProgress: options.showProgress !== false,
            style: options.style || 'rocket', // rocket, arrow, circle
            ...options
        };

        this.button = null;
        this.progressRing = null;
        this.isVisible = false;
        this.init();
    }

    init() {
        this.createButton();
        this.addStyles();
        this.attachEvents();
    }

    createButton() {
        const button = document.createElement('button');
        button.id = 'scroll-to-top';
        button.className = `scroll-to-top ${this.options.position} ${this.options.style}`;
        button.setAttribute('aria-label', 'Scroll to top');
        button.setAttribute('title', 'Back to top');

        const icons = {
            rocket: '<i class="fas fa-rocket"></i>',
            arrow: '<i class="fas fa-arrow-up"></i>',
            circle: '<i class="fas fa-chevron-up"></i>'
        };

        button.innerHTML = `
            ${this.options.showProgress ? `
                <svg class="progress-ring" viewBox="0 0 60 60">
                    <circle class="progress-ring-bg" cx="30" cy="30" r="26" />
                    <circle class="progress-ring-fill" cx="30" cy="30" r="26" />
                </svg>
            ` : ''}
            <span class="scroll-icon">
                ${icons[this.options.style] || icons.rocket}
            </span>
        `;

        document.body.appendChild(button);
        this.button = button;

        if (this.options.showProgress) {
            this.progressRing = button.querySelector('.progress-ring-fill');
            this.updateProgress();
        }
    }

    addStyles() {
        if (document.getElementById('scroll-to-top-styles')) return;

        const styles = `
            .scroll-to-top {
                position: fixed;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
                border: none;
                cursor: pointer;
                z-index: 999997;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 20px rgba(255, 107, 53, 0.3);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                opacity: 0;
                visibility: hidden;
                transform: scale(0);
            }

            .scroll-to-top.visible {
                opacity: 1;
                visibility: visible;
                transform: scale(1);
            }

            .scroll-to-top:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 30px rgba(255, 107, 53, 0.5);
            }

            .scroll-to-top:active {
                transform: scale(0.95);
            }

            /* Positions */
            .scroll-to-top.bottom-right {
                bottom: 30px;
                right: 30px;
            }

            .scroll-to-top.bottom-left {
                bottom: 30px;
                left: 30px;
            }

            .scroll-to-top.bottom-center {
                bottom: 30px;
                left: 50%;
                transform: translateX(-50%) scale(0);
            }

            .scroll-to-top.bottom-center.visible {
                transform: translateX(-50%) scale(1);
            }

            .scroll-to-top.bottom-center:hover {
                transform: translateX(-50%) scale(1.1);
            }

            /* Icon */
            .scroll-icon {
                color: white;
                font-size: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 2;
            }

            .scroll-to-top.rocket .scroll-icon {
                animation: rocketBounce 2s ease-in-out infinite;
            }

            .scroll-to-top.arrow .scroll-icon {
                animation: arrowBounce 1.5s ease-in-out infinite;
            }

            .scroll-to-top.circle .scroll-icon {
                animation: circlePulse 2s ease-in-out infinite;
            }

            /* Progress Ring */
            .progress-ring {
                position: absolute;
                width: 100%;
                height: 100%;
                transform: rotate(-90deg);
                z-index: 1;
            }

            .progress-ring-bg {
                fill: none;
                stroke: rgba(255, 255, 255, 0.2);
                stroke-width: 2;
            }

            .progress-ring-fill {
                fill: none;
                stroke: white;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-dasharray: 163.36;
                stroke-dashoffset: 163.36;
                transition: stroke-dashoffset 0.1s linear;
            }

            /* Animations */
            @keyframes rocketBounce {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-5px);
                }
            }

            @keyframes arrowBounce {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-8px);
                }
            }

            @keyframes circlePulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.1);
                }
            }

            /* Mobile */
            @media (max-width: 768px) {
                .scroll-to-top {
                    width: 50px;
                    height: 50px;
                    bottom: 20px;
                    right: 20px;
                }

                .scroll-to-top.bottom-left {
                    bottom: 20px;
                    left: 20px;
                }

                .scroll-to-top.bottom-center {
                    bottom: 20px;
                }

                .scroll-icon {
                    font-size: 20px;
                }
            }

            /* Dark mode */
            body.dark-mode .scroll-to-top {
                background: linear-gradient(135deg, #F7931E 0%, #FF6B35 100%);
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.id = 'scroll-to-top-styles';
        styleElement.textContent = styles;
        document.head.appendChild(styleElement);
    }

    attachEvents() {
        // Scroll event
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Click event
        this.button.addEventListener('click', () => this.scrollToTop());
    }

    handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Show/hide button
        if (scrollTop > this.options.showAt) {
            if (!this.isVisible) {
                this.button.classList.add('visible');
                this.isVisible = true;
            }
        } else {
            if (this.isVisible) {
                this.button.classList.remove('visible');
                this.isVisible = false;
            }
        }

        // Update progress
        if (this.options.showProgress) {
            this.updateProgress();
        }
    }

    updateProgress() {
        if (!this.progressRing) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = (scrollTop / scrollHeight) * 100;

        const circumference = 163.36; // 2 * PI * radius (26)
        const offset = circumference - (progress / 100) * circumference;
        this.progressRing.style.strokeDashoffset = offset;
    }

    scrollToTop() {
        const start = window.pageYOffset || document.documentElement.scrollTop;
        const duration = this.options.scrollDuration;
        const startTime = performance.now();

        const easeInOutCubic = (t) => {
            return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
        };

        const scroll = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = easeInOutCubic(progress);

            window.scrollTo(0, start * (1 - ease));

            if (progress < 1) {
                requestAnimationFrame(scroll);
            }
        };

        requestAnimationFrame(scroll);

        // Add rocket trail effect for rocket style
        if (this.options.style === 'rocket') {
            this.addRocketTrail();
        }
    }

    addRocketTrail() {
        const trail = document.createElement('div');
        trail.className = 'rocket-trail';
        trail.style.cssText = `
            position: fixed;
            width: 3px;
            background: linear-gradient(to bottom, transparent, #FF6B35, transparent);
            pointer-events: none;
            z-index: 999996;
            animation: rocketTrail ${this.options.scrollDuration}ms ease-out;
            right: 44px;
            bottom: 30px;
            height: 0;
        `;

        if (!document.getElementById('rocket-trail-styles')) {
            const trailStyles = document.createElement('style');
            trailStyles.id = 'rocket-trail-styles';
            trailStyles.textContent = `
                @keyframes rocketTrail {
                    0% {
                        height: 0;
                        opacity: 1;
                    }
                    50% {
                        height: 80vh;
                        opacity: 0.8;
                    }
                    100% {
                        height: 100vh;
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(trailStyles);
        }

        document.body.appendChild(trail);

        setTimeout(() => {
            if (trail.parentNode) {
                trail.parentNode.removeChild(trail);
            }
        }, this.options.scrollDuration);
    }

    destroy() {
        if (this.button && this.button.parentNode) {
            this.button.parentNode.removeChild(this.button);
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.scrollToTopBtn = new ScrollToTop({
        showAt: 300,
        scrollDuration: 1000,
        position: 'bottom-right',
        showProgress: true,
        style: 'rocket'
    });
});

// Export
window.ScrollToTop = ScrollToTop;
