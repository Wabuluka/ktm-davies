FROM --platform=linux/amd64 node:18 AS build

COPY ./src /app
WORKDIR /app
RUN npm ci && npm run build

FROM --platform=linux/amd64 composer:2 as vendor

COPY ./src/composer.json composer.json
COPY ./src/composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

FROM --platform=linux/amd64 php:8.2-apache

ARG BASIC_USER=admin
ARG BASIC_PASS=admin
ARG USER=user
ARG UID=1000

RUN apt-get update && apt-get install -y \
        zip \
        unzip \
        libpng-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd pdo_mysql \
  && a2enmod rewrite

RUN useradd -G www-data,root -u $UID -d /home/$USER $USER && \
    mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

COPY --chown=$USER:$USER ./src /var/www/html

RUN echo ${BASIC_PASS} | htpasswd -i -c /var/www/html/public/.htpasswd ${BASIC_USER} \
  && chown $USER:$USER /var/www/html/public/.htpasswd

COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --chown=$USER:$USER --from=build /app/public/build /var/www/html/public/build
COPY --chown=$USER:$USER ./docker/laravel/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/laravel/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/html
USER $USER

ENTRYPOINT ["/entrypoint.sh"]
