FROM php:5.6.19-fpm

# Install modules
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install bcmath
RUN pecl install xdebug

#Add phalcon from source
RUN apt-get update && apt-get install -y git \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && git clone --depth=1 git://github.com/phalcon/cphalcon.git \
    && cd cphalcon/build && ./install \
    && echo 'extension=phalcon.so' > /usr/local/etc/php/conf.d/phalcon.ini \
    && cd ../.. && rm -rf cphalcon

