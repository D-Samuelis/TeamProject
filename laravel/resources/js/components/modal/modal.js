export class Modal {
    static showCustom(options) {
        const { title, body, confirmText = 'Save', cancelText = 'Cancel', onConfirm, rules = {} } = options;

        const modalHtml = `
            <div class="modal" id="dynamic-modal">
                <div class="modal__overlay modal-close-trigger"></div>
                <div class="modal__content">
                    <div class="modal__header">
                        <h2 class="modal-header__title">${title}</h2>
                        <button class="modal-close-trigger" style="border: none; background: transparent; font-size: 1.25rem; cursor: pointer;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal__body">
                        ${body}
                    </div>
                    <div class="modal__buttons">
                        <button type="button" class="modal__nav-link is-active btn-confirm" style="border: none; cursor: pointer;">
                            ${confirmText}
                        </button>
                        <button type="button" class="modal__nav-link modal-close-trigger" style="border: none; cursor: pointer;">
                            ${cancelText}
                        </button>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById('dynamic-modal');

        const close = () => modal.remove();
        modal.querySelectorAll('.modal-close-trigger').forEach(el => el.onclick = close);

        modal.querySelector('.btn-confirm').onclick = () => {
            const form = modal.querySelector('form');
            if (form) {
                const event = new Event('submit', { cancelable: true, bubbles: true });
                form.dispatchEvent(event);
                
                if (event.defaultPrevented) return;
            }

            if (onConfirm) onConfirm(modal);
        };

        this._attachValidation(modal, rules);
    }

    static _attachValidation(modal, customRules = {}) {
        const form = modal.querySelector('form');
        if (!form) return;

        const rules = {
            ...customRules
        };

        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            if (!input.closest('.modal-form__group')?.querySelector('.modal-form__error')) {
                const group = input.closest('.modal-form__group');
                if (group) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'modal-form__error invalid-input-field';
                    group.appendChild(errorDiv);
                }
            }

            input.addEventListener('blur', () => this._validateField(input, rules, form));
            input.addEventListener('input', () => {
                if (input.classList.contains('input-error')) this._validateField(input, rules, form);
            });
        });

        form.addEventListener('submit', (e) => {
            let isValid = true;
            inputs.forEach(input => {
                if (!this._validateField(input, rules, form)) isValid = false;
            });
            if (!isValid) e.preventDefault();
        });
    }

    static _validateField(input, rulesList, form) {
        const rules = rulesList[input.name];
        if (!rules) return true;

        const value = input.value.trim();
        const group = input.closest('.modal-form__group');
        const errorDiv = group?.querySelector('.invalid-input-field');

        const show = (msg) => {
            if (group) {
                group.classList.remove('shake-it');
                void group.offsetWidth;
                group.classList.add('shake-it');
            }

            input.classList.add('input-error');
            if (errorDiv) {
                errorDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> <span>${msg}</span>`;
                errorDiv.classList.add('active');
            }
        };

        const hide = () => {
            input.classList.remove('input-error');
            if (group) group.classList.remove('shake-it');
            if (errorDiv) {
                errorDiv.classList.remove('active');
                setTimeout(() => {
                    if (!errorDiv.classList.contains('active')) errorDiv.innerHTML = '';
                }, 200);
            }
        };

        if (rules.required && !value) {
            show(rules.required.message);
            return false;
        }

        hide();
        return true;
    }
}