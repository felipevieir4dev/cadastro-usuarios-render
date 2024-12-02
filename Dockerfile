FROM php:8.3-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo_mysql

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite headers

# Configurar o PHP para desenvolvimento
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

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
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Criar diretório para logs do PHP
RUN mkdir -p /var/log/php && \
    chown -R www-data:www-data /var/log/php

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor a porta que o Render vai usar
EXPOSE 80

# Configurar o Apache para usar a porta do Render
ENV PORT=80
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    apache2-foreground
