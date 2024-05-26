# Use the official PHP CLI image
FROM php:8.1-cli

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli

# Copy from composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /app

# Copy the application files to the container
COPY . .

# Keep container running
CMD ["tail", "-f", "/dev/null"]