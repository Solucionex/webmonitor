FROM ubuntu/apache2:2.4-22.04_edge

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update -qy && \
    apt upgrade -qy && \
    apt install software-properties-common -qy

RUN add-apt-repository ppa:ondrej/php

RUN apt update -qy && \
    apt install -qy \
    php8.2 \
    php8.2-cli \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-intl \
    php8.2-zip \
    php8.2-xml \
    php8.2-fpm \
    php8.2-mysql \
    libapache2-mod-php8.2 \
    libapache2-mod-fcgid \
    git \
    supervisor \
    && rm -rf /var/lib/apt

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enconf php8.2-fpm
RUN a2enmod actions fcgid alias proxy_fcgi setenvif
RUN service php8.2-fpm restart
RUN service apache2 restart
