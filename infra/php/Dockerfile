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
RUN mkdir -p /var/www/html/public/uploads \
    && chown www-data:www-data /var/www/html/public/uploads \
    && chmod 0755 /var/www/html/public/uploads

WORKDIR /var/www/html
USER www-data
EXPOSE 9000
CMD ["sh", "-c", "php /var/www/html/app/bin/migrate.php && exec php-fpm -F"]
