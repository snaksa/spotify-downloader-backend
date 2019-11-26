FROM php:7.2-apache

ENV APACHE_DOCUMENT_ROOT=/vaw/www/html/public

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY .docker/default.conf /etc/apache2/sites-available/000-default.conf

RUN composer install

EXPOSE 80

RUN a2enmod rewrite

CMD /usr/sbin/apache2ctl -D FOREGROUND