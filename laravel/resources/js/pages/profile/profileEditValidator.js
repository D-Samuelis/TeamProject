export function initProfileEditValidator() {
    const forms = document.querySelectorAll(".form-profile-info");
    if (forms.length === 0) return;

    const validationRules = {
        name: {
            required: { value: true, message: "Full name is required" },
        },
        email: {
            required: { value: true, message: "Email address is required" },
            isEmail: { value: true, message: "This email format is incorrect" },
        },
        birth_date: {
            required: { value: true, message: "Birth date is required" },
            ageCheck: {
                min: 18,
                max: 100,
                minMessage: "You must be at least 18 years old",
                maxMessage: "Age must be under 100 years",
            },
        },
        password: {
            min: {
                value: 8,
                message: "Password must be at least 8 characters",
            },
        },
        password_confirmation: {
            match: { value: "password", message: "Passwords do not match" },
        },
        phone_number: {
            required: { value: true, message: "Phone number is required" },
            isPhone: { value: true, message: "Use international format, e.g. +421901234567" },
        },
        city: {
            required: { value: true, message: "City is required" },
        },
        country: {
            required: { value: true, message: "Country is required" },
        },
        current_password: {
            required: {
                value: true,
                message:
                    "Please enter your current password to confirm changes",
            },
        },
    };

    const validateField = (input, form) => {
        const rules = validationRules[input.name];
        if (!rules) return true;

        const value = input.value.trim();
        const group = input.closest(".auth-form__group, .form__group, .modal-form__group");
        const errorDiv = group?.querySelector(
            ".invalid-input-field, .form-error, .modal-form__error",
        );

        const show = (msg) => {
            input.classList.add("input-error");
            if (errorDiv) {
                errorDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> <span>${msg}</span>`;
                errorDiv.classList.add("active");
            }
        };

        const hide = () => {
            input.classList.remove("input-error");
            if (errorDiv) {
                errorDiv.classList.remove("active");
                setTimeout(() => {
                    if (!errorDiv.classList.contains("active"))
                        errorDiv.innerHTML = "";
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

        if (rules.isPhone && value) {
            if (!/^\+[1-9]\d{7,14}$/.test(value)) {
                show(rules.isPhone.message);
                return false;
            }
        }

        if (rules.ageCheck && value) {
            const birthDate = new Date(value);
            const today = new Date();

            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (
                monthDiff < 0 ||
                (monthDiff === 0 && today.getDate() < birthDate.getDate())
            ) {
                age--;
            }

            if (birthDate > today) {
                show("Birth date cannot be in the future");
                return false;
            }

            if (age < rules.ageCheck.min) {
                show(rules.ageCheck.minMessage);
                return false;
            }

            if (age >= rules.ageCheck.max) {
                show(rules.ageCheck.maxMessage);
                return false;
            }
        }

        if (rules.min && value && value.length < rules.min.value) {
            show(rules.min.message);
            return false;
        }

        if (rules.match) {
            const target = form.querySelector(
                `input[name="${rules.match.value}"]`,
            );
            if (target && (value || target.value) && value !== target.value) {
                show(rules.match.message);
                return false;
            }
        }

        hide();
        return true;
    };

    forms.forEach((form) => {
        const inputs = form.querySelectorAll("input, select");

        inputs.forEach((input) => {
            input.addEventListener("input", () => {
                if (input.classList.contains("input-error"))
                    validateField(input, form);
            });

            input.addEventListener("blur", () => validateField(input, form));

            if (input.name === "phone_number") {
                input.addEventListener("input", () => {
                    let value = input.value;

                    if (value.length === 1 && value !== "+") {
                        if (/\d/.test(value)) value = "+" + value;
                        else {
                            input.value = "";
                            return;
                        }
                    }

                    const hasPlus = value.startsWith("+");
                    const digits = value.replace(/\D/g, "");

                    let formatted = hasPlus ? "+" : "";
                    formatted += digits.substring(0, 15);

                    input.value = formatted.substring(0, 16);
                });
            }
        });

        form.addEventListener("submit", (e) => {
            let isFormValid = true;

            inputs.forEach((input) => {
                if (!validateField(input, form)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                const firstError = form.querySelector(".active");
                if (firstError)
                    firstError.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
            }
        });
    });
}
