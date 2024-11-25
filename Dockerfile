FROM serversideup/php:8-fpm-alpine AS base
    USER root
    RUN install-php-extensions exif pgsql sqlite3 imagick/imagick@28f27044e435a2b203e32675e942eb8de620ee58 mbstring \
        gd xml zip redis bcmath intl curl xhprof

FROM base AS development
    ARG UID
    ARG GID

    USER root
    RUN docker-php-serversideup-set-id www-data $UID:$GID
    USER www-data

FROM --platform=$BUILDPLATFORM node AS static_builder
    WORKDIR /var/www/html
    COPY . /var/www/html
    RUN yarn && yarn build

FROM base AS production
    ENV AUTORUN_ENABLED=true
    COPY --from=static_builder --chown=www-data:www-data /var/www/html/public /var/www/html/public
    COPY --chown=www-data:www-data . /var/www/html
    RUN composer install --prefer-dist
    USER www-data

