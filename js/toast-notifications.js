// ========================================
// PYRAMEDIA - Toast Notifications System
// ========================================

class ToastNotification {
    constructor() {
        this.container = null;
        this.toasts = [];
        this.init();
    }

    init() {
        this.createContainer();
        this.addStyles();
    }

    createContainer() {
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
            this.container = container;
        } else {
            this.container = document.getElementById('toast-container');
        }
    }

    addStyles() {
        if (document.getElementById('toast-styles')) return;

        const styles = `
            .toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 999998;
                display: flex;
                flex-direction: column;
                gap: 15px;
                max-width: 400px;
            }

            .toast {
                background: white;
                border-radius: 12px;
                padding: 16px 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                display: flex;
                align-items: flex-start;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease, fadeIn 0.3s ease;
                position: relative;
                overflow: hidden;
                transform-origin: right top;
            }

            .toast.removing {
                animation: slideOutRight 0.3s ease, fadeOut 0.3s ease;
            }

            .toast::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background: currentColor;
                animation: progressBar var(--duration) linear;
            }

            .toast-icon {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 14px;
                color: white;
            }

            .toast-content {
                flex: 1;
                min-width: 0;
            }

            .toast-title {
                font-weight: 600;
                font-size: 15px;
                margin-bottom: 4px;
                color: #1a1a1a;
            }

            .toast-message {
                font-size: 14px;
                color: #666;
                line-height: 1.5;
            }

            .toast-close {
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
                flex-shrink: 0;
            }

            .toast-close:hover {
                background: rgba(0, 0, 0, 0.05);
                color: #333;
            }

            /* Toast Types */
            .toast.success {
                border-left: 4px solid #10b981;
                color: #10b981;
            }

            .toast.success .toast-icon {
                background: #10b981;
            }

            .toast.error {
                border-left: 4px solid #ef4444;
                color: #ef4444;
            }

            .toast.error .toast-icon {
                background: #ef4444;
            }

            .toast.warning {
                border-left: 4px solid #f59e0b;
                color: #f59e0b;
            }

            .toast.warning .toast-icon {
                background: #f59e0b;
            }

            .toast.info {
                border-left: 4px solid #3b82f6;
                color: #3b82f6;
            }

            .toast.info .toast-icon {
                background: #3b82f6;
            }

            /* Animations */
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                }
                to {
                    transform: translateX(0);
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                }
                to {
                    transform: translateX(400px);
                }
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }

            @keyframes progressBar {
                from { width: 100%; }
                to { width: 0%; }
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .toast-container {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                    max-width: none;
                }

                .toast {
                    min-width: auto;
                    max-width: none;
                }
            }

            /* Dark Mode */
            body.dark-mode .toast {
                background: #1a1a1a;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            }

            body.dark-mode .toast-title {
                color: #ffffff;
            }

            body.dark-mode .toast-message {
                color: #cccccc;
            }

            body.dark-mode .toast-close {
                color: #666;
            }

            body.dark-mode .toast-close:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #999;
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.id = 'toast-styles';
        styleElement.textContent = styles;
        document.head.appendChild(styleElement);
    }

    show(options) {
        const {
            type = 'info',
            title = '',
            message = '',
            duration = 5000,
            closeable = true,
            sound = false
        } = options;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.style.setProperty('--duration', `${duration}ms`);

        const icons = {
            success: '✓',
            error: '✕',
            warning: '!',
            info: 'i'
        };

        toast.innerHTML = `
            <div class="toast-icon">
                ${icons[type] || icons.info}
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            ${closeable ? '<button class="toast-close" aria-label="Close">×</button>' : ''}
        `;

        if (closeable) {
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => this.remove(toast));
        }

        this.container.appendChild(toast);
        this.toasts.push(toast);

        // Play sound if enabled
        if (sound) {
            this.playSound(type);
        }

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }

        return toast;
    }

    remove(toast) {
        if (!toast || !toast.parentNode) return;

        toast.classList.add('removing');

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            const index = this.toasts.indexOf(toast);
            if (index > -1) {
                this.toasts.splice(index, 1);
            }
        }, 300);
    }

    playSound(type) {
        // Simple beep sound using Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        const frequencies = {
            success: 800,
            error: 400,
            warning: 600,
            info: 700
        };

        oscillator.frequency.value = frequencies[type] || 700;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    }

    // Convenience methods
    success(message, title = 'Success', options = {}) {
        return this.show({ type: 'success', title, message, ...options });
    }

    error(message, title = 'Error', options = {}) {
        return this.show({ type: 'error', title, message, ...options });
    }

    warning(message, title = 'Warning', options = {}) {
        return this.show({ type: 'warning', title, message, ...options });
    }

    info(message, title = 'Info', options = {}) {
        return this.show({ type: 'info', title, message, ...options });
    }

    clear() {
        this.toasts.forEach(toast => this.remove(toast));
    }
}

// Create global instance
window.Toast = new ToastNotification();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ToastNotification;
}
