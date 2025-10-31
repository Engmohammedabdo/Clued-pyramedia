/**
 * PYRAMEDIA - Form Validator
 * Professional, reusable form validation library
 * Inspired by Parsley.js but lightweight and customized
 * @version 2.0
 */

class FormValidator {
    constructor(formSelector, options = {}) {
        this.form = typeof formSelector === 'string'
            ? document.querySelector(formSelector)
            : formSelector;

        if (!this.form) {
            console.error('Form not found:', formSelector);
            return;
        }

        this.options = {
            validateOnBlur: true,
            validateOnInput: true,
            showErrors: true,
            errorClass: 'is-invalid',
            successClass: 'is-valid',
            errorMessageClass: 'error-message',
            ...options
        };

        this.validators = {
            required: this.validateRequired,
            email: this.validateEmail,
            phone: this.validatePhone,
            minLength: this.validateMinLength,
            maxLength: this.validateMaxLength,
            pattern: this.validatePattern,
            match: this.validateMatch,
            url: this.validateURL,
            number: this.validateNumber,
            min: this.validateMin,
            max: this.validateMax
        };

        this.customValidators = {};
        this.errors = {};

        this.init();
    }

    /**
     * Initialize validator
     */
    init() {
        this.fields = Array.from(this.form.querySelectorAll('input, textarea, select'));
        this.attachEventListeners();
    }

    /**
     * Attach event listeners to form fields
     */
    attachEventListeners() {
        this.fields.forEach(field => {
            if (this.options.validateOnBlur) {
                field.addEventListener('blur', () => this.validateField(field));
            }

            if (this.options.validateOnInput) {
                field.addEventListener('input', () => {
                    if (field.classList.contains(this.options.errorClass)) {
                        this.validateField(field);
                    }
                });
            }
        });

        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    /**
     * Validate single field
     */
    validateField(field) {
        const rules = this.getFieldRules(field);
        const errors = [];

        for (const [rule, param] of Object.entries(rules)) {
            const validator = this.validators[rule] || this.customValidators[rule];

            if (validator) {
                const isValid = validator.call(this, field.value, param, field);
                if (!isValid) {
                    const errorMessage = this.getErrorMessage(field, rule, param);
                    errors.push(errorMessage);
                    break; // Stop at first error
                }
            }
        }

        this.updateFieldUI(field, errors);
        this.errors[field.name] = errors;

        return errors.length === 0;
    }

    /**
     * Get validation rules for a field
     */
    getFieldRules(field) {
        const rules = {};

        // Required
        if (field.hasAttribute('required') || field.hasAttribute('data-required')) {
            rules.required = true;
        }

        // Email
        if (field.type === 'email' || field.hasAttribute('data-validate-email')) {
            rules.email = true;
        }

        // Phone
        if (field.type === 'tel' || field.hasAttribute('data-validate-phone')) {
            rules.phone = true;
        }

        // URL
        if (field.type === 'url' || field.hasAttribute('data-validate-url')) {
            rules.url = true;
        }

        // Number
        if (field.type === 'number' || field.hasAttribute('data-validate-number')) {
            rules.number = true;
        }

        // Min length
        if (field.hasAttribute('minlength')) {
            rules.minLength = parseInt(field.getAttribute('minlength'));
        }

        // Max length
        if (field.hasAttribute('maxlength')) {
            rules.maxLength = parseInt(field.getAttribute('maxlength'));
        }

        // Min value
        if (field.hasAttribute('min')) {
            rules.min = parseFloat(field.getAttribute('min'));
        }

        // Max value
        if (field.hasAttribute('max')) {
            rules.max = parseFloat(field.getAttribute('max'));
        }

        // Pattern
        if (field.hasAttribute('pattern')) {
            rules.pattern = field.getAttribute('pattern');
        }

        // Match (for password confirmation)
        if (field.hasAttribute('data-match')) {
            rules.match = field.getAttribute('data-match');
        }

        // Custom validators from data attributes
        Array.from(field.attributes).forEach(attr => {
            if (attr.name.startsWith('data-validate-')) {
                const validatorName = attr.name.replace('data-validate-', '');
                if (this.customValidators[validatorName]) {
                    rules[validatorName] = attr.value || true;
                }
            }
        });

        return rules;
    }

    /**
     * Validation functions
     */
    validateRequired(value) {
        return value.trim().length > 0;
    }

    validateEmail(value) {
        if (!value) return true; // Let required handle empty values
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(value);
    }

    validatePhone(value) {
        if (!value) return true;
        const phoneRegex = /^[\d\s\+\-\(\)]{8,}$/;
        return phoneRegex.test(value);
    }

    validateMinLength(value, min) {
        if (!value) return true;
        return value.length >= min;
    }

    validateMaxLength(value, max) {
        if (!value) return true;
        return value.length <= max;
    }

    validatePattern(value, pattern) {
        if (!value) return true;
        const regex = new RegExp(pattern);
        return regex.test(value);
    }

    validateMatch(value, targetSelector, field) {
        const targetField = this.form.querySelector(targetSelector);
        if (!targetField) return true;
        return value === targetField.value;
    }

    validateURL(value) {
        if (!value) return true;
        try {
            new URL(value);
            return true;
        } catch {
            return false;
        }
    }

    validateNumber(value) {
        if (!value) return true;
        return !isNaN(value) && !isNaN(parseFloat(value));
    }

    validateMin(value, min) {
        if (!value) return true;
        return parseFloat(value) >= min;
    }

    validateMax(value, max) {
        if (!value) return true;
        return parseFloat(value) <= max;
    }

    /**
     * Get error message for a rule
     */
    getErrorMessage(field, rule, param) {
        const customMessage = field.getAttribute(`data-error-${rule}`);
        if (customMessage) return customMessage;

        const fieldName = field.getAttribute('data-label') ||
                         field.getAttribute('placeholder') ||
                         field.name ||
                         'This field';

        const messages = {
            required: `${fieldName} is required`,
            email: 'Please enter a valid email address',
            phone: 'Please enter a valid phone number',
            minLength: `Minimum ${param} characters required`,
            maxLength: `Maximum ${param} characters allowed`,
            pattern: 'Invalid format',
            match: 'Fields do not match',
            url: 'Please enter a valid URL',
            number: 'Please enter a valid number',
            min: `Minimum value is ${param}`,
            max: `Maximum value is ${param}`
        };

        return messages[rule] || 'Invalid value';
    }

    /**
     * Update field UI
     */
    updateFieldUI(field, errors) {
        if (!this.options.showErrors) return;

        const formGroup = field.closest('.form-group') || field.parentElement;
        let errorElement = formGroup.querySelector(`.${this.options.errorMessageClass}`);

        // Create error element if it doesn't exist
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = this.options.errorMessageClass;
            errorElement.setAttribute('role', 'alert');
            errorElement.setAttribute('aria-live', 'polite');
            formGroup.appendChild(errorElement);
        }

        if (errors.length > 0) {
            // Show error
            field.classList.remove(this.options.successClass);
            field.classList.add(this.options.errorClass);
            errorElement.textContent = errors[0];
            errorElement.classList.add('active');
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', errorElement.id || `error-${field.name}`);
        } else {
            // Show success
            field.classList.remove(this.options.errorClass);
            if (field.value) {
                field.classList.add(this.options.successClass);
            }
            errorElement.textContent = '';
            errorElement.classList.remove('active');
            field.setAttribute('aria-invalid', 'false');
        }
    }

