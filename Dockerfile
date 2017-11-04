FROM php:7.1-alpine

MAINTAINER Guilherme Silveira <xguiga@gmail.com>

# Download some php dependencies
RUN apk add --no-cache icu-dev && \
    docker-php-ext-configure intl && \
    NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} intl opcache pdo_mysql

# Configuring PHP
RUN echo -e "expose_php = Off;\n" \
            "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT;\n" \
            "log_errors = On;\n" \
            "short_open_tag = Off;\n" \
    > /usr/local/etc/php/php.ini && \
    echo -e "opcache.revalidate_freq = 60;\n" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Installing composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir /usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

WORKDIR /usr/share/applike

# Improving cache layer...
# First copy just composer.{json,lock} because if we change some file in our project
# we don't need to install all dependencies again, only if we change composer files related.
COPY composer.json composer.lock ./
COPY app/AppKernel.php app/AppCache.php ./app/
RUN composer install --no-dev --optimize-autoloader

# Now we can copy all files
COPY . ./

# Run symfony scripts
RUN composer symfony-scripts

EXPOSE 8000

CMD ["sh", "-c", "composer build-parameters && bin/console server:run 0.0.0.0:8000"]
