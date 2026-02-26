export default function initAuthValidator() {
    const forms = document.querySelectorAll('.auth-form:not(.no-validate)');
    
    if (forms.length === 0) return;

    const validationRules = {
    name: {
        required: { value: true, message: 'Full name is required' }
    },
    email: {
        required: { value: true, message: 'Email address is required' },
        isEmail: { value: true, message: 'This email format is incorrect' }
    },
    password: {
        required: { value: true, message: 'Password is required' },
        min: { value: 8, message: 'Password must be at least 8 characters' }
    },
    password_confirmation: {
        required: { value: true, message: 'Please confirm your password' },
        match: { value: 'password', message: 'Passwords do not match' }
    },
    birth: {
        required: { value: true, message: 'Please select your birth date' },
        ageCheck: { max: 100, message: 'You surely are not 100 years old' }
    },
    phone: {
        required: { value: true, message: 'Phone number is required' },
        isPhone: { value: true, message: 'Format: +421 9xx xxx xxx' }
    },
    country: {
        required: { value: true, message: 'Country is a required field' }
    },
    city: {
        required: { value: true, message: 'City is a required field' }
    }
};

    const validateField = (input, form) => {
        const rules = validationRules[input.name];
        if (!rules) return true;

        const value = input.value.trim();
        const group = input.closest('.auth-form__group');
        const errorDiv = group?.querySelector('.invalid-input-field');

        const show = (msg) => {
            input.classList.add('input-error');
            if (errorDiv) {
                errorDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> <span>${msg}</span>`;
                errorDiv.classList.add('active');
            }
        };

        const hide = () => {
            input.classList.remove('input-error');
            if (errorDiv) {
                errorDiv.classList.remove('active');
                setTimeout(() => { 
                    if(!errorDiv.classList.contains('active')) errorDiv.innerHTML = ''; 
                }, 200);
            }
        };

        if (rules.required && !value) {
            show(rules.required.message);
            return false;
        }

        if (rules.ageCheck && value) {
            const birthDate = new Date(value);
            const today = new Date();
            
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            if (birthDate > today) {
                show("Birth date cannot be in the future");
                return false;
            }

            if (age > rules.ageCheck.max) {
                show(rules.ageCheck.message);
                return false;
            }
        }

        if (rules.isPhone && value) {
            if (!/^\+[0-9\s]{10,20}$/.test(value)) {
                show(rules.isPhone.message);
                return false;
            }
        }

        if (rules.isEmail && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                show(rules.isEmail.message);
                return false;
            }
        }

        if (rules.min && value.length < rules.min.value) {
            show(rules.min.message);
            return false;
        }

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
        const inputs = form.querySelectorAll('input:not([type="radio"]), select');

        inputs.forEach(input => {
            if (input.name === 'phone') {
                input.addEventListener('input', (e) => {
                    let value = input.value;

                    if (value.length === 1 && value !== '+') {
                        if (/\d/.test(value)) {
                            value = '+' + value;
                        } else {
                            input.value = '';
                            return;
                        }
                    }

                    const hasPlus = value.startsWith('+');
                    const digits = value.replace(/\D/g, '');

                    let formatted = hasPlus ? '+' : '';
                    
                    if (digits.length > 0) {
                        formatted += digits.substring(0, 3);
                        
                        if (digits.length > 3) {
                            const rest = digits.substring(3).match(/.{1,3}/g);
                            formatted += ' ' + rest.join(' ');
                        }
                    }

                    input.value = formatted.substring(0, 16);
                });
            }

            input.addEventListener('input', () => {
                if (input.classList.contains('input-error')) validateField(input, form);
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
                console.log(`🛑 Validácia zlyhala pre formulár: ${form.action}`);
                const firstError = form.querySelector('.active');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
}