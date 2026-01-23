import { Application } from '@hotwired/stimulus';
import './stimulus_bootstrap.js';
import './utils/formValidator';
import './utils/passwordStrength';
import './utils/phoneMask';
import './utils/formSubmit';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/app.scss';

// Контроллеры
import ImageUploadController from "./controllers/image_upload_controller";
import AdminNavigationController from "./controllers/admin/navigation_controller";

// Инициализация Stimulus
const app = Application.start();

// Регистрация контроллеров
app.register('image-upload', ImageUploadController);
app.register('admin-navigation', AdminNavigationController); // Изменили имя с 'navigation'

// Экспортируем для отладки
window.Stimulus = app;

console.log('✅ Stimulus Application запущена');
console.log('Зарегистрированные контроллеры:',
    Array.from(app.router.modulesByIdentifier.keys())
);
