// ========================================
// PYRAMEDIA - Advanced Animations Library
// ========================================

/**
 * Scroll-triggered animations controller
 */
class ScrollAnimations {
    constructor() {
        this.elements = [];
        this.init();
    }

    init() {
        this.setupIntersectionObserver();
        this.setupScrollProgress();
        this.setupParallax();
    }

    /**
     * Setup Intersection Observer for scroll-triggered animations
     */
    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');

                    // Add stagger effect for children
                    const children = entry.target.querySelectorAll('[data-stagger]');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.classList.add('animate-in');
                        }, index * 100);
                    });
                }
            });
        }, options);

        // Observe all elements with data-scroll attribute
        document.querySelectorAll('[data-scroll]').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Setup scroll progress indicator
     */
    setupScrollProgress() {
        let progressBar = document.getElementById('scroll-progress');

        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.id = 'scroll-progress';
            progressBar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 4px;
                background: linear-gradient(90deg, #FF6B35 0%, #F7931E 100%);
                z-index: 9999;
                transition: width 0.1s ease;
            `;
            document.body.appendChild(progressBar);
        }

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = (scrollTop / scrollHeight) * 100;
            progressBar.style.width = progress + '%';
        });
    }

    /**
     * Setup parallax scrolling effect
     */
    setupParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');

        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;

            parallaxElements.forEach(el => {
                const speed = el.dataset.parallax || 0.5;
                const yPos = -(scrolled * speed);
                el.style.transform = `translateY(${yPos}px)`;
            });
        });
    }
}

/**
 * Advanced cursor effects
 */
class CursorEffects {
    constructor() {
        this.cursor = null;
        this.cursorDot = null;
        this.init();
    }

    init() {
        // Create custom cursor elements
        this.cursor = document.createElement('div');
        this.cursor.className = 'custom-cursor';
        this.cursor.style.cssText = `
            width: 40px;
            height: 40px;
            border: 2px solid #FF6B35;
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99999;
            transition: all 0.15s ease;
            transform: translate(-50%, -50%);
            mix-blend-mode: difference;
        `;

        this.cursorDot = document.createElement('div');
        this.cursorDot.className = 'custom-cursor-dot';
        this.cursorDot.style.cssText = `
            width: 8px;
            height: 8px;
            background: #FF6B35;
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 99999;
            transform: translate(-50%, -50%);
        `;

        document.body.appendChild(this.cursor);
        document.body.appendChild(this.cursorDot);

        // Track mouse movement
        document.addEventListener('mousemove', (e) => {
            this.cursorDot.style.left = e.clientX + 'px';
            this.cursorDot.style.top = e.clientY + 'px';

            setTimeout(() => {
                this.cursor.style.left = e.clientX + 'px';
                this.cursor.style.top = e.clientY + 'px';
            }, 50);
        });

        // Expand cursor on hover
        document.querySelectorAll('a, button, [data-cursor-expand]').forEach(el => {
            el.addEventListener('mouseenter', () => {
                this.cursor.style.width = '60px';
                this.cursor.style.height = '60px';
                this.cursor.style.borderColor = '#F7931E';
            });

            el.addEventListener('mouseleave', () => {
                this.cursor.style.width = '40px';
                this.cursor.style.height = '40px';
                this.cursor.style.borderColor = '#FF6B35';
            });
        });

        // Hide default cursor on desktop
        if (window.innerWidth > 768) {
            document.body.style.cursor = 'none';
            document.querySelectorAll('a, button').forEach(el => {
                el.style.cursor = 'none';
            });
        }
    }
}

/**
 * Text animations
 */
class TextAnimations {
    constructor() {
        this.init();
    }

    init() {
        this.setupTypingEffect();
        this.setupSplitText();
        this.setupCountUp();
    }

    /**
     * Typing effect for text elements
     */
    setupTypingEffect() {
        document.querySelectorAll('[data-typing]').forEach(el => {
            const text = el.textContent;
            const speed = parseInt(el.dataset.typing) || 50;
            el.textContent = '';

            let i = 0;
            const typeWriter = () => {
                if (i < text.length) {
                    el.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, speed);
                }
            };

            // Start typing when element is visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        typeWriter();
                        observer.disconnect();
                    }
                });
            });

            observer.observe(el);
        });
    }

    /**
     * Split text into animated characters
     */
    setupSplitText() {
        document.querySelectorAll('[data-split-text]').forEach(el => {
            const text = el.textContent;
            el.innerHTML = '';

            text.split('').forEach((char, index) => {
                const span = document.createElement('span');
                span.textContent = char;
                span.style.cssText = `
                    display: inline-block;
                    opacity: 0;
                    transform: translateY(20px);
                    animation: fadeInUp 0.5s ease forwards;
                    animation-delay: ${index * 0.05}s;
                `;
                el.appendChild(span);
            });
        });
    }

    /**
     * Count up animation for numbers
     */
    setupCountUp() {
        document.querySelectorAll('[data-countup]').forEach(el => {
            const target = parseInt(el.dataset.countup);
            const duration = parseInt(el.dataset.duration) || 2000;
            const suffix = el.dataset.suffix || '';

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCount(el, 0, target, duration, suffix);
                        observer.disconnect();
                    }
                });
            });

            observer.observe(el);
        });
    }

    animateCount(element, start, end, duration, suffix) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current) + suffix;
        }, 16);
    }
}

/**
 * Particle background effect
 */
class ParticleBackground {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.options = {
            particleCount: options.particleCount || 50,
            particleColor: options.particleColor || '#FF6B35',
            particleSize: options.particleSize || 3,
            speed: options.speed || 1,
            ...options
        };

        this.init();
    }

    init() {
        const canvas = document.createElement('canvas');
        canvas.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        `;
        this.container.style.position = 'relative';
        this.container.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        canvas.width = this.container.offsetWidth;
        canvas.height = this.container.offsetHeight;

        const particles = [];

        // Create particles
        for (let i = 0; i < this.options.particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * this.options.speed,
                vy: (Math.random() - 0.5) * this.options.speed,
                size: Math.random() * this.options.particleSize + 1
            });
        }

        // Animation loop
        const animate = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach((particle, i) => {
                // Update position
                particle.x += particle.vx;
                particle.y += particle.vy;

                // Bounce off edges
                if (particle.x < 0 || particle.x > canvas.width) particle.vx *= -1;
                if (particle.y < 0 || particle.y > canvas.height) particle.vy *= -1;

                // Draw particle
                ctx.fillStyle = this.options.particleColor;
                ctx.globalAlpha = 0.5;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                ctx.fill();

                // Draw connections
                particles.forEach((otherParticle, j) => {
                    if (i === j) return;

                    const dx = particle.x - otherParticle.x;
                    const dy = particle.y - otherParticle.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 100) {
                        ctx.strokeStyle = this.options.particleColor;
                        ctx.globalAlpha = 0.2 * (1 - distance / 100);
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particle.x, particle.y);
                        ctx.lineTo(otherParticle.x, otherParticle.y);
                        ctx.stroke();
                    }
                });
            });

            requestAnimationFrame(animate);
        };

        animate();

        // Resize handler
        window.addEventListener('resize', () => {
            canvas.width = this.container.offsetWidth;
            canvas.height = this.container.offsetHeight;
        });
    }
}

