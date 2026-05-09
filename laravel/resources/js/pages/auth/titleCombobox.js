export function initTitleComboboxes() {
    const config = {
        'titles_before_list': ['Bc.', 'BcA.', 'Ing.', 'Ing. arch.', 'MUDr.', 'MVDr.', 'Mgr.', 'MgA.', 'PhDr.', 'PaedDr.', 'RNDr.', 'JUDr.'],
        'titles_after_list': ['PhD.', 'MBA', 'LL.M.', 'DrSc.', 'CSc.']
    };

    const unrecognizedMessage = "This title is not in our database yet. It may be reviewed manually and you may be asked to provide proof of your diploma later.";

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

            if (matched) {
                input.value = matched;
                infoBox?.classList.remove('active');
                input.dispatchEvent(new Event('input'));
                return;
            }

            if (infoBox && tooltip) {
                infoBox.classList.add('active');
                tooltip.textContent = unrecognizedMessage;
                infoBox.style.color = "var(--color-warning-text)";
                infoBox.style.background = "var(--color-bg-complement-v2)";
            }
        });
    });
}