    /**
     * Validate entire form
     */
    validate() {
        let isValid = true;
        this.errors = {};

        this.fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Handle form submission
     */
    handleSubmit(e) {
        if (!this.validate()) {
            e.preventDefault();

            // Focus first invalid field
            const firstInvalidField = this.form.querySelector(`.${this.options.errorClass}`);
            if (firstInvalidField) {
                firstInvalidField.focus();

                // Scroll to field
                firstInvalidField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            // Show toast if available
            if (window.Toast) {
                window.Toast.error('Please fix the errors in the form');
            }

            return false;
        }

        return true;
    }

    /**
     * Add custom validator
     */
    addValidator(name, validatorFn, errorMessage) {
        this.customValidators[name] = validatorFn;

        // Add to error messages if provided
        if (errorMessage) {
            this.customMessages = this.customMessages || {};
            this.customMessages[name] = errorMessage;
        }
    }

    /**
     * Reset form validation
     */
    reset() {
        this.errors = {};
        this.fields.forEach(field => {
            field.classList.remove(this.options.errorClass, this.options.successClass);

            const formGroup = field.closest('.form-group') || field.parentElement;
            const errorElement = formGroup.querySelector(`.${this.options.errorMessageClass}`);

            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.remove('active');
            }

            field.setAttribute('aria-invalid', 'false');
        });

        this.form.reset();
    }

    /**
     * Destroy validator
     */
    destroy() {
        this.fields.forEach(field => {
            field.removeEventListener('blur', this.validateField);
            field.removeEventListener('input', this.validateField);
        });

        this.form.removeEventListener('submit', this.handleSubmit);
    }
}

/**
 * Auto-initialize validators for forms with data-validate attribute
 */
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('[data-validate="true"]');
    forms.forEach(form => {
        const validatorName = form.getAttribute('data-validator-name') || 'validator';
        form[validatorName] = new FormValidator(form);
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormValidator;
}

// Make available globally
window.FormValidator = FormValidator;
