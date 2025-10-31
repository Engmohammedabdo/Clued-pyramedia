// ========================================
// PYRAMEDIA - Professional Loading Screen
// ========================================

class LoadingScreen {
    constructor() {
        this.loadingElement = null;
        this.progressBar = null;
        this.progressText = null;
        this.init();
    }

    init() {
        this.createLoadingScreen();
        this.simulateLoading();
    }

    createLoadingScreen() {
        // Create loading screen HTML
        const loadingHTML = `
            <div id="pyramedia-loader" class="pyramedia-loading-screen">
                <div class="loader-container">
                    <!-- Animated Logo -->
                    <div class="loader-logo">
                        <svg viewBox="0 0 200 200" class="logo-svg">
                            <!-- Pyramid -->
                            <path class="pyramid-shape" d="M 100,40 L 160,140 L 40,140 Z" fill="none" stroke="#FF6B35" stroke-width="3"/>
                            <path class="pyramid-left" d="M 100,40 L 40,140 L 70,150 Z" fill="#FF6B35" opacity="0.3"/>
                            <path class="pyramid-right" d="M 100,40 L 130,150 L 160,140 Z" fill="#FF6B35" opacity="0.5"/>

                            <!-- Media Wave -->
                            <g class="media-wave">
                                <circle cx="60" cy="120" r="3" fill="#F7931E"/>
                                <circle cx="80" cy="120" r="3" fill="#F7931E"/>
                                <circle cx="100" cy="120" r="3" fill="#F7931E"/>
                                <circle cx="120" cy="120" r="3" fill="#F7931E"/>
                                <circle cx="140" cy="120" r="3" fill="#F7931E"/>
                            </g>
                        </svg>
                    </div>

                    <!-- Brand Name -->
                    <h1 class="loader-brand">PYRAMEDIA</h1>
                    <p class="loader-tagline">Marketing & Media Solutions</p>

                    <!-- Progress Bar -->
                    <div class="loader-progress">
                        <div class="progress-bar-container">
                            <div class="progress-bar" id="loaderProgressBar"></div>
                        </div>
                        <div class="progress-text" id="loaderProgressText">0%</div>
                    </div>

                    <!-- Loading Text -->
                    <p class="loader-status" id="loaderStatus">Initializing...</p>
                </div>

                <!-- Animated Particles -->
                <canvas id="loaderParticles" class="loader-particles"></canvas>
            </div>
        `;

        document.body.insertAdjacentHTML('afterbegin', loadingHTML);
        this.loadingElement = document.getElementById('pyramedia-loader');
        this.progressBar = document.getElementById('loaderProgressBar');
        this.progressText = document.getElementById('loaderProgressText');
        this.statusText = document.getElementById('loaderStatus');

        this.addStyles();
        this.initParticles();
    }

