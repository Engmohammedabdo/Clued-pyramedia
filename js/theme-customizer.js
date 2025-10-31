// ========================================
// PYRAMEDIA - Theme Customizer
// ========================================

class ThemeCustomizer {
    constructor() {
        this.settings = {
            primaryColor: '#FF6B35',
            secondaryColor: '#F7931E',
            darkMode: false,
            fontSize: 'medium',
            cornerRadius: 'medium',
            animationSpeed: 'normal',
            reduceMotion: false,
            highContrast: false
        };

        this.panel = null;
        this.isOpen = false;
        this.init();
    }

    init() {
        this.loadSettings();
        this.createPanel();
        this.addStyles();
        this.applySettings();
        this.attachEvents();
    }

    loadSettings() {
        const saved = localStorage.getItem('pyramedia_theme_settings');
        if (saved) {
            this.settings = { ...this.settings, ...JSON.parse(saved) };
        }
    }

    saveSettings() {
        localStorage.setItem('pyramedia_theme_settings', JSON.stringify(this.settings));
    }

    createPanel() {
        const panel = document.createElement('div');
        panel.id = 'theme-customizer';
        panel.className = 'theme-customizer';
        panel.innerHTML = `
            <button class="customizer-toggle" id="customizerToggle" aria-label="Open theme customizer">
                <i class="fas fa-palette"></i>
            </button>

            <div class="customizer-panel" id="customizerPanel">
                <div class="customizer-header">
                    <h3>
                        <i class="fas fa-paint-brush"></i>
                        Theme Customizer
                    </h3>
                    <button class="customizer-close" id="customizerClose" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="customizer-content">
                    <!-- Dark Mode -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-moon"></i>
                            Dark Mode
                        </label>
                        <label class="switch-micro">
                            <input type="checkbox" id="darkModeToggle" ${this.settings.darkMode ? 'checked' : ''}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    <!-- High Contrast -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-adjust"></i>
                            High Contrast
                        </label>
                        <label class="switch-micro">
                            <input type="checkbox" id="highContrastToggle" ${this.settings.highContrast ? 'checked' : ''}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    <!-- Reduce Motion -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-running"></i>
                            Reduce Motion
                        </label>
                        <label class="switch-micro">
                            <input type="checkbox" id="reduceMotionToggle" ${this.settings.reduceMotion ? 'checked' : ''}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    <!-- Primary Color -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-droplet"></i>
                            Primary Color
                        </label>
                        <div class="color-presets">
                            <button class="color-preset" data-color="#FF6B35" style="background: #FF6B35" title="Orange"></button>
                            <button class="color-preset" data-color="#3B82F6" style="background: #3B82F6" title="Blue"></button>
                            <button class="color-preset" data-color="#10B981" style="background: #10B981" title="Green"></button>
                            <button class="color-preset" data-color="#8B5CF6" style="background: #8B5CF6" title="Purple"></button>
                            <button class="color-preset" data-color="#EF4444" style="background: #EF4444" title="Red"></button>
                            <button class="color-preset" data-color="#F59E0B" style="background: #F59E0B" title="Amber"></button>
                        </div>
                        <input type="color" id="primaryColorPicker" value="${this.settings.primaryColor}">
                    </div>

                    <!-- Font Size -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-text-height"></i>
                            Font Size
                        </label>
                        <div class="button-group">
                            <button class="btn-group-item ${this.settings.fontSize === 'small' ? 'active' : ''}" data-size="small">Small</button>
                            <button class="btn-group-item ${this.settings.fontSize === 'medium' ? 'active' : ''}" data-size="medium">Medium</button>
                            <button class="btn-group-item ${this.settings.fontSize === 'large' ? 'active' : ''}" data-size="large">Large</button>
                        </div>
                    </div>

                    <!-- Corner Radius -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-vector-square"></i>
                            Corner Radius
                        </label>
                        <div class="button-group">
                            <button class="btn-group-item ${this.settings.cornerRadius === 'sharp' ? 'active' : ''}" data-radius="sharp">Sharp</button>
                            <button class="btn-group-item ${this.settings.cornerRadius === 'medium' ? 'active' : ''}" data-radius="medium">Medium</button>
                            <button class="btn-group-item ${this.settings.cornerRadius === 'round' ? 'active' : ''}" data-radius="round">Round</button>
                        </div>
                    </div>

                    <!-- Animation Speed -->
                    <div class="customizer-section">
                        <label class="customizer-label">
                            <i class="fas fa-gauge"></i>
                            Animation Speed
                        </label>
                        <div class="button-group">
                            <button class="btn-group-item ${this.settings.animationSpeed === 'slow' ? 'active' : ''}" data-speed="slow">Slow</button>
                            <button class="btn-group-item ${this.settings.animationSpeed === 'normal' ? 'active' : ''}" data-speed="normal">Normal</button>
                            <button class="btn-group-item ${this.settings.animationSpeed === 'fast' ? 'active' : ''}" data-speed="fast">Fast</button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button class="customizer-reset" id="resetTheme">
                        <i class="fas fa-undo"></i>
                        Reset to Default
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(panel);
        this.panel = panel;
    }

    addStyles() {
        if (document.getElementById('theme-customizer-styles')) return;

        const styles = `
            .theme-customizer {
                position: fixed;
                top: 50%;
                right: 0;
                transform: translateY(-50%);
                z-index: 999990;
            }

            .customizer-toggle {
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #FF6B35, #F7931E);
                border: none;
                border-radius: 10px 0 0 10px;
                color: white;
                font-size: 20px;
                cursor: pointer;
                box-shadow: -3px 3px 10px rgba(0, 0, 0, 0.2);
                transition: all 0.3s ease;
                z-index: 2;
            }

            .customizer-toggle:hover {
                width: 60px;
                box-shadow: -5px 5px 20px rgba(0, 0, 0, 0.3);
            }

            .customizer-toggle i {
                animation: spin 3s linear infinite;
            }

            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .customizer-panel {
                position: absolute;
                right: -350px;
                top: 50%;
                transform: translateY(-50%);
                width: 350px;
                max-height: 90vh;
                background: white;
                border-radius: 15px 0 0 15px;
                box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .theme-customizer.open .customizer-panel {
                right: 0;
            }

            .customizer-header {
                padding: 20px;
                background: linear-gradient(135deg, #FF6B35, #F7931E);
                color: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .customizer-header h3 {
                font-size: 18px;
                font-weight: 600;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .customizer-close {
                background: none;
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                padding: 5px;
                transition: transform 0.3s ease;
            }

            .customizer-close:hover {
                transform: rotate(90deg);
            }

            .customizer-content {
                padding: 20px;
                overflow-y: auto;
                flex: 1;
            }

            .customizer-section {
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }

            .customizer-section:last-child {
                border-bottom: none;
            }

            .customizer-label {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                color: #333;
                margin-bottom: 12px;
            }

            .color-presets {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
                flex-wrap: wrap;
            }

            .color-preset {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 3px solid transparent;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .color-preset:hover,
            .color-preset.active {
                border-color: #333;
                transform: scale(1.1);
            }

            #primaryColorPicker {
                width: 100%;
                height: 50px;
                border: 2px solid #eee;
                border-radius: 8px;
                cursor: pointer;
            }

            .button-group {
                display: flex;
                gap: 8px;
            }

            .btn-group-item {
                flex: 1;
                padding: 10px;
                border: 2px solid #eee;
                background: white;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #666;
            }

            .btn-group-item:hover {
                border-color: #FF6B35;
                color: #FF6B35;
            }

            .btn-group-item.active {
                background: linear-gradient(135deg, #FF6B35, #F7931E);
                border-color: #FF6B35;
                color: white;
            }

            .customizer-reset {
                width: 100%;
                padding: 15px;
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .customizer-reset:hover {
                background: #dc2626;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
            }

            /* Dark Mode */
            body.dark-mode .customizer-panel {
                background: #1a1a1a;
            }

            body.dark-mode .customizer-label {
                color: white;
            }

            body.dark-mode .btn-group-item {
                background: #2a2a2a;
                border-color: #3a3a3a;
                color: #ccc;
            }

            body.dark-mode .customizer-section {
                border-bottom-color: #333;
            }

            /* Mobile */
            @media (max-width: 768px) {
                .customizer-panel {
                    width: 300px;
                    right: -300px;
                }

                .customizer-toggle {
                    width: 45px;
                    height: 45px;
                    font-size: 18px;
                }
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.id = 'theme-customizer-styles';
        styleElement.textContent = styles;
        document.head.appendChild(styleElement);
    }

    attachEvents() {
        // Toggle panel
        document.getElementById('customizerToggle').addEventListener('click', () => this.togglePanel());
        document.getElementById('customizerClose').addEventListener('click', () => this.togglePanel());

        // Dark mode
        document.getElementById('darkModeToggle').addEventListener('change', (e) => {
            this.settings.darkMode = e.target.checked;
            this.applySettings();
            this.saveSettings();
        });

        // High contrast
        document.getElementById('highContrastToggle').addEventListener('change', (e) => {
            this.settings.highContrast = e.target.checked;
            this.applySettings();
            this.saveSettings();
        });

        // Reduce motion
        document.getElementById('reduceMotionToggle').addEventListener('change', (e) => {
            this.settings.reduceMotion = e.target.checked;
            this.applySettings();
            this.saveSettings();
        });

        // Primary color presets
        document.querySelectorAll('.color-preset').forEach(btn => {
            btn.addEventListener('click', () => {
                const color = btn.dataset.color;
                this.settings.primaryColor = color;
                document.getElementById('primaryColorPicker').value = color;
                this.updateActivePreset(btn);
                this.applySettings();
                this.saveSettings();
            });
        });

        // Primary color picker
        document.getElementById('primaryColorPicker').addEventListener('change', (e) => {
            this.settings.primaryColor = e.target.value;
            this.applySettings();
            this.saveSettings();
        });

        // Font size
        document.querySelectorAll('[data-size]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.settings.fontSize = btn.dataset.size;
                this.updateButtonGroup(btn);
                this.applySettings();
                this.saveSettings();
            });
        });

        // Corner radius
        document.querySelectorAll('[data-radius]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.settings.cornerRadius = btn.dataset.radius;
                this.updateButtonGroup(btn);
                this.applySettings();
                this.saveSettings();
            });
        });

        // Animation speed
        document.querySelectorAll('[data-speed]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.settings.animationSpeed = btn.dataset.speed;
                this.updateButtonGroup(btn);
                this.applySettings();
                this.saveSettings();
            });
        });

        // Reset
        document.getElementById('resetTheme').addEventListener('click', () => {
            if (confirm('Reset all theme settings to default?')) {
                this.resetSettings();
            }
        });
    }

    togglePanel() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.panel.classList.add('open');
        } else {
            this.panel.classList.remove('open');
        }
    }

    updateActivePreset(activeBtn) {
        document.querySelectorAll('.color-preset').forEach(btn => {
            btn.classList.remove('active');
        });
        activeBtn.classList.add('active');
    }

    updateButtonGroup(activeBtn) {
        const group = activeBtn.parentElement;
        group.querySelectorAll('.btn-group-item').forEach(btn => {
            btn.classList.remove('active');
        });
        activeBtn.classList.add('active');
    }

    applySettings() {
        const root = document.documentElement;

        // Dark mode
        if (this.settings.darkMode) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }

        // High contrast
        if (this.settings.highContrast) {
            document.body.classList.add('high-contrast');
        } else {
            document.body.classList.remove('high-contrast');
        }

        // Reduce motion
        if (this.settings.reduceMotion) {
            root.style.setProperty('--animation-duration', '0.001s');
            document.body.classList.add('reduce-motion');
        } else {
            root.style.removeProperty('--animation-duration');
            document.body.classList.remove('reduce-motion');
        }

        // Primary color - Apply to actual elements
        root.style.setProperty('--primary-color', this.settings.primaryColor);
        this.applyPrimaryColor(this.settings.primaryColor);

        // Font size
        const fontSizes = {
            small: '14px',
            medium: '16px',
            large: '18px'
        };
        root.style.setProperty('--base-font-size', fontSizes[this.settings.fontSize]);
        document.body.style.fontSize = fontSizes[this.settings.fontSize];

        // Corner radius
        const radiusValues = {
            sharp: '4px',
            medium: '8px',
            round: '16px'
        };
        root.style.setProperty('--border-radius', radiusValues[this.settings.cornerRadius]);
        this.applyBorderRadius(radiusValues[this.settings.cornerRadius]);

        // Animation speed
        const speedValues = {
            slow: '0.6s',
            normal: '0.3s',
            fast: '0.15s'
        };
        root.style.setProperty('--transition-speed', speedValues[this.settings.animationSpeed]);
        this.applyAnimationSpeed(speedValues[this.settings.animationSpeed]);
    }

    applyPrimaryColor(color) {
        // Create dynamic stylesheet if not exists
        let styleId = 'theme-customizer-dynamic';
        let styleEl = document.getElementById(styleId);

        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = styleId;
            document.head.appendChild(styleEl);
        }

        // Calculate secondary color (lighter version)
        const secondaryColor = this.lightenColor(color, 20);

        styleEl.textContent = `
            /* Dynamic Theme Colors */
            .gradient-bg,
            .btn-primary,
            button.gradient-bg,
            a.gradient-bg {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%) !important;
            }

            .text-orange-500,
            .gradient-text {
                color: ${color} !important;
            }

            .gradient-text {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .border-orange-500 {
                border-color: ${color} !important;
            }

            .hover\\:text-orange-500:hover {
                color: ${color} !important;
            }

            .hover\\:border-orange-500:hover {
                border-color: ${color} !important;
            }

            .focus\\:border-orange-500:focus {
                border-color: ${color} !important;
            }

            .bg-orange-500 {
                background-color: ${color} !important;
            }

            /* Scroll to top button */
            #scroll-to-top {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%) !important;
            }

            /* Toast notifications */
            .toast.info {
                border-left-color: ${color} !important;
                color: ${color} !important;
            }

            .toast.info .toast-icon {
                background: ${color} !important;
            }

            /* Theme customizer toggle */
            .customizer-toggle {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%) !important;
            }

            .customizer-header {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%) !important;
            }

            /* Buttons */
            .btn-group-item.active {
                background: linear-gradient(135deg, ${color} 0%, ${secondaryColor} 100%) !important;
                border-color: ${color} !important;
            }

            .btn-group-item:hover {
                border-color: ${color} !important;
                color: ${color} !important;
            }

            /* Links */
            .nav-link::after {
                background: ${color} !important;
            }

            .wave-input.has-value label,
            .wave-input.focused label {
                color: ${color} !important;
            }

            .wave-input::after {
                background: linear-gradient(90deg, ${color}, ${secondaryColor}) !important;
            }

            /* Progress bars */
            .progress-bar,
            .scroll-progress {
                background: linear-gradient(90deg, ${color} 0%, ${secondaryColor} 100%) !important;
            }

            /* Checkbox */
            .checkbox-micro input:checked + .checkmark {
                background: linear-gradient(135deg, ${color}, ${secondaryColor}) !important;
                border-color: ${color} !important;
            }

            /* Switch */
            .switch-micro input:checked + .switch-slider {
                background: linear-gradient(135deg, ${color}, ${secondaryColor}) !important;
            }
        `;
    }

    applyBorderRadius(radius) {
        document.querySelectorAll('button, .btn, input, textarea, select, .card, .rounded-lg, .rounded-xl, .rounded-2xl').forEach(el => {
            if (!el.classList.contains('rounded-full')) {
                el.style.borderRadius = radius;
            }
        });
    }

    applyAnimationSpeed(speed) {
        document.querySelectorAll('*').forEach(el => {
            const currentTransition = window.getComputedStyle(el).transition;
            if (currentTransition && currentTransition !== 'none' && currentTransition !== 'all 0s ease 0s') {
                el.style.transitionDuration = speed;
            }
        });
    }

    lightenColor(color, percent) {
        const num = parseInt(color.replace("#",""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 +
            (G<255?G<1?0:G:255)*0x100 +
            (B<255?B<1?0:B:255))
            .toString(16).slice(1);
    }

    resetSettings() {
        this.settings = {
            primaryColor: '#FF6B35',
            secondaryColor: '#F7931E',
            darkMode: false,
            fontSize: 'medium',
            cornerRadius: 'medium',
            animationSpeed: 'normal',
            reduceMotion: false,
            highContrast: false
        };

        this.saveSettings();
        this.applySettings();

        // Update UI
        document.getElementById('darkModeToggle').checked = false;
        document.getElementById('highContrastToggle').checked = false;
        document.getElementById('reduceMotionToggle').checked = false;
        document.getElementById('primaryColorPicker').value = '#FF6B35';

        document.querySelectorAll('.btn-group-item').forEach(btn => {
            btn.classList.remove('active');
            if ((btn.dataset.size === 'medium') ||
                (btn.dataset.radius === 'medium') ||
                (btn.dataset.speed === 'normal')) {
                btn.classList.add('active');
            }
        });

        if (window.Toast) {
            Toast.success('Theme reset to default settings');
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.themeCustomizer = new ThemeCustomizer();
});

// Export
window.ThemeCustomizer = ThemeCustomizer;
