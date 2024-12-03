FROM php:8.3-apache

# Instalar dependências e extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite headers

# Criar diretório para logs do PHP
RUN mkdir -p /var/log/php && \
    chown www-data:www-data /var/log/php && \
    chmod 755 /var/log/php

# Configurar o PHP
RUN { \
    echo 'display_errors = On'; \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'error_reporting = E_ALL'; \
    echo 'max_execution_time = 30'; \
    echo 'default_socket_timeout = 60'; \
    echo 'memory_limit = 128M'; \
    echo 'post_max_size = 20M'; \
    echo 'upload_max_filesize = 10M'; \
    echo 'max_input_time = 60'; \
} > /usr/local/etc/php/conf.d/custom.ini

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

# Criar diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY . .

# Verificar e ajustar permissões
RUN ls -la /var/www/html/public && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 644 /var/www/html/public/*.php && \
    chmod 755 /var/www/html/public

# Expor a porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
