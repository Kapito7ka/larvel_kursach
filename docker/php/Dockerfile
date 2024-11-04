FROM php:8.3-fpm

ARG UID=1000
ARG GID=1000

# Set working directory
WORKDIR /var/www

# Add docker php ext repo
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring pdo_mysql pdo_pgsql zip exif pcntl gd memcached redis

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    lua-zlib-dev \
    libmemcached-dev \
    nginx \
    cron

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install node with nm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get install -y nodejs \
  && curl -L https://www.npmjs.com/install.sh | sh

# Add Yarn
RUN npm install -g yarn

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www


# PHP Error Log Files
RUN mkdir /var/log/php
RUN touch /var/log/php/errors.log && chmod 777 /var/log/php/errors.log

COPY ./start.sh /usr/local/bin/start

RUN chmod u+x /usr/local/bin/start
RUN chown www-data:www-data /usr/local/bin/start

USER www-data

CMD ["/usr/local/bin/start"]
