FROM php:8.1-apache

ENV LIB_DEPS="zlib1g-dev libzip-dev libpng-dev"
ENV ICU_RELEASE=68.1
ENV CXXFLAGS "--std=c++0x"

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends $LIB_DEPS \
        git \
        gnupg \
        fontconfig \
        fontconfig-config \
        fonts-dejavu-core \
        libc6-i386 \
        libfontconfig1 \
        libfontenc1 \
        libfreetype6 \
        libjpeg62-turbo \
        libpng16-16 \
        libx11-6 \
        libx11-data \
        libxau6 \
        libxcb1 \
        libxdmcp6 \
        libxext6 \
        libxfont2 \
        libxrender1 \
        p7zip-full \
        psmisc \
        ucf \
        unzip \
        x11-common \
        xfonts-75dpi \
        xfonts-base \
        xfonts-encodings \
        xfonts-utils \
        zip \
    \
    && a2enmod rewrite \
    \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd 
