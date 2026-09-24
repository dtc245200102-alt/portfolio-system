FROM php:8.4-fpm-alpine

RUN apk add --no-cache oniguruma \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && apk del .build-deps

COPY infra/php/app.ini /usr/local/etc/php/conf.d/zz-portfolio.ini
COPY infra/php/www.conf /usr/local/etc/php-fpm.d/zz-portfolio.conf
COPY --chown=www-data:www-data app /var/www/html/app
COPY --chown=www-data:www-data public /var/www/html/public
COPY --chown=www-data:www-data views /var/www/html/views

WORKDIR /var/www/html
USER www-data
EXPOSE 9000
CMD ["php-fpm", "-F"]
