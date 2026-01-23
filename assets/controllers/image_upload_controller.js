import { Controller } from '@hotwired/stimulus';
import { showLoader, hideLoader, formatFileSize, isValidImageType } from '../utils/file_upload-helpers'

export default class extends Controller {
    static targets = ['area', 'input', 'icon', 'loader', 'info', 'preview', 'errors'];

    // Конфигурация
    static values = {
        maxSize: { type: Number, default: 5 * 1024 * 1024 }, // 5MB в байтах
        allowedTypes: { type: Array, default: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'] },
        previewMaxWidth: { type: Number, default: 300 },
        uploadUrl: { type: String, default: '' } // URL для AJAX загрузки, если нужно
    }

    connect() {
        console.log('Image Upload Controller connected');
        this.setupEventListeners();
    }

    disconnect() {
        this.removeEventListeners();
    }

    setupEventListeners() {
        // Добавляем обработчики для области перетаскивания
        this.areaTarget.addEventListener('dragover', this.handleDragOver.bind(this));
        this.areaTarget.addEventListener('dragleave', this.handleDragLeave.bind(this));
        this.areaTarget.addEventListener('drop', this.handleDrop.bind(this));

        // Обработчик изменения input
        this.inputTarget.addEventListener('change', this.handleFileSelect.bind(this));
    }

    removeEventListeners() {
        this.areaTarget.removeEventListener('dragover', this.handleDragOver);
        this.areaTarget.removeEventListener('dragleave', this.handleDragLeave);
        this.areaTarget.removeEventListener('drop', this.handleDrop);
        this.inputTarget.removeEventListener('change', this.handleFileSelect);
    }

    // Stimulus actions
    handleClick(event) {
        event.preventDefault();
        console.log(event)
        this.inputTarget.click();
    }

    handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        this.areaTarget.classList.add('dragover');
    }

    handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        this.areaTarget.classList.remove('dragover');
    }

    handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        this.areaTarget.classList.remove('dragover');

        const files = event.dataTransfer.files;
        if (files.length > 0) {
            this.processFile(files[0]);
        }
    }

    // Обработка выбора файла через input
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.processFile(file);
        }
    }

    // Основной метод обработки файла
    async processFile(file) {
        // Валидация
        const validation = this.validateFile(file);
        if (!validation.valid) {
            this.showError(validation.message);
            return;
        }

        // Показываем лоадер
        showLoader(this.loaderTarget);
        this.areaTarget.classList.add('loading');

        try {
            // Создаем предпросмотр
            await this.createPreview(file);

            // Показываем информацию о файле
            this.showFileInfo(file);

            // Если есть uploadUrl, загружаем на сервер
            if (this.uploadUrlValue) {
                await this.uploadFile(file);
            }

            // Убираем лоадер
            hideLoader(this.loaderTarget);
            this.areaTarget.classList.remove('loading');
            this.areaTarget.classList.add('has-file');

        } catch (error) {
            hideLoader(this.loaderTarget);
            this.areaTarget.classList.remove('loading');
            this.showError(`Ошибка обработки файла: ${error.message}`);
        }
    }

    // Валидация файла
    validateFile(file) {
        // Проверка типа файла
        if (!this.allowedTypesValue.includes(file.type)) {
            return {
                valid: false,
                message: 'Недопустимый формат файла. Разрешены: JPEG, PNG, GIF, WebP'
            };
        }

        // Проверка размера
        if (file.size > this.maxSizeValue) {
            const maxSizeMB = this.maxSizeValue / 1024 / 1024;
            return {
                valid: false,
                message: `Файл слишком большой. Максимальный размер: ${maxSizeMB}MB`
            };
        }

        return { valid: true };
    }

    // Создание предпросмотра
    createPreview(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    // Очищаем предыдущий предпросмотр
                    this.clearPreview();

                    // Создаем контейнер для изображения
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'preview-image';

                    // Создаем само изображение
                    const previewImg = document.createElement('img');
                    previewImg.src = event.target.result;
                    previewImg.alt = 'Предпросмотр загруженного изображения';

                    // Масштабируем если нужно
                    if (img.width > this.previewMaxWidthValue) {
                        previewImg.style.width = `${this.previewMaxWidthValue}px`;
                    }

                    previewContainer.appendChild(previewImg);
                    this.previewTarget.appendChild(previewContainer);

                    resolve();
                };

                img.onerror = () => {
                    reject(new Error('Ошибка загрузки изображения для предпросмотра'));
                };

                img.src = event.target.result;
            };

            reader.onerror = () => {
                reject(new Error('Ошибка чтения файла'));
            };

            reader.readAsDataURL(file);
        });
    }

    // Показать информацию о файле
    showFileInfo(file) {
        const fileInfoHTML = `
            <div class="file-info">
                <i class="bi bi-check-circle-fill"></i>
                <div class="file-details">
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">(${formatFileSize(file.size)})</span>
                </div>
                <button type="button" class="remove-btn" data-action="click->image-upload#removeFile">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        `;

        this.infoTarget.innerHTML = fileInfoHTML;
        this.clearErrors();
    }

    // Загрузка файла на сервер (если нужно)
    async uploadFile(file) {
        if (!this.uploadUrlValue) return;

        const formData = new FormData();
        formData.append('image', file);

        const response = await fetch(this.uploadUrlValue, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Ошибка загрузки файла на сервер');
        }

        const result = await response.json();
        return result;
    }

    // Удалить файл
    removeFile() {
        this.inputTarget.value = '';
        this.clearFileInfo();
        this.clearPreview();
        this.clearErrors();
        this.areaTarget.classList.remove('has-file');
    }

    // Показать ошибку
    showError(message) {
        this.errorsTarget.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // Очистить ошибки
    clearErrors() {
        this.errorsTarget.innerHTML = '';
    }

    // Очистить информацию о файле
    clearFileInfo() {
        this.infoTarget.innerHTML = '';
    }

    // Очистить предпросмотр
    clearPreview() {
        this.previewTarget.innerHTML = '';
    }
}
