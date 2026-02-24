export function initTitleComboboxes() {
    const config = {
        'titles_before_list': ['Bc.', 'BcA.', 'Ing.', 'Ing. arch.', 'MUDr.', 'MVDr.', 'Mgr.', 'MgA.', 'PhDr.', 'PaedDr.', 'RNDr.', 'JUDr.'],
        'titles_after_list': ['PhD.', 'MBA', 'LL.M.', 'DrSc.', 'CSc.']
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
            if (!rawVal) return;

            const cleanVal = rawVal.toLowerCase().replace(/\./g, '');

            const matched = titles.find(t => {
                const cleanTitle = t.toLowerCase().replace(/\./g, '');
                return cleanTitle === cleanVal;
            });

            if (matched) input.value = matched;
        });
    });
}