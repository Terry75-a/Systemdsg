FROM php:8.3-cli
RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-install intl mysqli pdo_mysql && rm -rf /var/lib/apt/lists/*
WORKDIR /app
CMD ["php", "spark", "serve", "--host=0.0.0.0", "--port=8080"]
