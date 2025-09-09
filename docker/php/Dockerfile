FROM php:8.1-fpm

# PHPの設定をコピー
COPY php.ini /usr/local/etc/php/

# 必要なパッケージをインストール
RUN apt-get update \
  && apt-get install -y \
  git \
  default-mysql-client \
  zlib1g-dev \
  libzip-dev \
  unzip \
  && docker-php-ext-install pdo_mysql zip \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Composerのインストール
RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer

# 作業ディレクトリ
WORKDIR /var/www