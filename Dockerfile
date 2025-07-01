FROM php:8.2-apache

# Instala Git e as extensões necessárias
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

# Clona o projeto do GitHub diretamente para a pasta do Apache
RUN git clone https://github.com/JordanDurante/GerenciaRural2.git /var/www/html

# Cria diretório de sessões e define permissões corretas
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www-data:www-data /var/lib/php/sessions && \
    echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/session-path.ini

# Ativa mod_rewrite do Apache
RUN a2enmod rewrite

# Porta 80 interna → será exposta como 8080 no compose
EXPOSE 80

# Redireciona logs para volume
VOLUME ["/var/log/apache2"]

# Inicia o Apache
ENTRYPOINT ["apache2-foreground"]