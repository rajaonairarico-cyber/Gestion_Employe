FROM php:8.2-cli

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY . /app
WORKDIR /app

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t /app"]