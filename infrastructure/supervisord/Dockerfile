FROM php:5.6-cli

# Install modules
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo_mysql


RUN pecl install xdebug

#Add PHP YAML support
RUN apt-get update && apt-get install -y libyaml-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && printf "\n" | pecl install yaml \
    && echo 'extension=yaml.so' > /usr/local/etc/php/conf.d/yaml.ini

#Add phalcon from source
RUN apt-get update && apt-get install -y git \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && git clone --depth=1 git://github.com/phalcon/cphalcon.git \
    && cd cphalcon/build && ./install \
    && echo 'extension=phalcon.so' > /usr/local/etc/php/conf.d/phalcon.ini \
    && cd ../.. && rm -rf cphalcon

RUN apt-get update && apt-get install -y supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./config/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]