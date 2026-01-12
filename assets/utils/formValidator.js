// assets/js/utils/formValidator.js

export default class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.fields = {};
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;

        // Находим все поля с data-validate
        const validateFields = this.form.querySelectorAll('[data-validate]');

        validateFields.forEach(field => {
            const fieldName = field.name || field.id;
            const validateType = field.getAttribute('data-validate');

            this.fields[fieldName] = {
                element: field,
                type: validateType,
                isValid: false,
                errorElement: document.getElementById(`${fieldName.replace(/[\[\]]/g, '-')}-error`) ||
                    document.getElementById(`${fieldName}-error`)
            };

            // Добавляем обработчики событий
            field.addEventListener('blur', () => this.validateField(fieldName));
            field.addEventListener('input', () => this.validateField(fieldName));
        });

        // Обработка чекбокса согласия
        const termsCheckbox = this.form.querySelector('[data-validate="terms"]');
        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', () => {
                this.validateField(termsCheckbox.name || 'agreeTerms');
                this.updateSubmitButton();
            });
        }

        // Валидация при отправке формы
        this.form.addEventListener('submit', (e) => {
            if (!this.validateAll()) {
                e.preventDefault();
                this.showAllErrors();
            }
        });
    }

    validateField(fieldName) {
        const field = this.fields[fieldName];
        if (!field) return false;

        const value = field.element.type === 'checkbox' ?
            field.element.checked :
            field.element.value.trim();

        let isValid = false;
        let errorMessage = '';

        switch (field.type) {
            case 'email':
                isValid = this.validateEmail(value);
                errorMessage = !isValid ? 'Введите корректный email адрес' : '';
                break;

            case 'phone':
                isValid = this.validatePhone(value);
                errorMessage = !isValid ? 'Введите корректный номер телефона' : '';
                break;

            case 'name':
                isValid = this.validateName(value);
                errorMessage = !isValid ? 'Имя должно содержать только буквы и быть не короче 2 символов' : '';
                break;

            case 'password':
                isValid = this.validatePassword(value);
                errorMessage = !isValid ? 'Пароль не соответствует требованиям' : '';
                break;

            case 'terms':
                isValid = value === true;
                errorMessage = !isValid ? 'Необходимо согласиться с условиями' : '';
                break;

            default:
                isValid = value.length > 0;
                errorMessage = !isValid ? 'Это поле обязательно для заполнения' : '';
        }

        field.isValid = isValid;
        this.updateFieldUI(field, isValid, errorMessage);
        this.updateSubmitButton();

        return isValid;
    }

    validateAll() {
        let allValid = true;

        Object.keys(this.fields).forEach(fieldName => {
            const isValid = this.validateField(fieldName);
            if (!isValid) allValid = false;
        });

        return allValid;
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

    updateFieldUI(field, isValid, errorMessage) {
        if (isValid) {
            field.element.classList.remove('is-invalid');
            field.element.classList.add('is-valid');
        } else {
            field.element.classList.remove('is-valid');
            field.element.classList.add('is-invalid');
        }

        if (field.errorElement) {
            if (!isValid && errorMessage) {
                field.errorElement.textContent = errorMessage;
                field.errorElement.classList.add('show');
            } else {
                field.errorElement.classList.remove('show');
            }
        }
    }

    showAllErrors() {
        Object.keys(this.fields).forEach(fieldName => {
            if (!this.fields[fieldName].isValid) {
                this.validateField(fieldName);
            }
        });
    }

    updateSubmitButton() {
        const submitBtn = this.form.querySelector('#submitBtn');
        if (!submitBtn) return;

        const allValid = Object.values(this.fields).every(field => field.isValid);
        submitBtn.disabled = !allValid;
    }
}

// Инициализация валидатора
document.addEventListener('DOMContentLoaded', function() {
    const validator = new FormValidator('registrationForm');
});
