export default function initGenderSelection() {
    const radios = document.querySelectorAll('.gender-radio');
    const slider = document.getElementById('genderSlider');

    if (radios.length === 0) return;

    function moveSliderTo(genderValue) {
        if (!slider) return;
        const index = { male: 0, female: 1, other: 2, none: 3 }[genderValue] ?? 0;
        slider.style.transform = `translateX(${index * 100}%)`;
    }

    const initialChecked = document.querySelector('.gender-radio:checked');
    if (initialChecked) {
        moveSliderTo(initialChecked.value);
    }

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                moveSliderTo(radio.value);
            }
        });
    });
}