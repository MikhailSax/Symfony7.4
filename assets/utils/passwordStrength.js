// assets/js/utils/passwordStrength.js

export default class PasswordStrength {
    constructor(passwordInputId, strengthBarId) {
        this.passwordInput = document.getElementById(passwordInputId);
        this.strengthBar = document.getElementById(strengthBarId);
        this.requirements = {
            length: document.getElementById('reqLength'),
            uppercase: document.getElementById('reqUppercase'),
            number: document.getElementById('reqNumber')
        };

        this.init();
    }

    init() {
        if (!this.passwordInput || !this.strengthBar) return;

        this.passwordInput.addEventListener('input', (e) => {
            this.updateStrength(e.target.value);
        });
    }

    updateStrength(password) {
        let strength = 0;

        // Проверка длины
        if (password.length >= 6) {
            strength += 33;
            this.updateRequirementUI('length', true);
        } else {
            this.updateRequirementUI('length', false);
        }

        // Проверка заглавной буквы
        if (/[A-Z]/.test(password)) {
            strength += 33;
            this.updateRequirementUI('uppercase', true);
        } else {
            this.updateRequirementUI('uppercase', false);
        }

        // Проверка цифры
        if (/[0-9]/.test(password)) {
            strength += 34;
            this.updateRequirementUI('number', true);
        } else {
            this.updateRequirementUI('number', false);
        }

        // Обновление индикатора
        this.strengthBar.style.width = strength + '%';
        this.strengthBar.className = 'strength-bar';

        if (strength < 33) {
            this.strengthBar.classList.add('strength-weak');
        } else if (strength < 66) {
            this.strengthBar.classList.add('strength-medium');
        } else {
            this.strengthBar.classList.add('strength-strong');
        }
    }

    updateRequirementUI(requirementId, met) {
        const requirement = this.requirements[requirementId];
        if (!requirement) return;

        if (met) {
            requirement.classList.add('met');
            requirement.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>' + requirement.textContent.replace('✓ ', '') + '</span>';
        } else {
            requirement.classList.remove('met');
            requirement.innerHTML = '<i class="bi bi-circle"></i><span>' + requirement.textContent.replace('✓ ', '') + '</span>';
        }
    }
}

// Инициализация индикатора сложности пароля
document.addEventListener('DOMContentLoaded', function() {
    const passwordStrength = new PasswordStrength('passwordInput', 'passwordStrength');
});
