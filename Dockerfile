# ベースイメージとしてPHP 8とApacheを使用
FROM php:8.2.21-apache

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql

# 作業ディレクトリの設定
WORKDIR /var/www/html

# ローカルのappディレクトリをコンテナの/var/www/htmlにコピー
COPY app/ /var/www/html/

# アップロードディレクトリの権限設定
RUN chmod -R 777 /var/www/html/uploads

# Apacheの設定
EXPOSE 80