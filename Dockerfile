# Используем официальный образ PHP с Apache
FROM php:8.2-apache

# Устанавливаем необходимые пакеты и расширения
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    libcurl4-openssl-dev \ 
    # Добавляем зависимость для cURL
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql curl \ 
    # Устанавливаем cURL
    && docker-php-ext-enable curl # Активируем cURL

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта в контейнер
COPY . .

# Устанавливаем права на папки
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Устанавливаем зависимости Laravel
RUN composer install

# Определяем переменные окружения для подключения к базе данных
ENV DB_HOST=db
ENV DB_USER=root
ENV DB_PASSWORD=wT8gn!RpC2p/z.M5
ENV DB_NAME=where_parts_db

# Устанавливаем права на папки
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Конфигурируем Apache для обслуживания сайта
RUN a2enmod rewrite

# Пробрасываем порт 80
EXPOSE 80

# Запускаем Apache сервер
CMD ["apachectl", "-D", "FOREGROUND"]