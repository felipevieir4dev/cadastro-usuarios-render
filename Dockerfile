FROM php:8.3-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar módulos do Apache necessários
RUN a2enmod rewrite headers

# Configurar o Apache
RUN echo '\
<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Configurar o DocumentRoot do Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Criar diretório público
RUN mkdir -p /var/www/html/public

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor a porta que o Render vai usar
EXPOSE 80

# Configurar o Apache para usar a porta do Render
ENV PORT=80
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground
