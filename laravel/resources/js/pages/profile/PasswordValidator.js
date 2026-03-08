export function initProfilePasswordValidator() {
    const forms = document.querySelectorAll('.form-password');
    if (forms.length === 0) return;

    const validationRules = {
        current_password: {
            required: { value: true, message: 'Current password is required' }
        },
        password: {
            required: { value: true, message: 'New password is required' },
            min: { value: 8, message: 'Password must be at least 8 characters' },

            // hasUppercase: { value: true, message: 'Password must contain at least one uppercase letter' },
            // hasLowercase: { value: true, message: 'Password must contain at least one lowercase letter' },
            // hasNumber: { value: true, message: 'Password must contain at least one number' }
        },
        password_confirmation: {
            required: { value: true, message: 'Please confirm your new password' },
            match: { value: 'password', message: 'Passwords do not match' }
        }
    };

    const validateField = (input, form) => {
        const rules = validationRules[input.name];
        if (!rules) return true;

        const value = input.value.trim();
        const group = input.closest('.form__group');
        const errorDiv = group?.querySelector('.form-error');

        const show = (msg) => {
            input.classList.add('input-error');
            if (errorDiv) {
                errorDiv.textContent = msg;
                errorDiv.classList.add('active');
            }
        };

        const hide = () => {
            input.classList.remove('input-error');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.classList.remove('active');
            }
        };

        if (rules.required && !value) {
            show(rules.required.message);
            return false;
        }

        if (rules.min && value.length < rules.min.value) {
            show(rules.min.message);
            return false;
        }

        /*
        if (rules.hasUppercase && value && !/[A-Z]/.test(value)) {
            show(rules.hasUppercase.message);
            return false;
        }

        if (rules.hasLowercase && value && !/[a-z]/.test(value)) {
            show(rules.hasLowercase.message);
            return false;
        }

        if (rules.hasNumber && value && !/[0-9]/.test(value)) {
            show(rules.hasNumber.message);
            return false;
        }
        */

        if (rules.match) {
            const target = form.querySelector(`input[name="${rules.match.value}"]`);
            if (target && value !== target.value) {
                show(rules.match.message);
                return false;
            }
        }

        hide();
        return true;
    };

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.classList.contains('input-error')) {
                    validateField(input, form);
                }
            });

            input.addEventListener('blur', () => validateField(input, form));
        });

        form.addEventListener('submit', (e) => {
            let isFormValid = true;

            inputs.forEach(input => {
                if (!validateField(input, form)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                const firstError = form.querySelector('.form-error.active');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
}