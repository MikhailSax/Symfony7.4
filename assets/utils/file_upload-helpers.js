// Показать лоадер
export function showLoader(loaderElement) {
    if (loaderElement) {
        loaderElement.classList.add('active');
    }
}

// Скрыть лоадер
export function hideLoader(loaderElement) {
    if (loaderElement) {
        loaderElement.classList.remove('active');
    }
}

// Форматирование размера файла
export function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Проверка типа изображения
export function isValidImageType(file, allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']) {
    return allowedTypes.includes(file.type);
}

// Создание URL для предпросмотра
export function createObjectURL(file) {
    return URL.createObjectURL(file);
}

// Освобождение URL
export function revokeObjectURL(url) {
    if (url) {
        URL.revokeObjectURL(url);
    }
}
