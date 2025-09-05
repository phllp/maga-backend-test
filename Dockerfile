# ---------- 1) Build Tailwind assets ----------
FROM node:20-alpine AS assets
WORKDIR /app

# Only copy what's needed for Tailwind to resolve classes
COPY package*.json tailwind.config.js ./ 
COPY resources ./resources
# Tailwind scans your views to keep used classes
COPY app/Views ./app/Views

RUN npm ci --no-audit --fund=false \
 && npm run build
# output: /app/public/assets/app.css (see your package.json script)

# ---------- 2) Install PHP dependencies with Composer ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress

# ---------- 3) Runtime: PHP + pdo_pgsql ----------
FROM php:8.2-cli-alpine AS runtime
WORKDIR /app

# Install PostgreSQL PDO driver
RUN apk add --no-cache postgresql-dev \
 && docker-php-ext-install pdo_pgsql

# (Optional but nice) smaller images: clear apk cache already done by --no-cache

# Copy full source
COPY . .

# Bring in vendor/ from Composer stage
COPY --from=vendor /app/vendor ./vendor

# Ensure public/assets exists, then copy built CSS
RUN mkdir -p public/assets
COPY --from=assets /app/public/assets/app.css ./public/assets/app.css

# Expose dev port
EXPOSE 8000

# Environment (override via compose if you want)
ENV APP_DEV=false

# Run with PHP built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
