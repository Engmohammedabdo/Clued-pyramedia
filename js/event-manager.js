/**
 * PYRAMEDIA - Event Manager
 * Centralized event handling system
 * Replaces inline event handlers with proper event delegation
 * @version 2.0
 */

class EventManager {
    constructor() {
        this.init();
    }

    /**
     * Initialize all event listeners
     */
    init() {
        this.initBookingEvents();
        this.initServiceModalEvents();
        this.initFormEvents();
        this.initNavigationEvents();
        this.initKeyboardNavigation();
    }

    /**
     * Booking Modal Events
     */
    initBookingEvents() {
        // Open booking modal
        document.querySelectorAll('[data-action="booking"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openBookingModal();
            });
        });

        // Close booking modal
        const closeBookingBtn = document.querySelector('[data-action="close-booking"]');
        if (closeBookingBtn) {
            closeBookingBtn.addEventListener('click', () => this.closeBookingModal());
        }

        // Close modal on backdrop click
        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('click', (e) => {
                if (e.target === bookingModal) {
                    this.closeBookingModal();
                }
            });
        }

        // Calendar navigation
        const prevMonthBtn = document.querySelector('[data-action="prev-month"]');
        const nextMonthBtn = document.querySelector('[data-action="next-month"]');

        if (prevMonthBtn) {
            prevMonthBtn.addEventListener('click', () => this.previousMonth());
        }

        if (nextMonthBtn) {
            nextMonthBtn.addEventListener('click', () => this.nextMonth());
        }

        // Continue to step 2
        const continueBtn = document.getElementById('continueToStep2');
        if (continueBtn) {
            continueBtn.addEventListener('click', () => this.showBookingStep2());
        }

        // Back to step 1
        const backBtn = document.querySelector('[data-action="back-to-step1"]');
        if (backBtn) {
            backBtn.addEventListener('click', () => this.showBookingStep1());
        }

        // Calendar day selection - using event delegation
        const calendarDays = document.getElementById('calendarDays');
        if (calendarDays) {
            calendarDays.addEventListener('click', (e) => {
                const day = e.target.closest('.calendar-day');
                if (day && !day.classList.contains('disabled')) {
                    this.selectCalendarDay(day);
                }
            });
        }

        // Time slot selection - using event delegation
        const timeSlots = document.getElementById('timeSlots');
        if (timeSlots) {
            timeSlots.addEventListener('click', (e) => {
                const slot = e.target.closest('.time-slot');
                if (slot && !slot.classList.contains('disabled')) {
                    this.selectTimeSlot(slot);
                }
            });
        }
    }

    /**
     * Service Modal Events
     */
    initServiceModalEvents() {
        // Open service modals
        document.querySelectorAll('[data-service]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const service = btn.getAttribute('data-service');
                this.openServiceModal(service);
            });
        });

        // Close service modal
        const closeServiceBtn = document.querySelector('[data-action="close-service"]');
        if (closeServiceBtn) {
            closeServiceBtn.addEventListener('click', () => this.closeServiceModal());
        }

        // Close modal on backdrop click
        const serviceModal = document.getElementById('serviceModal');
        if (serviceModal) {
            serviceModal.addEventListener('click', (e) => {
                if (e.target === serviceModal) {
                    this.closeServiceModal();
                }
            });
        }
    }

    /**
     * Form Events - Enhanced Validation
     */
    initFormEvents() {
        // Contact form
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => this.handleContactForm(e));
        }

        // Booking form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => this.handleBookingForm(e));
        }

        // Service inquiry form
        const serviceForm = document.getElementById('serviceInquiryForm');
        if (serviceForm) {
            serviceForm.addEventListener('submit', (e) => this.handleServiceInquiry(e));
        }

        // Newsletter form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => this.handleNewsletterForm(e));
        }

        // Real-time validation
        this.initRealtimeValidation();
    }

    /**
     * Navigation Events
     */
    initNavigationEvents() {
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('active');

                // Update icon
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');
                }
            });

            // Close mobile menu when clicking on links
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href === '#' || href === '') return;

                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offset = 80; // navbar height
                    const targetPosition = target.offsetTop - offset;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Keyboard Navigation for Accessibility
     */
    initKeyboardNavigation() {
        // ESC to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }

            // Tab trap for modals
            if (e.key === 'Tab') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    this.trapFocus(e, activeModal);
                }
            }
        });
    }

    /**
     * Real-time Form Validation
     */
    initRealtimeValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        });
    }

    /**
     * Validate Individual Field
     */
    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        let isValid = true;
        let errorMessage = '';

        // Check if required
        if (required && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }

        // Phone validation
        if (type === 'tel' && value) {
            const phoneRegex = /^[\d\s\+\-\(\)]+$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }

        // Update UI
        this.updateFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    /**
     * Update Field Validation UI
     */
    updateFieldValidation(field, isValid, errorMessage) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        let errorElement = formGroup.querySelector('.error-message');

        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.className = 'error-message';
            errorElement.setAttribute('role', 'alert');
            formGroup.appendChild(errorElement);
        }

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            errorElement.classList.remove('active');
            field.setAttribute('aria-invalid', 'false');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            errorElement.textContent = errorMessage;
            errorElement.classList.add('active');
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', errorElement.id || 'error-' + field.name);
        }
    }

    /**
     * Handle Contact Form Submission
     */
    async handleContactForm(e) {
        e.preventDefault();
        const form = e.target;

        // Validate all fields
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            if (window.Toast) {
                window.Toast.error('Please fill all required fields correctly');
            }
            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

        try {
            const formData = new FormData(form);
            const response = await fetch('php/contact-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (window.Toast) {
                    window.Toast.success('Message sent successfully! We\'ll get back to you soon.');
                }
                form.reset();
                // Remove validation classes
                inputs.forEach(input => {
                    input.classList.remove('is-valid', 'is-invalid');
                });
            } else {
                throw new Error(result.message || 'Failed to send message');
            }
        } catch (error) {
            console.error('Contact form error:', error);
            if (window.Toast) {
                window.Toast.error('Failed to send message. Please try again.');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    /**
     * Handle Booking Form Submission
     */
    async handleBookingForm(e) {
        e.preventDefault();
        const form = e.target;

        // Validate
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            if (window.Toast) {
                window.Toast.error('Please fill all required fields correctly');
            }
            return;
        }

        // Get selected date and time
        const selectedDate = document.querySelector('.calendar-day.selected');
        const selectedTime = document.querySelector('.time-slot.selected');

        if (!selectedDate || !selectedTime) {
            if (window.Toast) {
                window.Toast.error('Please select a date and time');
            }
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirming...';

        try {
            const formData = new FormData(form);
            formData.append('date', selectedDate.dataset.date);
            formData.append('time', selectedTime.dataset.time);

            const response = await fetch('php/booking-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (window.Toast) {
                    window.Toast.success('Booking confirmed! Check your email for details.');
                }
                form.reset();
                this.closeBookingModal();
            } else {
                throw new Error(result.message || 'Failed to book consultation');
            }
        } catch (error) {
            console.error('Booking form error:', error);
            if (window.Toast) {
                window.Toast.error('Failed to confirm booking. Please try again.');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    /**
     * Handle Service Inquiry Form
     */
    async handleServiceInquiry(e) {
        e.preventDefault();
        const form = e.target;

        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            if (window.Toast) {
                window.Toast.error('Please fill all required fields correctly');
            }
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';

        try {
            const formData = new FormData(form);
            const response = await fetch('php/service-inquiry.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (window.Toast) {
                    window.Toast.success('Inquiry submitted! We\'ll contact you soon.');
                }
                form.reset();
                this.closeServiceModal();
            } else {
                throw new Error(result.message || 'Failed to submit inquiry');
            }
        } catch (error) {
            console.error('Service inquiry error:', error);
            if (window.Toast) {
                window.Toast.error('Failed to submit inquiry. Please try again.');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    /**
     * Handle Newsletter Form
     */
    async handleNewsletterForm(e) {
        e.preventDefault();
        const form = e.target;
        const emailInput = form.querySelector('input[type="email"]');

        if (!this.validateField(emailInput)) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const formData = new FormData(form);
            const response = await fetch('php/newsletter-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (window.Toast) {
                    window.Toast.success('Successfully subscribed to newsletter!');
                }
                form.reset();
                emailInput.classList.remove('is-valid', 'is-invalid');
            } else {
                throw new Error(result.message || 'Failed to subscribe');
            }
        } catch (error) {
            console.error('Newsletter error:', error);
            if (window.Toast) {
                window.Toast.error('Failed to subscribe. Please try again.');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    }

    /**
     * Modal Functions
     */
    openBookingModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Focus first input
            setTimeout(() => {
                const firstInput = modal.querySelector('input, button');
                if (firstInput) firstInput.focus();
            }, 100);

            // Initialize calendar if function exists
            if (typeof window.initCalendar === 'function') {
                window.initCalendar();
            }
        }
    }

    closeBookingModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            // Reset to step 1
            this.showBookingStep1();
        }
    }

    openServiceModal(service) {
        const modal = document.getElementById('serviceModal');
        if (modal && typeof window.openServiceModal === 'function') {
            window.openServiceModal(service);
        }
    }

    closeServiceModal() {
        const modal = document.getElementById('serviceModal');
        if (modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    closeAllModals() {
        this.closeBookingModal();
        this.closeServiceModal();
    }

    showBookingStep1() {
        const step1 = document.getElementById('bookingStep1');
        const step2 = document.getElementById('bookingStep2');
        if (step1 && step2) {
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
        }
    }

    showBookingStep2() {
        const step1 = document.getElementById('bookingStep1');
        const step2 = document.getElementById('bookingStep2');
        const selectedDate = document.querySelector('.calendar-day.selected');
        const selectedTime = document.querySelector('.time-slot.selected');

        if (!selectedDate || !selectedTime) {
            if (window.Toast) {
                window.Toast.warning('Please select a date and time first');
            }
            return;
        }

        if (step1 && step2) {
            step1.classList.add('hidden');
            step2.classList.remove('hidden');

            // Update appointment display
            const display = document.getElementById('appointmentDisplay');
            if (display) {
                display.textContent = `${selectedDate.dataset.date} at ${selectedTime.dataset.time}`;
            }
        }
    }

    selectCalendarDay(dayElement) {
        // Remove previous selection
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
            day.setAttribute('aria-selected', 'false');
        });

        // Select new day
        dayElement.classList.add('selected');
        dayElement.setAttribute('aria-selected', 'true');

        // Load time slots if function exists
        if (typeof window.loadTimeSlots === 'function') {
            window.loadTimeSlots(dayElement.dataset.date);
        }
    }

    selectTimeSlot(slotElement) {
        // Remove previous selection
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
            slot.setAttribute('aria-selected', 'false');
        });

        // Select new slot
        slotElement.classList.add('selected');
        slotElement.setAttribute('aria-selected', 'true');

        // Enable continue button
        const continueBtn = document.getElementById('continueToStep2');
        if (continueBtn) {
            continueBtn.disabled = false;
        }
    }

    previousMonth() {
        if (typeof window.previousMonth === 'function') {
            window.previousMonth();
        }
    }

    nextMonth() {
        if (typeof window.nextMonth === 'function') {
            window.nextMonth();
        }
    }

    /**
     * Trap focus within modal for accessibility
     */
    trapFocus(e, modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey && document.activeElement === firstElement) {
            e.preventDefault();
            lastElement.focus();
        } else if (!e.shiftKey && document.activeElement === lastElement) {
            e.preventDefault();
            firstElement.focus();
        }
    }
}

// Initialize Event Manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pyramediaEventManager = new EventManager();
    });
} else {
    window.pyramediaEventManager = new EventManager();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EventManager;
}
