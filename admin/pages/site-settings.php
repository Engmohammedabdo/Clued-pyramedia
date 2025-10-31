<?php
/**
 * PYRAMEDIA - Comprehensive Site Settings
 * Admin Panel - Full Site Control
 */

require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Site Settings';
$currentPage = 'settings';

include '../includes/header.php';
?>

<div class="page-header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cog mr-2"></i>
                Site Settings
            </h1>
            <p class="text-gray-600">Comprehensive control over website appearance and functionality</p>
        </div>
        <button id="saveAllSettings" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
            <i class="fas fa-save mr-2"></i>
            Save All Changes
        </button>
    </div>
</div>

<!-- Settings Tabs -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="settings-tab active" data-tab="general">
                <i class="fas fa-home mr-2"></i>
                General
            </button>
            <button class="settings-tab" data-tab="appearance">
                <i class="fas fa-palette mr-2"></i>
                Appearance
            </button>
            <button class="settings-tab" data-tab="homepage">
                <i class="fas fa-desktop mr-2"></i>
                Homepage
            </button>
            <button class="settings-tab" data-tab="navigation">
                <i class="fas fa-bars mr-2"></i>
                Navigation
            </button>
            <button class="settings-tab" data-tab="footer">
                <i class="fas fa-footer mr-2"></i>
                Footer
            </button>
            <button class="settings-tab" data-tab="seo">
                <i class="fas fa-search mr-2"></i>
                SEO
            </button>
            <button class="settings-tab" data-tab="advanced">
                <i class="fas fa-code mr-2"></i>
                Advanced
            </button>
        </nav>
    </div>
</div>

<!-- Settings Content -->
<div class="settings-content">
    <!-- General Settings -->
    <div id="tab-general" class="settings-panel active">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-blue-600"></i>
                General Settings
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Title</label>
                    <input type="text" id="siteTitle" value="PYRAMEDIA" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tagline</label>
                    <input type="text" id="siteTagline" value="Marketing & Media Solutions" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Email</label>
                    <input type="email" id="siteEmail" value="info@pyramedia.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Phone</label>
                    <input type="tel" id="sitePhone" value="+971 50 123 4567" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Description</label>
                    <textarea id="siteDescription" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">Leading marketing & media agency in the GCC region. We empower youth through the power of media and innovation.</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                    <select id="siteTimezone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="UTC">UTC</option>
                        <option value="Asia/Dubai" selected>Dubai (Asia/Dubai)</option>
                        <option value="Asia/Riyadh">Riyadh (Asia/Riyadh)</option>
                        <option value="Europe/London">London (Europe/London)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Language</label>
                    <select id="siteLanguage" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="en" selected>English</option>
                        <option value="ar">Arabic</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Appearance Settings -->
    <div id="tab-appearance" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-palette mr-3 text-purple-600"></i>
                Appearance Settings
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Color Scheme -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Color Scheme</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                            <div class="flex items-center gap-4">
                                <input type="color" id="primaryColor" value="#FF6B35" class="w-20 h-12 rounded cursor-pointer">
                                <input type="text" id="primaryColorHex" value="#FF6B35" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                            <div class="flex items-center gap-4">
                                <input type="color" id="secondaryColor" value="#F7931E" class="w-20 h-12 rounded cursor-pointer">
                                <input type="text" id="secondaryColorHex" value="#F7931E" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Typography</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Heading Font</label>
                            <select id="headingFont" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="Poppins" selected>Poppins</option>
                                <option value="Roboto">Roboto</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Inter">Inter</option>
                                <option value="Cairo">Cairo (Arabic)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Body Font</label>
                            <select id="bodyFont" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="Poppins" selected>Poppins</option>
                                <option value="Roboto">Roboto</option>
                                <option value="Open Sans">Open Sans</option>
                                <option value="Lato">Lato</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Base Font Size</label>
                            <select id="baseFontSize" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="14px">Small (14px)</option>
                                <option value="16px" selected>Medium (16px)</option>
                                <option value="18px">Large (18px)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Layout Options -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Layout Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Corner Radius</label>
                            <select id="cornerRadius" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="4px">Sharp (4px)</option>
                                <option value="8px" selected>Medium (8px)</option>
                                <option value="16px">Round (16px)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Container Width</label>
                            <select id="containerWidth" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="1140px">Normal (1140px)</option>
                                <option value="1280px" selected>Wide (1280px)</option>
                                <option value="1536px">Extra Wide (1536px)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Animation Speed</label>
                            <select id="animationSpeed" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="0.15s">Fast (0.15s)</option>
                                <option value="0.3s" selected>Normal (0.3s)</option>
                                <option value="0.6s">Slow (0.6s)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Feature Toggles -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Feature Toggles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Dark Mode</span>
                            <input type="checkbox" id="enableDarkMode" class="w-5 h-5 text-orange-600">
                        </label>

                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Loading Screen</span>
                            <input type="checkbox" id="enableLoadingScreen" checked class="w-5 h-5 text-orange-600">
                        </label>

                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Scroll to Top</span>
                            <input type="checkbox" id="enableScrollTop" checked class="w-5 h-5 text-orange-600">
                        </label>

                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Particle Effects</span>
                            <input type="checkbox" id="enableParticles" checked class="w-5 h-5 text-orange-600">
                        </label>

                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Animations</span>
                            <input type="checkbox" id="enableAnimations" checked class="w-5 h-5 text-orange-600">
                        </label>

                        <label class="flex items-center justify-between p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="font-medium">Toast Notifications</span>
                            <input type="checkbox" id="enableToast" checked class="w-5 h-5 text-orange-600">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Homepage Settings -->
    <div id="tab-homepage" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-home mr-3 text-green-600"></i>
                Homepage Settings
            </h2>

            <div class="space-y-8">
                <!-- Hero Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">Hero Section</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Title</label>
                            <input type="text" id="heroTitle" value="Transform Your Digital Presence" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Subtitle</label>
                            <textarea id="heroSubtitle" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">Leading marketing & media agency in the GCC region.</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Primary CTA Text</label>
                                <input type="text" id="heroCTA1" value="Free Consultation" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary CTA Text</label>
                                <input type="text" id="heroCTA2" value="Explore Services" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <label class="flex items-center">
                            <input type="checkbox" id="enableVideoBackground" class="w-5 h-5 text-orange-600 mr-2">
                            <span>Enable Video Background</span>
                        </label>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">Statistics Section</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Projects</label>
                            <input type="number" id="statProjects" value="500" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Clients</label>
                            <input type="number" id="statClients" value="200" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Experience (Years)</label>
                            <input type="number" id="statExperience" value="15" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Satisfaction (%)</label>
                            <input type="number" id="statSatisfaction" value="98" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Settings -->
    <div id="tab-navigation" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6">Navigation Settings</h2>
            <p class="text-gray-600">Configure navigation menu items and settings.</p>
        </div>
    </div>

    <!-- Footer Settings -->
    <div id="tab-footer" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6">Footer Settings</h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Text</label>
                    <input type="text" id="footerText" value="© 2025 PYRAMEDIA. All rights reserved." class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-3">Social Media Links</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="url" id="facebookUrl" placeholder="Facebook URL" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <input type="url" id="instagramUrl" placeholder="Instagram URL" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <input type="url" id="linkedinUrl" placeholder="LinkedIn URL" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <input type="url" id="twitterUrl" placeholder="Twitter URL" class="px-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Settings -->
    <div id="tab-seo" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6">SEO Settings</h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                    <textarea id="metaDescription" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Keywords</label>
                    <input type="text" id="metaKeywords" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Google Analytics ID</label>
                    <input type="text" id="googleAnalyticsId" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Settings -->
    <div id="tab-advanced" class="settings-panel">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-6 text-red-600">Advanced Settings</h2>
            <p class="text-red-600 mb-6">⚠️ Warning: Changes here can affect site functionality</p>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Custom CSS</label>
                    <textarea id="customCSS" rows="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm" placeholder="/* Add your custom CSS here */"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Custom JavaScript</label>
                    <textarea id="customJS" rows="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm" placeholder="// Add your custom JavaScript here"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Header Scripts</label>
                    <textarea id="headerScripts" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm" placeholder="<!-- Scripts to inject in <head> -->"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-tab {
    padding: 1rem 1.5rem;
    border-bottom: 2px solid transparent;
    font-weight: 600;
    color: #666;
    transition: all 0.3s ease;
}

