FROM php:8.3-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo_mysql

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite

# Configurar o PHP
RUN echo "display_errors = On" >> "$PHP_INI_DIR/conf.d/error-logging.ini" && \
    echo "error_log = /dev/stderr" >> "$PHP_INI_DIR/conf.d/error-logging.ini"

# Configurar o Apache
RUN echo '\
<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    ErrorLog /dev/stderr\n\
    CustomLog /dev/stdout combined\n\
    <Directory /var/www/html/public>\n\
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
RUN chown -R www-data:www-data /var/www/html

# Expor a porta que o Render vai usar
EXPOSE 80

# Configurar o Apache para usar a porta do Render
ENV PORT=80
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && \
    apache2-foreground
