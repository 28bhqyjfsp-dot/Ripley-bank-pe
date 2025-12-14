FROM php:8.2-apache

# Habilitar módulos comunes de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar todos los archivos del proyecto
COPY . /var/www/html/

# Permisos
RUN chmod -R 755 /var/www/html/