.settings-tab:hover {
    color: #FF6B35;
    border-bottom-color: #FF6B35;
}

.settings-tab.active {
    color: #FF6B35;
    border-bottom-color: #FF6B35;
}

.settings-panel {
    display: none;
}

.settings-panel.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Tab Switching
document.querySelectorAll('.settings-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const tabName = tab.dataset.tab;

        // Update active tab
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Show corresponding panel
        document.querySelectorAll('.settings-panel').forEach(panel => panel.classList.remove('active'));
        document.getElementById(`tab-${tabName}`).classList.add('active');
    });
});

// Color Sync
document.getElementById('primaryColor').addEventListener('input', (e) => {
    document.getElementById('primaryColorHex').value = e.target.value;
});

document.getElementById('primaryColorHex').addEventListener('input', (e) => {
    document.getElementById('primaryColor').value = e.target.value;
});

document.getElementById('secondaryColor').addEventListener('input', (e) => {
    document.getElementById('secondaryColorHex').value = e.target.value;
});

document.getElementById('secondaryColorHex').addEventListener('input', (e) => {
    document.getElementById('secondaryColor').value = e.target.value;
});

// Save Settings
document.getElementById('saveAllSettings').addEventListener('click', () => {
    const settings = {
        general: {
            siteTitle: document.getElementById('siteTitle').value,
            siteTagline: document.getElementById('siteTagline').value,
            siteEmail: document.getElementById('siteEmail').value,
            sitePhone: document.getElementById('sitePhone').value,
            siteDescription: document.getElementById('siteDescription').value,
            siteTimezone: document.getElementById('siteTimezone').value,
            siteLanguage: document.getElementById('siteLanguage').value
        },
        appearance: {
            primaryColor: document.getElementById('primaryColor').value,
            secondaryColor: document.getElementById('secondaryColor').value,
            headingFont: document.getElementById('headingFont').value,
            bodyFont: document.getElementById('bodyFont').value,
            baseFontSize: document.getElementById('baseFontSize').value,
            cornerRadius: document.getElementById('cornerRadius').value,
            containerWidth: document.getElementById('containerWidth').value,
            animationSpeed: document.getElementById('animationSpeed').value
        },
        features: {
            darkMode: document.getElementById('enableDarkMode').checked,
            loadingScreen: document.getElementById('enableLoadingScreen').checked,
            scrollTop: document.getElementById('enableScrollTop').checked,
            particles: document.getElementById('enableParticles').checked,
            animations: document.getElementById('enableAnimations').checked,
            toast: document.getElementById('enableToast').checked
        }
    };

    // Save to localStorage for demo
    localStorage.setItem('pyramedia_settings', JSON.stringify(settings));

    if (window.Toast) {
        Toast.success('All settings saved successfully!');
    } else {
        alert('Settings saved successfully!');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
