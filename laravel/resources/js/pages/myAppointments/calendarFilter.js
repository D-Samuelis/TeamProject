export function initCalendarFilters() {
    const monthSelect = document.getElementById('calendarMonth');
    const yearSelect = document.getElementById('calendarYear');

    if (!monthSelect || !yearSelect) return;

    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth();

    const monthFragment = document.createDocumentFragment();
    months.forEach((month, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = month;
        if (index === currentMonth) option.selected = true;
        monthFragment.appendChild(option);
    });
    monthSelect.appendChild(monthFragment);

    const yearFragment = document.createDocumentFragment();
    for (let i = 0; i <= 5; i++) {
        const year = currentYear + i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearFragment.appendChild(option);
    }
    yearSelect.appendChild(yearFragment);

    [monthSelect, yearSelect].forEach(select => {
        select.addEventListener('change', () => {
            console.log(`swap to: ${monthSelect.value} / ${yearSelect.value}`);
        });
    });
}