/**
 * Magnetic button effect
 */
class MagneticButtons {
    constructor() {
        this.init();
    }

    init() {
        document.querySelectorAll('[data-magnetic]').forEach(button => {
            button.addEventListener('mousemove', (e) => {
                const rect = button.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;

                button.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translate(0, 0)';
            });
        });
    }
}

/**
 * Image reveal effect
 */
class ImageReveal {
    constructor() {
        this.init();
    }

    init() {
        document.querySelectorAll('[data-image-reveal]').forEach(container => {
            const img = container.querySelector('img');
            if (!img) return;

            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, #FF6B35 0%, #F7931E 100%);
                transform: scaleX(0);
                transform-origin: left;
                z-index: 1;
            `;

            container.style.position = 'relative';
            container.style.overflow = 'hidden';
            container.appendChild(overlay);

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Animate overlay
                        overlay.style.transition = 'transform 0.8s cubic-bezier(0.77, 0, 0.175, 1)';
                        overlay.style.transform = 'scaleX(1)';

                        setTimeout(() => {
                            overlay.style.transformOrigin = 'right';
                            overlay.style.transform = 'scaleX(0)';
                        }, 800);

                        observer.disconnect();
                    }
                });
            });

            observer.observe(container);
        });
    }
}

/**
 * Smooth scroll with easing
 */
class SmoothScroll {
    constructor() {
        this.init();
    }

    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const targetId = anchor.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (!targetElement) return;

                e.preventDefault();

                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const startPosition = window.pageYOffset;
                const distance = targetPosition - startPosition;
                const duration = 1000;
                let start = null;

                const easeInOutCubic = (t) => {
                    return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
                };

                const animation = (currentTime) => {
                    if (start === null) start = currentTime;
                    const timeElapsed = currentTime - start;
                    const progress = Math.min(timeElapsed / duration, 1);
                    const ease = easeInOutCubic(progress);

                    window.scrollTo(0, startPosition + distance * ease);

                    if (timeElapsed < duration) {
                        requestAnimationFrame(animation);
                    }
                };

                requestAnimationFrame(animation);
            });
        });
    }
}

/**
 * Tilt effect for cards
 */
class TiltEffect {
    constructor() {
        this.init();
    }

    init() {
        document.querySelectorAll('[data-tilt]').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
            });

            card.style.transition = 'transform 0.3s ease';
            card.style.transformStyle = 'preserve-3d';
        });
    }
}

// ==========================================
// INITIALIZE ALL ANIMATIONS
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize scroll animations
    new ScrollAnimations();

    // Initialize cursor effects (only on desktop)
    if (window.innerWidth > 768) {
        // new CursorEffects(); // Commented out by default, uncomment to enable
    }

    // Initialize text animations
    new TextAnimations();

    // Initialize magnetic buttons
    new MagneticButtons();

    // Initialize image reveal
    new ImageReveal();

    // Initialize smooth scroll
    new SmoothScroll();

    // Initialize tilt effect
    new TiltEffect();

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        [data-scroll] {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-scroll].animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        [data-stagger] {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }

        [data-stagger].animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        [data-magnetic] {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-tilt] {
            will-change: transform;
        }
    `;
    document.head.appendChild(style);
});

// Export classes for manual initialization
window.PyramediaAnimations = {
    ScrollAnimations,
    CursorEffects,
    TextAnimations,
    ParticleBackground,
    MagneticButtons,
    ImageReveal,
    SmoothScroll,
    TiltEffect
};
