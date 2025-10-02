FROM php:8.2-cli

# Установка необходимых пакетов
RUN apt-get update \
    && apt-get install -y git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Копируем composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем только composer файлы и ставим зависимости
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts

# Копируем остальной код проекта
COPY . .

RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/api-docs \
    && chmod -R 755 storage

EXPOSE 85

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=85"]
