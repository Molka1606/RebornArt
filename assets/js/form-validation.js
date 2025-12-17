/**
 * Comprehensive Form Validation System
 * Provides real-time validation for all input fields
 */

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) return;
        
        this.errors = {};
        this.init();
    }

    init() {
        // Add validation styles
        this.addValidationStyles();
        
        // Add event listeners to all inputs
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            // Real-time validation on blur
            input.addEventListener('blur', () => this.validateField(input));
            // Clear errors on input
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        // Form submission validation
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                this.showFormErrors();
            }
        });
    }

    addValidationStyles() {
        if (document.getElementById('validation-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'validation-styles';
        style.textContent = `
            .form-control.is-invalid {
                border-color: #dc3545;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                padding-right: calc(1.5em + 0.75rem);
            }
            .form-control.is-valid {
                border-color: #198754;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                padding-right: calc(1.5em + 0.75rem);
            }
            .invalid-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875em;
                color: #dc3545;
            }
            .valid-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875em;
                color: #198754;
            }
        `;
        document.head.appendChild(style);
    }

    validateField(field) {
        const fieldName = field.name || field.id;
        const value = field.value.trim();
        let error = null;

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            error = this.getFieldLabel(field) + ' is required';
        }
        // Email validation
        else if (field.type === 'email' && value) {
            if (!this.isValidEmail(value)) {
                error = 'Please enter a valid email address';
            }
        }
        // Date validation (only for future dates on add forms, allow past on edit)
        else if (field.type === 'date' && value) {
            // Check if this is an edit form (has hidden id field)
            const form = field.closest('form');
            const isEditForm = form && form.querySelector('input[type="hidden"][name="id"]');
            
            if (!isEditForm && !this.isValidDate(field)) {
                error = 'Date cannot be in the past';
            }
        }
        // File validation
        else if (field.type === 'file' && field.files.length > 0) {
            error = this.validateFile(field);
        }
        // Text length validation
        else if (field.hasAttribute('maxlength')) {
            const maxLength = parseInt(field.getAttribute('maxlength'));
            if (value.length > maxLength) {
                error = `Maximum ${maxLength} characters allowed`;
            }
        }
        // Text minimum length
        else if (field.hasAttribute('minlength')) {
            const minLength = parseInt(field.getAttribute('minlength'));
            if (value.length < minLength) {
                error = `Minimum ${minLength} characters required`;
            }
        }
        // Select validation
        else if (field.tagName === 'SELECT' && field.hasAttribute('required')) {
            if (!value || value === '') {
                error = 'Please select an option';
            }
        }

        // Show/hide error
        if (error) {
            this.showFieldError(field, error);
            this.errors[fieldName] = error;
            return false;
        } else {
            this.showFieldSuccess(field);
            delete this.errors[fieldName];
            return true;
        }
    }

    validateFile(field) {
        const file = field.files[0];
        if (!file) return null;

        // File type validation
        const allowedTypes = field.getAttribute('accept');
        if (allowedTypes) {
            const types = allowedTypes.split(',').map(t => t.trim());
            const fileType = file.type;
            const fileName = file.name.toLowerCase();
            
            let isValid = false;
            types.forEach(type => {
                if (type === 'image/*' && fileType.startsWith('image/')) {
                    isValid = true;
                } else if (fileType === type || fileName.endsWith(type.replace('*', ''))) {
                    isValid = true;
                }
            });
            
            if (!isValid) {
                return 'Invalid file type. Allowed: ' + allowedTypes;
            }
        }

        // File size validation (default 5MB)
        const maxSize = field.getAttribute('data-max-size') || 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(2);
            return `File size exceeds ${maxSizeMB}MB limit`;
        }

        return null;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidDate(dateField) {
        const selectedDate = new Date(dateField.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return selectedDate >= today;
    }

    showFieldError(field, message) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = field.parentElement.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
    }

    showFieldSuccess(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        // Remove error message
        const existingError = field.parentElement.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    clearFieldError(field) {
        if (field.classList.contains('is-invalid')) {
            field.classList.remove('is-invalid');
            const errorDiv = field.parentElement.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }

    validateForm() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFormErrors() {
        if (Object.keys(this.errors).length > 0) {
            const firstError = Object.values(this.errors)[0];
            alert('Please fix the following errors:\n\n' + firstError);
            
            // Scroll to first error
            const firstInvalidField = this.form.querySelector('.is-invalid');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
        }
    }

    getFieldLabel(field) {
        // Try to get label
        const label = field.parentElement.querySelector('label');
        if (label) {
            return label.textContent.replace('*', '').trim();
        }
        
        // Try placeholder
        if (field.placeholder) {
            return field.placeholder;
        }
        
        // Use field name
        return field.name || field.id || 'This field';
    }
}

// Initialize validators for all forms when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Reservation form
    if (document.getElementById('reservation-form')) {
        new FormValidator('reservation-form');
    }

    // Event add form
    const eventAddForm = document.querySelector('form[action*="event_add_submit"]');
    if (eventAddForm) {
        eventAddForm.id = 'event-add-form';
        new FormValidator('event-add-form');
    }

    // Event edit form
    const eventEditForm = document.querySelector('form[action*="event_update"]');
    if (eventEditForm) {
        eventEditForm.id = 'event-edit-form';
        new FormValidator('event-edit-form');
    }

    // Public event add form
    const publicEventForm = document.querySelector('form[action*="event_add_public_submit"]');
    if (publicEventForm) {
        publicEventForm.id = 'public-event-form';
        new FormValidator('public-event-form');
    }

    // Edit reservation form
    const editReservationForm = document.querySelector('form[action*="action=update"]');
    if (editReservationForm && editReservationForm.querySelector('input[name="id"]')) {
        editReservationForm.id = 'edit-reservation-form';
        new FormValidator('edit-reservation-form');
    }
});

// Additional validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateDate(dateString) {
    const selectedDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return selectedDate >= today;
}

function validateFileSize(file, maxSizeMB = 5) {
    const maxSize = maxSizeMB * 1024 * 1024;
    return file.size <= maxSize;
}

function validateFileType(file, allowedTypes) {
    if (!allowedTypes) return true;
    const types = allowedTypes.split(',').map(t => t.trim());
    return types.some(type => {
        if (type === 'image/*') {
            return file.type.startsWith('image/');
        }
        return file.type === type || file.name.toLowerCase().endsWith(type.replace('*', ''));
    });
}

