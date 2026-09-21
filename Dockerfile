FROM php:8.2-apache

# Instalación de dependencias y extensiones de PHP necesarias (pdo_mysql, gd)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Habilitar mod_rewrite en Apache
RUN a2enmod rewrite

# Configurar Apache DocumentRoot hacia /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar AllowOverride All para soporte de .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copiar el código fuente
COPY . /var/www/html/

# Asegurar directorios de almacenamiento y permisos de subida
RUN mkdir -p /var/www/html/public/img/productos \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/public/img

EXPOSE 80
