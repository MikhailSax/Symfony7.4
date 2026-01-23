import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['usersCollapse', 'catalogCollapse'];

    connect() {
        console.log('Admin Navigation подключен');

        // Восстанавливаем состояние аккордеона из localStorage
        this.restoreCollapseState();

        // Сохраняем состояние при изменении
        this.setupCollapseListeners();
    }

    restoreCollapseState() {
        // Пользователи
        const usersState = localStorage.getItem('admin-nav-users-collapse');
        if (usersState === 'false') {
            this.collapseElement('usersCollapse');
        }

        // Каталог
        const catalogState = localStorage.getItem('admin-nav-catalog-collapse');
        if (catalogState === 'false') {
            this.collapseElement('catalogCollapse');
        }
    }

    setupCollapseListeners() {
        // Слушаем события collapse Bootstrap
        document.querySelectorAll('.collapse').forEach(collapse => {
            collapse.addEventListener('show.bs.collapse', (e) => {
                this.handleCollapseShow(e);
            });

            collapse.addEventListener('hide.bs.collapse', (e) => {
                this.handleCollapseHide(e);
            });
        });
    }

    handleCollapseShow(event) {
        const collapseId = event.target.id;
        this.saveCollapseState(collapseId, true);

        // Обновляем стрелку
        const button = document.querySelector(`[data-bs-target="#${collapseId}"]`);
        if (button) {
            const arrow = button.querySelector('.transition-rotate');
            if (arrow) {
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    }

    handleCollapseHide(event) {
        const collapseId = event.target.id;
        this.saveCollapseState(collapseId, false);

        // Обновляем стрелку
        const button = document.querySelector(`[data-bs-target="#${collapseId}"]`);
        if (button) {
            const arrow = button.querySelector('.transition-rotate');
            if (arrow) {
                arrow.style.transform = 'rotate(0)';
            }
        }
    }

    saveCollapseState(collapseId, isOpen) {
        if (collapseId === 'usersCollapse') {
            localStorage.setItem('admin-nav-users-collapse', isOpen);
        } else if (collapseId === 'catalogCollapse') {
            localStorage.setItem('admin-nav-catalog-collapse', isOpen);
        }
    }

    collapseElement(targetName) {
        if (this.hasTarget(targetName)) {
            const element = this[`${targetName}Target`];
            const bsCollapse = new bootstrap.Collapse(element, {
                toggle: false
            });
            bsCollapse.hide();
        }
    }
}
