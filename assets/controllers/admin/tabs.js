// assets/js/admin-tabs.js

document.addEventListener('DOMContentLoaded', function() {
    // Сохраняем активную вкладку при переключении
    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');

    tabLinks.forEach(tab => {
        tab.addEventListener('click', function() {
            localStorage.setItem('activeProductTab', this.id);
        });
    });

    // Восстанавливаем активную вкладку при загрузке
    const activeTab = localStorage.getItem('activeProductTab');
    if (activeTab) {
        const tabElement = document.getElementById(activeTab);
        if (tabElement && !tabElement.classList.contains('active')) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }

    // Добавляем индикатор заполненных вкладок
    function updateTabIndicators() {
        tabLinks.forEach(tab => {
            const targetId = tab.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);

            if (targetPane) {
                // Проверяем, есть ли заполненные поля в этой вкладке
                const filledFields = targetPane.querySelectorAll('.form-control[value!=""], .form-control:not([value])');

                if (filledFields.length > 0) {
                    // Добавляем индикатор
                    if (!tab.querySelector('.tab-indicator')) {
                        const indicator = document.createElement('span');
                        indicator.className = 'tab-indicator badge bg-success ms-1';
                        indicator.style.fontSize = '0.6rem';
                        indicator.textContent = '✓';
                        tab.appendChild(indicator);
                    }
                } else {
                    // Убираем индикатор
                    const indicator = tab.querySelector('.tab-indicator');
                    if (indicator) {
                        indicator.remove();
                    }
                }
            }
        });
    }

    // Слушаем изменения в полях формы
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('change', updateTabIndicators);
        input.addEventListener('input', updateTabIndicators);
    });

    // Первоначальное обновление
    updateTabIndicators();
});
