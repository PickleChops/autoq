FROM php:5.6-cli

#Install unzip and libpq (used by Postgres)
RUN apt-get update && apt-get install -y zip unzip libpq-dev libmcrypt-dev telnet nano \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install modules
RUN docker-php-ext-install mbstring \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && pecl install xdebug

#Add PHP YAML support
RUN apt-get update && apt-get install -y libyaml-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && printf "\n" | pecl install yaml \
    && echo 'extension=yaml.so' > /usr/local/etc/php/conf.d/yaml.ini

#Add phalcon from source
RUN apt-get update && apt-get install -y git \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && git clone --depth=1 --branch phalcon-v2.0.13 https://github.com/phalcon/cphalcon.git \
    && cd cphalcon/build && ./install \
    && echo 'extension=phalcon.so' > /usr/local/etc/php/conf.d/phalcon.ini \
    && cd ../.. && rm -rf cphalcon

#Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
