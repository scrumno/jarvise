#!/bin/bash

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Путь к директории проекта на сервере
PROJECT_PATH="/var/www/jarvis"
BRANCH="main"

echo -e "${YELLOW}Начинаем деплой проекта...${NC}"

# Переходим в директорию проекта
cd $PROJECT_PATH || {
    echo -e "${RED}Ошибка: Директория $PROJECT_PATH не существует${NC}"
    exit 1
}

# Получаем последние изменения
echo -e "${YELLOW}Получаем изменения из репозитория...${NC}"
git fetch origin
git checkout $BRANCH
git pull origin $BRANCH

# Проверяем успешность пулла
if [ $? -ne 0 ]; then
    echo -e "${RED}Ошибка при получении изменений из Git${NC}"
    exit 1
fi

echo -e "${GREEN}Изменения успешно получены${NC}"

# Устанавливаем зависимости через Composer
echo -e "${YELLOW}Устанавливаем Composer зависимости...${NC}"
composer install --no-dev --optimize-autoloader

# Проверяем успешность установки Composer
if [ $? -ne 0 ]; then
    echo -e "${RED}Ошибка при установке Composer зависимостей${NC}"
    exit 1
fi

echo -e "${GREEN}Composer зависимости установлены${NC}"

# Настройка прав (опционально)
echo -e "${YELLOW}Настраиваем права на файлы...${NC}"
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo -e "${GREEN}✓ Деплой успешно завершен!${NC}"
