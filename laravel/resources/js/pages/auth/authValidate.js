export default function initAuthValidator() {
    const forms = document.querySelectorAll('.auth-form');
    
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
        required: { value: true, message: 'Please select your birth date' }
    },
    phone: {
        required: { value: true, message: 'Phone number is a required field' }
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