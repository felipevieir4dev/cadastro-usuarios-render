FROM php:8.3-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo_mysql

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite headers

# Configurar o PHP para desenvolvimento
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Configurar o PHP
RUN echo "\n\
; Configurações de Erro\n\
error_reporting = E_ALL\n\
display_errors = On\n\
display_startup_errors = On\n\
log_errors = On\n\
error_log = /var/log/php/error.log\n\
\n\
; Configurações de Memória e Tempo\n\
memory_limit = 256M\n\
max_execution_time = 30\n\
\n\
; Configurações de Upload\n\
upload_max_filesize = 10M\n\
post_max_size = 10M\n\
\n\
; Configurações PDO\n\
pdo_mysql.default_socket = /var/run/mysqld/mysqld.sock\n\
" >> "$PHP_INI_DIR/php.ini"

# Configurar o Apache
RUN echo '\
<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    DirectoryIndex index.php\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        \n\
        <FilesMatch \.php$>\n\
            SetHandler application/x-httpd-php\n\
        </FilesMatch>\n\
    </Directory>\n\
    \n\
    # Logs detalhados\n\
    LogLevel debug\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Criar diretórios necessários
RUN mkdir -p /var/log/php /var/www/html/logs && \
    chown -R www-data:www-data /var/log/php /var/www/html/logs && \
    chmod -R 755 /var/log/php /var/www/html/logs

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Expor a porta que o Render vai usar
EXPOSE 80

# Configurar o Apache para usar a porta do Render
ENV PORT=80
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    echo "[$(date)] Container iniciado" >> /var/log/php/error.log && \
    apache2-foreground