    addStyles() {
        const styles = `
            .pyramedia-loading-screen {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity 0.5s ease, visibility 0.5s ease;
            }

            .pyramedia-loading-screen.loaded {
                opacity: 0;
                visibility: hidden;
            }

            .loader-container {
                text-align: center;
                position: relative;
                z-index: 2;
            }

            .loader-logo {
                width: 150px;
                height: 150px;
                margin: 0 auto 30px;
                animation: float 3s ease-in-out infinite;
            }

            .logo-svg {
                width: 100%;
                height: 100%;
            }

            .pyramid-shape {
                stroke-dasharray: 400;
                stroke-dashoffset: 400;
                animation: drawPyramid 2s ease forwards;
            }

            .pyramid-left, .pyramid-right {
                opacity: 0;
                animation: fadeInPyramid 1s ease forwards 1s;
            }

            .media-wave circle {
                animation: waveAnimation 1.5s ease-in-out infinite;
            }

            .media-wave circle:nth-child(1) { animation-delay: 0s; }
            .media-wave circle:nth-child(2) { animation-delay: 0.1s; }
            .media-wave circle:nth-child(3) { animation-delay: 0.2s; }
            .media-wave circle:nth-child(4) { animation-delay: 0.3s; }
            .media-wave circle:nth-child(5) { animation-delay: 0.4s; }

            .loader-brand {
                font-size: 2.5rem;
                font-weight: 800;
                background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 10px;
                letter-spacing: 3px;
                animation: fadeInUp 0.8s ease forwards 0.5s;
                opacity: 0;
            }

            .loader-tagline {
                color: #999;
                font-size: 0.9rem;
                margin-bottom: 40px;
                animation: fadeInUp 0.8s ease forwards 0.7s;
                opacity: 0;
            }

            .loader-progress {
                width: 300px;
                margin: 0 auto 20px;
                animation: fadeInUp 0.8s ease forwards 0.9s;
                opacity: 0;
            }

            .progress-bar-container {
                width: 100%;
                height: 4px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #FF6B35 0%, #F7931E 100%);
                border-radius: 10px;
                width: 0%;
                transition: width 0.3s ease;
                box-shadow: 0 0 10px rgba(255, 107, 53, 0.5);
            }

            .progress-text {
                color: #FF6B35;
                font-size: 1.2rem;
                font-weight: 600;
            }

            .loader-status {
                color: #666;
                font-size: 0.85rem;
                animation: fadeInUp 0.8s ease forwards 1.1s;
                opacity: 0;
            }

            .loader-particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
            }

            /* Animations */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }

            @keyframes drawPyramid {
                to { stroke-dashoffset: 0; }
            }

            @keyframes fadeInPyramid {
                to { opacity: 1; }
            }

            @keyframes waveAnimation {
                0%, 100% { transform: translateY(0px); opacity: 1; }
                50% { transform: translateY(-10px); opacity: 0.5; }
            }

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

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .loader-logo {
                    width: 100px;
                    height: 100px;
                }

                .loader-brand {
                    font-size: 2rem;
                }

                .loader-progress {
                    width: 250px;
                }
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.textContent = styles;
        document.head.appendChild(styleElement);
    }

    initParticles() {
        const canvas = document.getElementById('loaderParticles');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                size: Math.random() * 2 + 1,
                opacity: Math.random() * 0.5 + 0.2
            });
        }

        const animate = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

                ctx.fillStyle = `rgba(255, 107, 53, ${p.opacity})`;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fill();
            });

            if (this.loadingElement && !this.loadingElement.classList.contains('loaded')) {
                requestAnimationFrame(animate);
            }
        };

        animate();

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    }

    simulateLoading() {
        const steps = [
            { progress: 20, status: 'Loading resources...', delay: 300 },
            { progress: 40, status: 'Initializing components...', delay: 500 },
            { progress: 60, status: 'Preparing content...', delay: 400 },
            { progress: 80, status: 'Finalizing...', delay: 400 },
            { progress: 100, status: 'Ready!', delay: 300 }
        ];

        let currentStep = 0;

        const updateProgress = () => {
            if (currentStep < steps.length) {
                const step = steps[currentStep];
                this.setProgress(step.progress, step.status);
                currentStep++;
                setTimeout(updateProgress, step.delay);
            } else {
                setTimeout(() => this.hideLoader(), 300);
            }
        };

        // Start after initial delay
        setTimeout(updateProgress, 500);
    }

    setProgress(percent, status) {
        if (this.progressBar) {
            this.progressBar.style.width = percent + '%';
        }
        if (this.progressText) {
            this.progressText.textContent = percent + '%';
        }
        if (status && this.statusText) {
            this.statusText.textContent = status;
        }
    }

    hideLoader() {
        if (this.loadingElement) {
            this.loadingElement.classList.add('loaded');

            // Remove from DOM after animation
            setTimeout(() => {
                if (this.loadingElement && this.loadingElement.parentNode) {
                    this.loadingElement.parentNode.removeChild(this.loadingElement);
                }
            }, 500);
        }

        // Dispatch event
        window.dispatchEvent(new CustomEvent('pyramediaLoaded'));
    }
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new LoadingScreen();
    });
} else {
    new LoadingScreen();
}

// Export for manual control
window.PyramediaLoader = LoadingScreen;
