// ========================================
// PYRAMEDIA - Advanced Micro-interactions
// ========================================

class MicroInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.addStyles();
        this.initRippleEffect();
        this.initIconAnimations();
        this.initFormWaves();
        this.initButtonEffects();
        this.initCardHoverEffects();
        this.initTooltips();
    }

    addStyles() {
        if (document.getElementById('micro-interactions-styles')) return;

        const styles = `
            /* Ripple Effect */
            .ripple-container {
                position: relative;
                overflow: hidden;
            }

            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s ease-out;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            /* Icon Animations */
            .icon-hover {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-block;
            }

            .icon-hover:hover {
                transform: scale(1.2) rotate(5deg);
            }

            .icon-bounce:hover {
                animation: iconBounce 0.5s ease;
            }

            .icon-spin:hover {
                animation: iconSpin 0.6s ease;
            }

            .icon-shake:hover {
                animation: iconShake 0.5s ease;
            }

            .icon-pulse:hover {
                animation: iconPulse 0.8s ease infinite;
            }

            @keyframes iconBounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }

            @keyframes iconSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            @keyframes iconShake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            @keyframes iconPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }

            /* Form Wave Effect */
            .wave-input {
                position: relative;
            }

            .wave-input input,
            .wave-input textarea {
                position: relative;
                z-index: 1;
            }

            .wave-input::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: linear-gradient(90deg, #FF6B35, #F7931E);
                transition: width 0.3s ease;
                z-index: 2;
            }

            .wave-input.focused::after {
                width: 100%;
            }

            .wave-input label {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #999;
                pointer-events: none;
                transition: all 0.3s ease;
                background: white;
                padding: 0 4px;
            }

            .wave-input.has-value label,
            .wave-input.focused label {
                top: 0;
                font-size: 12px;
                color: #FF6B35;
            }

            /* Button Effects */
            .btn-micro {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .btn-micro::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s ease, height 0.6s ease;
            }

            .btn-micro:hover::before {
                width: 300px;
                height: 300px;
            }

            .btn-micro:active {
                transform: scale(0.95);
            }

            /* Card Hover Effects */
            .card-micro {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
            }

            .card-micro::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(247, 147, 30, 0.1));
                opacity: 0;
                transition: opacity 0.3s ease;
                border-radius: inherit;
                pointer-events: none;
            }

            .card-micro:hover {
                transform: translateY(-10px) scale(1.02);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .card-micro:hover::before {
                opacity: 1;
            }

            /* Tooltip */
            .tooltip-micro {
                position: relative;
                cursor: help;
            }

            .tooltip-micro::before {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(-8px);
                background: #1a1a1a;
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 13px;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: all 0.3s ease;
                z-index: 1000;
            }

            .tooltip-micro::after {
                content: '';
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(2px);
                border: 5px solid transparent;
                border-top-color: #1a1a1a;
                opacity: 0;
                pointer-events: none;
                transition: all 0.3s ease;
            }

            .tooltip-micro:hover::before,
            .tooltip-micro:hover::after {
                opacity: 1;
                transform: translateX(-50%) translateY(-10px);
            }

            .tooltip-micro:hover::after {
                transform: translateX(-50%) translateY(0);
            }

            /* Success/Error Animations */
            .input-success {
                border-color: #10b981 !important;
                animation: successShake 0.5s ease;
            }

            .input-error {
                border-color: #ef4444 !important;
                animation: errorShake 0.5s ease;
            }

            @keyframes successShake {
                0%, 100% { transform: translateX(0); }
                25%, 75% { transform: translateX(5px); }
                50% { transform: translateX(-5px); }
            }

            @keyframes errorShake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }

            /* Loading Dots */
            .loading-dots {
                display: inline-flex;
                gap: 4px;
            }

            .loading-dots span {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: currentColor;
                animation: loadingDots 1.4s ease-in-out infinite;
            }

            .loading-dots span:nth-child(1) { animation-delay: 0s; }
            .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
            .loading-dots span:nth-child(3) { animation-delay: 0.4s; }

            @keyframes loadingDots {
                0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
                40% { transform: scale(1); opacity: 1; }
            }

            /* Checkbox Animation */
            .checkbox-micro {
                position: relative;
                display: inline-block;
                width: 20px;
                height: 20px;
            }

            .checkbox-micro input {
                opacity: 0;
                position: absolute;
            }

            .checkbox-micro .checkmark {
                position: absolute;
                top: 0;
                left: 0;
                width: 20px;
                height: 20px;
                border: 2px solid #ddd;
                border-radius: 4px;
                transition: all 0.3s ease;
            }

            .checkbox-micro input:checked + .checkmark {
                background: linear-gradient(135deg, #FF6B35, #F7931E);
                border-color: #FF6B35;
            }

            .checkbox-micro .checkmark::after {
                content: '';
                position: absolute;
                display: none;
                left: 6px;
                top: 2px;
                width: 5px;
                height: 10px;
                border: solid white;
                border-width: 0 2px 2px 0;
                transform: rotate(45deg);
            }

            .checkbox-micro input:checked + .checkmark::after {
                display: block;
                animation: checkmarkSlide 0.3s ease;
            }

            @keyframes checkmarkSlide {
                0% {
                    transform: rotate(45deg) translateY(-10px);
                    opacity: 0;
                }
                100% {
                    transform: rotate(45deg) translateY(0);
                    opacity: 1;
                }
            }

            /* Switch Toggle */
            .switch-micro {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 26px;
            }

            .switch-micro input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .switch-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: 0.4s;
                border-radius: 26px;
            }

            .switch-slider::before {
                position: absolute;
                content: '';
                height: 18px;
                width: 18px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: 0.4s;
                border-radius: 50%;
            }

            .switch-micro input:checked + .switch-slider {
                background: linear-gradient(135deg, #FF6B35, #F7931E);
            }

            .switch-micro input:checked + .switch-slider::before {
                transform: translateX(24px);
            }

            /* Badge Pulse */
            .badge-pulse {
                position: relative;
                display: inline-block;
            }

            .badge-pulse::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 8px;
                height: 8px;
                background: #ef4444;
                border-radius: 50%;
                animation: badgePulse 2s ease infinite;
            }

            @keyframes badgePulse {
                0%, 100% {
                    transform: scale(1);
                    opacity: 1;
                }
                50% {
                    transform: scale(1.5);
                    opacity: 0;
                }
            }

            /* Dark Mode */
            body.dark-mode .wave-input label {
                background: #1a1a1a;
            }

            body.dark-mode .tooltip-micro::before {
                background: #333;
            }

            body.dark-mode .tooltip-micro::after {
                border-top-color: #333;
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.id = 'micro-interactions-styles';
        styleElement.textContent = styles;
        document.head.appendChild(styleElement);
    }

    initRippleEffect() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('.ripple-container, button, .btn, a.btn, [data-ripple]');
            if (!target) return;

            if (!target.classList.contains('ripple-container')) {
                target.classList.add('ripple-container');
            }

            const ripple = document.createElement('span');
            ripple.classList.add('ripple');

            const rect = target.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            target.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    initIconAnimations() {
        document.querySelectorAll('[data-icon-animation]').forEach(icon => {
            const animation = icon.dataset.iconAnimation;
            icon.classList.add(`icon-${animation}`);
        });
    }

    initFormWaves() {
        document.querySelectorAll('.wave-input input, .wave-input textarea').forEach(input => {
            const container = input.closest('.wave-input');

            input.addEventListener('focus', () => {
                container.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                container.classList.remove('focused');
                if (input.value) {
                    container.classList.add('has-value');
                } else {
                    container.classList.remove('has-value');
                }
            });

            // Check initial value
            if (input.value) {
                container.classList.add('has-value');
            }
        });
    }

    initButtonEffects() {
        document.querySelectorAll('button, .btn, a.btn').forEach(btn => {
            if (!btn.classList.contains('btn-micro')) {
                btn.classList.add('btn-micro');
            }
        });
    }

    initCardHoverEffects() {
        document.querySelectorAll('.service-card, .blog-card, .portfolio-card, [data-card]').forEach(card => {
            if (!card.classList.contains('card-micro')) {
                card.classList.add('card-micro');
            }
        });
    }

    initTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            if (!el.classList.contains('tooltip-micro')) {
                el.classList.add('tooltip-micro');
            }
        });
    }

    // Utility functions
    static showSuccess(input) {
        input.classList.remove('input-error');
        input.classList.add('input-success');
        setTimeout(() => input.classList.remove('input-success'), 2000);
    }

    static showError(input) {
        input.classList.remove('input-success');
        input.classList.add('input-error');
        setTimeout(() => input.classList.remove('input-error'), 2000);
    }

    static createLoadingDots() {
        return `
            <div class="loading-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.microInteractions = new MicroInteractions();
});

// Export
window.MicroInteractions = MicroInteractions;
