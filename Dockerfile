FROM php:8.3-apache

# Instalar dependências e extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql mysqli zip

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite headers

# Configurar o PHP
RUN echo "display_errors = On" >> "$PHP_INI_DIR/conf.d/error-logging.ini" && \
    echo "error_log = /dev/stderr" >> "$PHP_INI_DIR/conf.d/error-logging.ini" && \
    echo "max_execution_time = 30" >> "$PHP_INI_DIR/conf.d/error-logging.ini" && \
    echo "default_socket_timeout = 60" >> "$PHP_INI_DIR/conf.d/error-logging.ini"

# Configurar o Apache
RUN echo '\
<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    ErrorLog /dev/stderr\n\
    CustomLog /dev/stdout combined\n\
    <Directory /var/www/html/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Configurar variáveis de ambiente para o PHP
ENV DB_HOST=${DB_HOST} \
    DB_USER=${DB_USER} \
    DB_PASSWORD=${DB_PASSWORD} \
    DB_NAME=${DB_NAME} \
    DB_PORT=${DB_PORT} \
    RENDER=${RENDER} \
    DISPLAY_ERRORS=${DISPLAY_ERRORS} \
    ERROR_REPORTING=${ERROR_REPORTING}

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expor a porta 80
EXPOSE 80
