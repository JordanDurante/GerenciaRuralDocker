FROM php:8.2-apache

# Instala as extensões necessárias
RUN apt-get update && apt-get install -y \
    zip unzip libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

# Cria diretório de sessões e define permissões corretas
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www-data:www-data /var/lib/php/sessions

# Configura PHP para usar esse diretório como session.save_path
RUN echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/session-path.ini

# Copia os arquivos da pasta www (fora do container) para o Apache
COPY ./www /var/www/html

# Ativa mod_rewrite do Apache
RUN a2enmod rewrite

# Porta 80 interna → será exposta como 8080 no compose
EXPOSE 80

# Redireciona logs para volume
VOLUME ["/var/log/apache2"]

# Inicia o Apache
ENTRYPOINT ["apache2-foreground"]