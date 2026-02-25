export function initTitleComboboxes() {
    const config = {
        'titles_before_list': ['Bc.', 'BcA.', 'Ing.', 'Ing. arch.', 'MUDr.', 'MVDr.', 'Mgr.', 'MgA.', 'PhDr.', 'PaedDr.', 'RNDr.', 'JUDr.'],
        'titles_after_list': ['PhD.', 'MBA', 'LL.M.', 'DrSc.', 'CSc.']
    };

    const messages = {
        standard: "Verification required! You will need to upload a scan of your diploma in your profile settings later.",
        unrecognized: "Warning! This title is not in our database yet. It will be sent for manual approval and you will need to upload your diploma in your profile."
    };

    Object.entries(config).forEach(([listId, titles]) => {
        const datalist = document.getElementById(listId);
        if (!datalist) return;

        datalist.innerHTML = ''; 
        const fragment = document.createDocumentFragment();
        titles.forEach(title => {
            const option = document.createElement('option');
            option.value = title;
            fragment.appendChild(option);
        });
        datalist.appendChild(fragment);

        const input = document.querySelector(`input[list="${listId}"]`);
        if (!input) return;

        input.addEventListener('blur', () => {
            const rawVal = input.value.trim();
            const group = input.closest('.auth-form__group');
            const infoBox = group?.querySelector('.auth-form__info');
            const tooltip = infoBox?.querySelector('.info-tooltip');
            
            if (!rawVal) {
                if (infoBox) infoBox.classList.remove('active');
                return;
            }

            const cleanVal = rawVal.toLowerCase().replace(/\./g, '');
            const matched = titles.find(t => t.toLowerCase().replace(/\./g, '') === cleanVal);

            if (infoBox && tooltip) {
                infoBox.classList.add('active');

                if (matched) {
                    input.value = matched;
                    tooltip.textContent = messages.standard;
                    infoBox.style.color = "var(--color-primary)";
                    infoBox.style.background = "color-mix(in srgb, var(--color-primary), transparent 90%)";
                } else {
                    tooltip.textContent = messages.unrecognized;
                    infoBox.style.color = "var(--color-warning-text)";
                    infoBox.style.background = "color-mix(in srgb, var(--color-warning), transparent 80%)";
                }
            }
            
            if (matched) input.dispatchEvent(new Event('input'));
        });
    });
}