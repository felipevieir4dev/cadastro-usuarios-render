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

# Definir variáveis de ambiente
ENV DB_HOST= \
    DB_USER= \
    DB_PASSWORD= \
    DB_NAME= \
    DB_PORT= \
    RENDER= \
    DISPLAY_ERRORS= \
    ERROR_REPORTING=

# Criar diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY . .

# Script de inicialização para configurar variáveis de ambiente
RUN echo '#!/bin/sh\n\
echo "Starting with configuration:"\n\
echo "DB_HOST=$DB_HOST"\n\
echo "DB_PORT=$DB_PORT"\n\
echo "DB_NAME=$DB_NAME"\n\
echo "DB_USER=$DB_USER"\n\
apache2-foreground\n\
' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Verificar e ajustar permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    find /var/www/html/public -type f -name "*.php" -exec chmod 644 {} \;

# Expor a porta 80
EXPOSE 80

# Usar o script de inicialização
CMD ["/usr/local/bin/docker-entrypoint.sh"]
