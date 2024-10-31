FROM php:8.1-fpm

RUN useradd embapge -u 1000

# Arguments defined in docker-compose.yml
# ARG user
# ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip 

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --file

COPY . /var/www/

# Create system user to run Composer and Artisan Commands
# RUN useradd -G www-data,root -u $uid -d /home/$user $user
# RUN mkdir -p /home/$user/.composer && \
#     chown -R $user:$user /home/$user

# COPY --chown=www-data:www-data . /var/www/
# RUN chown -R www-data:www-data /var/www/


EXPOSE 9000

# USER www-data