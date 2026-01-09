# Usamos una imagen oficial de PHP con FPM (rápida y ligera)
FROM php:8.2-fpm

# Instalamos dependencias del sistema y drivers necesarios
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libsqlite3-dev \
    sqlite3

# Limpiamos caché para reducir tamaño de imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# ¡CLAVE! Instalamos el driver de SQLite para los tests unitarios
# (Esto soluciona tu error "could not find driver")
RUN docker-php-ext-install pdo_sqlite

# Instalamos Composer (El navegante)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuramos el directorio de trabajo
WORKDIR /var/www

# Copiamos todo el proyecto al contenedor
COPY . /var/www

# Damos permisos a la carpeta de storage (para que Laravel pueda escribir logs y caché)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache