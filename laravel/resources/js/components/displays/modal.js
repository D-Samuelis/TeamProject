export class Modal {
    static showCustom(options) {
        const { 
            title, 
            type,
            action = "create", // create | edit | delete | warning | info
            body, 
            confirmText = 'Save', 
            cancelText = 'Cancel', 
            onConfirm, 
            rules = {} 
        } = options;

        const actionConfig = {
            create:  { color: '#64a764', tag: 'New Record' },
            edit:    { color: '#7b58fb', tag: 'Modification' },
            delete:  { color: '#eb251e', tag: 'Warning' },
            warning: { color: '#e6a429', tag: 'Attention' },
            info:    { color: '#3b82f6', tag: 'Information' }
        };

        const config = actionConfig[action] || actionConfig.info;
        const activeColor = config.color;
        const displayTag = type || config.tag;
        const activeColorHover = `color-mix(in srgb, ${activeColor}, black 15%)`;

        const isInfo = action === 'info';
        
        const buttonsHtml = isInfo 
            ? `
                <button type="button" class="modal__nav-link modal-close-trigger" 
                        style="border: none; cursor: pointer; background-color: var(--modal-accent); color: white; border-radius: .15rem; padding: 0.6rem 2rem; transition: all 0.2s;"
                        onmouseenter="this.style.backgroundColor='var(--modal-accent-hover)';" 
                        onmouseleave="this.style.backgroundColor='var(--modal-accent)';">
                    ${confirmText === 'Save' ? 'Got it' : confirmText}
                </button>
            `
            : `
                <button type="button" class="modal__nav-link is-active btn-confirm" 
                        style="border: none; cursor: pointer; background-color: var(--modal-accent); color: white; padding: 0.6rem 1.4rem; border-radius: .15rem; transition: all 0.2s;"
                        onmouseenter="this.style.backgroundColor='var(--modal-accent-hover)';" 
                        onmouseleave="this.style.backgroundColor='var(--modal-accent)';">
                    ${confirmText}
                </button>
                <button type="button" class="modal__nav-link modal-close-trigger" 
                        style="border: none; cursor: pointer; background: var(--color-border-light-very); color: var(--color-text); border-radius: .15rem; padding: 0.6rem 1.4rem; transition: all 0.2s;"
                        onmouseenter="this.style.background='var(--color-border-light)'"
                        onmouseleave="this.style.background='var(--color-border-light-very)'">
                    ${cancelText}
                </button>
            `;

        const modalHtml = `
        <div class="modal" id="dynamic-modal" style="--modal-accent: ${activeColor}; --modal-accent-hover: ${activeColorHover}">
            <div class="modal__overlay modal-close-trigger"></div>
            <div class="modal__content">
                <div class="modal__header">
                    <div class="modal-header__title-group">
                        <h2 class="modal-header__title">${title}</h2>
                        <div class="modal-type-badge" style="border-left: 3px solid var(--modal-accent); background-color: color-mix(in srgb, var(--modal-accent), transparent 85%); padding: 0.2rem 0.6rem; border-radius: 0 4px 4px 0;">
                            <h3 class="modal-header__subtitle" style="color: var(--modal-accent); margin: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                ${displayTag}
                            </h3>
                        </div>
                    </div>
                    <button class="modal-close-trigger" 
                        style="border: none; background: transparent; font-size: 1.25rem; cursor: pointer; color: var(--color-text-light); transition: all 0.2s; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 4px;" 
                        onmouseenter="this.style.color='#ff4221'; this.style.backgroundColor='rgba(255, 66, 33, 0.1)';" 
                        onmouseleave="this.style.color='var(--color-text-light)'; this.style.backgroundColor='transparent'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal__body">
                    ${body}
                </div>
                <div class="modal__buttons">
                    ${buttonsHtml}
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById('dynamic-modal');

        const close = () => modal.remove();
        
        // Pripojíme zatváranie na všetky elementy s touto triedou (overlay, krížik, cancel button)
        modal.querySelectorAll('.modal-close-trigger').forEach(el => {
            el.onclick = close;
        });

        // OPRAVENÉ: Naviazanie na confirm button len ak existuje (nie je v info móde)
        const btnConfirm = modal.querySelector('.btn-confirm');
        if (btnConfirm) {
            btnConfirm.onclick = () => {
                const form = modal.querySelector('form');
                if (form) {
                    const event = new Event('submit', { cancelable: true, bubbles: true });
                    form.dispatchEvent(event);
                    if (event.defaultPrevented) return;
                }

                if (onConfirm) onConfirm(modal);
            };
        }

        this._attachValidation(modal, rules);
    }

    static showFieldErrors(modal, errors) {
        const form = modal.querySelector('form');
        if (!form) return;

        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            const group    = input.closest('.modal-form__group');
            const errorDiv = group?.querySelector('.invalid-input-field');

            input.classList.add('input-error');

            if (group) {
                group.classList.remove('shake-it');
                void group.offsetWidth;
                group.classList.add('shake-it');
            }

            if (errorDiv) {
                errorDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> <span>${messages[0]}</span>`;
                errorDiv.classList.add('active');
            }
        });
    }

    static clearFieldErrors(modal) {
        const form = modal.querySelector('form');
        if (!form) return;

        form.querySelectorAll('input, textarea, select').forEach(input => {
            this._clearField(input);
        });
    }

    static _attachValidation(modal, customRules = {}) {
        const form = modal.querySelector('form');
        if (!form) return;

        const rules = { ...customRules };
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
                this._clearField(input);
                if (rules[input.name]) this._validateField(input, rules, form);
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

        const show = (msg) => {
            const group    = input.closest('.modal-form__group');
            const errorDiv = group?.querySelector('.invalid-input-field');

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

        if (rules.required && !value) {
            show(rules.required.message);
            return false;
        }

        this._clearField(input);
        return true;
    }

    static _clearField(input) {
        const group    = input.closest('.modal-form__group');
        const errorDiv = group?.querySelector('.invalid-input-field');

        input.classList.remove('input-error');
        if (group) group.classList.remove('shake-it');

        if (errorDiv) {
            errorDiv.classList.remove('active');
            setTimeout(() => {
                if (!errorDiv.classList.contains('active')) errorDiv.innerHTML = '';
            }, 200);
        }
    }

    static close(modal) {
        modal?.remove();
    }
}