# backend/Dockerfile
FROM php:8.3-cli

WORKDIR /var/www/html

# Instala dependencias del sistema y extensiones PHP necesarias para Symfony + MariaDB
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        intl \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia los archivos de dependencias primero (mejor cache de Docker)
COPY composer.json composer.lock ./

# Instala dependencias de PHP (sin scripts para evitar errores si falta .env)
RUN composer install --no-scripts --no-autoloader

# Copia el resto del código
COPY . .

# Genera el autoloader optimizado
RUN composer dump-autoload --optimize

# Expone el puerto del servidor de desarrollo de Symfony
EXPOSE 8000

# Arranca el servidor de desarrollo de Symfony
# 0.0.0.0 es imprescindible para que sea accesible desde fuera del contenedor
CMD ["sh", "-c", "mkdir -p config/jwt && php bin/console lexik:jwt:generate-keypair --skip-if-exists && php -S 0.0.0.0:8000 -t public"]
