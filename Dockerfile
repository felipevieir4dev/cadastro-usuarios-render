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
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/

# Configurar o Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/

# Criar diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY . .

# Script de inicialização
COPY docker/scripts/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Verificar e ajustar permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    find /var/www/html/public -type f -name "*.php" -exec chmod 644 {} \;

# Expor a porta 80
EXPOSE 80

# Usar o script de inicialização
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
