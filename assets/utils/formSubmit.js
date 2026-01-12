// assets/js/utils/formSubmit.js

export default class FormSubmit {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.init();
    }

    init() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                return false;
            }

            this.showLoading();
        });
    }

    validateForm() {
        const email = this.form.querySelector('[data-validate="email"]');
        const phone = this.form.querySelector('[data-validate="phone"]');
        const name = this.form.querySelector('[data-validate="name"]');
        const password = this.form.querySelector('[data-validate="password"]');
        const terms = this.form.querySelector('[data-validate="terms"]');

        let isValid = true;

        if (email && !this.validateEmail(email.value)) {
            this.showError(email, 'Введите корректный email адрес');
            isValid = false;
        }

        if (phone && !this.validatePhone(phone.value)) {
            this.showError(phone, 'Введите корректный номер телефона');
            isValid = false;
        }

        if (name && !this.validateName(name.value)) {
            this.showError(name, 'Имя должно содержать только буквы');
            isValid = false;
        }

        if (password && !this.validatePassword(password.value)) {
            this.showError(password, 'Пароль не соответствует требованиям');
            isValid = false;
        }

        if (terms && !terms.checked) {
            this.showError(terms, 'Необходимо согласиться с условиями');
            isValid = false;
        }

        return isValid;
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validatePhone(phone) {
        const cleanPhone = phone.replace(/\D/g, '');
        return cleanPhone.length >= 10;
    }

    validateName(name) {
        const re = /^[a-zA-Zа-яА-ЯёЁ\s]{2,50}$/;
        return re.test(name);
    }

    validatePassword(password) {
        return password.length >= 6 &&
            /[A-Z]/.test(password) &&
            /[0-9]/.test(password);
    }

    showError(element, message) {
        element.classList.add('is-invalid');

        const errorId = `${element.name || element.id}-error`;
        const errorElement = document.getElementById(errorId);

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }

        // Фокус на поле с ошибкой
        element.focus();
    }

    showLoading() {
        const submitBtn = this.form.querySelector('#submitBtn');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Регистрация...';
            submitBtn.disabled = true;

            // Восстановление кнопки через 5 секунд (на случай ошибки)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        }
    }
}

// Инициализация отправки формы
document.addEventListener('DOMContentLoaded', function() {
    const formSubmit = new FormSubmit('registrationForm');
});
