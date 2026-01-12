// assets/js/utils/phoneMask.js

export default class PhoneMask {
    constructor(phoneInputSelector) {
        this.phoneInput = document.querySelector(phoneInputSelector);
        this.init();
    }

    init() {
        if (!this.phoneInput) return;

        this.phoneInput.addEventListener('input', (e) => {
            this.applyMask(e.target);
        });

        this.phoneInput.addEventListener('keydown', (e) => {
            // Разрешаем: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Разрешаем: Ctrl+A, Ctrl+C, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Разрешаем: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }

            // Запрещаем не цифровые клавиши
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }

    applyMask(input) {
        let value = input.value.replace(/\D/g, '');

        if (value.length > 0) {
            // Убираем код страны если он 7 или 8
            if (value[0] === '7' || value[0] === '8') {
                value = value.substring(1);
            }

            let formatted = '+7 ';

            if (value.length > 0) {
                formatted += '(' + value.substring(0, 3);
            }
            if (value.length > 3) {
                formatted += ') ' + value.substring(3, 6);
            }
            if (value.length > 6) {
                formatted += '-' + value.substring(6, 8);
            }
            if (value.length > 8) {
                formatted += '-' + value.substring(8, 10);
            }

            input.value = formatted;
        }
    }
}

// Инициализация маски телефона
document.addEventListener('DOMContentLoaded', function() {
    const phoneMask = new PhoneMask('[data-validate="phone"]');
});
