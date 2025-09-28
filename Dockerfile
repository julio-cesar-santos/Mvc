# Usa a imagem base do PHP 8.2 com Apache
FROM php:8.2-apache

# Instala as extensões essenciais para o seu projeto
RUN docker-php-ext-install pdo pdo_mysql

# --- Configuração do Apache para a pasta 'public' ---
# Modifica o Virtual Host padrão para servir a partir de /var/www/html/public
RUN echo "DocumentRoot /var/www/html/public" > /etc/apache2/sites-available/000-default.conf
RUN echo "<Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    Options Indexes FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf
RUN echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf
RUN echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf

# Habilita o mod_rewrite, necessário para o roteamento MVC no .htaccess
RUN a2enmod rewrite

# O diretório de trabalho será a raiz do projeto (mapeado via volume)
WORKDIR /var/www/html