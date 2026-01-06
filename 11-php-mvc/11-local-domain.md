# Local Domain Setup and Development

## Overview

Configure local development environment with custom domains, virtual hosts, and development tools for testing your MVC application locally.

---

## Table of Contents

1. Local Development Overview
2. Virtual Hosts
3. DNS Configuration
4. SSL/TLS for HTTPS
5. Development Tools
6. Environment Configuration
7. Complete Examples

---

## Local Development Overview

### Development Environment

```
Local Development Setup:

Web Server (Apache/Nginx)
  ↓
Virtual Hosts Configuration
  ↓
DNS Resolution (hosts file)
  ↓
Local Domain (example.local)
  ↓
PHP Application
```

### Why Custom Domains

```
Benefits:
✓ Test real URL patterns
✓ Test cookies and sessions
✓ Test API endpoints properly
✓ Realistic development
✓ Better production parity
✓ Test multiple domains
✓ Test subdomains
```

---

## Virtual Hosts

### Apache Virtual Hosts

```apache
# macOS: /usr/local/etc/httpd/extra/httpd-vhosts.conf
# Linux: /etc/apache2/sites-available/

<VirtualHost *:80>
    ServerName example.local
    ServerAlias www.example.local
    
    DocumentRoot "/Users/username/Sites/example"
    
    <Directory "/Users/username/Sites/example">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog "/Users/username/Sites/example/logs/error.log"
    AccessLog "/Users/username/Sites/example/logs/access.log" combined
</VirtualHost>

<VirtualHost *:443>
    ServerName example.local
    ServerAlias www.example.local
    
    DocumentRoot "/Users/username/Sites/example"
    
    SSLEngine on
    SSLCertificateFile "/usr/local/etc/httpd/certs/example.local.crt"
    SSLCertificateKeyFile "/usr/local/etc/httpd/certs/example.local.key"
    
    <Directory "/Users/username/Sites/example">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Virtual Hosts

```nginx
# /usr/local/etc/nginx/servers/example.local.conf

server {
    listen 80;
    listen 443 ssl http2;
    
    server_name example.local www.example.local;
    
    root /Users/username/Sites/example/public;
    index index.php;
    
    # SSL Configuration
    ssl_certificate /usr/local/etc/nginx/certs/example.local.crt;
    ssl_certificate_key /usr/local/etc/nginx/certs/example.local.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    
    # Redirect HTTP to HTTPS
    if ($scheme != "https") {
        return 301 https://$server_name$request_uri;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Rewrite rules
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Logs
    error_log /Users/username/Sites/example/logs/error.log;
    access_log /Users/username/Sites/example/logs/access.log;
}
```

---

## DNS Configuration

### Hosts File Setup

```bash
# macOS and Linux: /etc/hosts
# Windows: C:\Windows\System32\drivers\etc\hosts

127.0.0.1   example.local
127.0.0.1   www.example.local
127.0.0.1   api.example.local
127.0.0.1   admin.example.local

# IPv6
::1         example.local
::1         www.example.local
```

### Testing DNS Resolution

```bash
# Test domain resolution
ping example.local
nslookup example.local
dig example.local

# Windows
ipconfig /flushdns

# macOS
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder

# Linux
sudo systemctl restart systemd-resolved
```

### dnsmasq Configuration (Advanced)

```bash
# Install dnsmasq (macOS)
brew install dnsmasq

# Configure /usr/local/etc/dnsmasq.conf
address=/example.local/127.0.0.1
address=/api.example.local/127.0.0.1
address=/admin.example.local/127.0.0.1

# Start service
sudo brew services start dnsmasq

# Restart resolver
sudo launchctl stop com.apple.mDNSResponder
sudo launchctl start com.apple.mDNSResponder
```

---

## SSL/TLS for HTTPS

### Generate Self-Signed Certificates

```bash
# Create certificate directory
mkdir -p /usr/local/etc/httpd/certs
cd /usr/local/etc/httpd/certs

# Generate private key
openssl genrsa -out example.local.key 2048

# Create certificate request
openssl req -new \
    -key example.local.key \
    -out example.local.csr \
    -subj "/C=US/ST=State/L=City/O=Company/CN=example.local"

# Generate self-signed certificate (365 days)
openssl x509 -req -days 365 \
    -in example.local.csr \
    -signkey example.local.key \
    -out example.local.crt

# Alternative: Generate both in one command
openssl req -x509 -newkey rsa:2048 -keyout example.local.key \
    -out example.local.crt -days 365 -nodes \
    -subj "/C=US/ST=State/L=City/O=Company/CN=example.local"

# Trust certificate (macOS)
sudo security add-trusted-cert -d -r trustRoot \
    -k /Library/Keychains/System.keychain \
    /usr/local/etc/httpd/certs/example.local.crt
```

### Wildcard Certificate

```bash
# For *.example.local
openssl req -x509 -newkey rsa:2048 -keyout example.local.key \
    -out example.local.crt -days 365 -nodes \
    -subj "/CN=*.example.local"
```

---

## Development Tools

### Docker Setup

```dockerfile
# Dockerfile for PHP development

FROM php:8.1-fpm

# Install extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

EXPOSE 9000

CMD ["php-fpm"]
```

### Docker Compose

```yaml
# docker-compose.yml

version: '3.9'

services:
  php:
    build: .
    volumes:
      - .:/app
    ports:
      - "9000:9000"
    environment:
      - DB_HOST=db
      - DB_NAME=app_db
    depends_on:
      - db
  
  nginx:
    image: nginx:alpine
    volumes:
      - .:/app
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      - php
  
  db:
    image: postgres:13
    environment:
      POSTGRES_DB: app_db
      POSTGRES_USER: app
      POSTGRES_PASSWORD: secret
    volumes:
      - db_data:/var/lib/postgresql/data

volumes:
  db_data:
```

### Makefile for Development

```makefile
# Makefile

.PHONY: help install start stop logs test

help:
	@echo "Available commands:"
	@echo "  make install   - Install dependencies"
	@echo "  make start     - Start development server"
	@echo "  make stop      - Stop servers"
	@echo "  make logs      - View logs"
	@echo "  make test      - Run tests"

install:
	composer install
	npm install

start:
	docker-compose up -d

stop:
	docker-compose down

logs:
	docker-compose logs -f

test:
	./vendor/bin/phpunit

dev-server:
	php -S example.local:8000 -t public/

db-migrate:
	php bin/console migrate

db-seed:
	php bin/console seed

clean:
	rm -rf vendor node_modules
	rm -rf logs/*
```

---

## Environment Configuration

### .env File

```bash
# .env (for local development)
# This file is NOT committed to version control

APP_ENV=local
APP_DEBUG=true
APP_URL=http://example.local

DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=app_db_local
DB_USER=app
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM=dev@example.local

API_KEY=dev-key-123456

LOG_LEVEL=debug
```

### PHP Configuration for Development

```php
<?php
// config/development.php

return [
    'debug' => true,
    'display_errors' => true,
    'log_errors' => true,
    'error_log' => __DIR__ . '/../logs/error.log',
    
    'database' => [
        'driver' => 'pgsql',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: 5432,
        'database' => getenv('DB_NAME') ?: 'app_db_local',
        'username' => getenv('DB_USER') ?: 'app',
        'password' => getenv('DB_PASSWORD') ?: 'secret',
    ],
    
    'cache' => [
        'driver' => 'file',
        'path' => __DIR__ . '/../storage/cache',
    ],
    
    'session' => [
        'driver' => 'file',
        'path' => __DIR__ . '/../storage/sessions',
    ],
];
```

---

## Complete Examples

### Example 1: Local Setup Script

```bash
#!/bin/bash
# setup-local.sh

DOMAIN="example.local"
SITE_DIR="/Users/username/Sites/example"

echo "Setting up local development environment..."

# 1. Add to hosts file
echo "Adding $DOMAIN to /etc/hosts..."
echo "127.0.0.1   $DOMAIN" | sudo tee -a /etc/hosts
echo "127.0.0.1   www.$DOMAIN" | sudo tee -a /etc/hosts

# 2. Create project directory
mkdir -p "$SITE_DIR"/{public,logs,storage}

# 3. Create .env file
cp "$SITE_DIR/.env.example" "$SITE_DIR/.env"

# 4. Install dependencies
cd "$SITE_DIR"
composer install
npm install

# 5. Generate certificates
mkdir -p /usr/local/etc/httpd/certs
openssl req -x509 -newkey rsa:2048 -keyout /usr/local/etc/httpd/certs/$DOMAIN.key \
    -out /usr/local/etc/httpd/certs/$DOMAIN.crt -days 365 -nodes \
    -subj "/C=US/ST=State/L=City/O=Company/CN=$DOMAIN"

# 6. Restart web server
sudo apachectl restart

echo "✓ Local environment setup complete!"
echo "Visit: https://$DOMAIN"
```

### Example 2: Docker Development

```bash
#!/bin/bash
# Start development environment

docker-compose up -d

echo "Waiting for services to start..."
sleep 5

# Run migrations
docker-compose exec php php bin/console migrate

# Seed database
docker-compose exec php php bin/console seed

echo "✓ Development environment ready!"
echo "Visit: http://example.local"
```

### Example 3: Development Server

```php
<?php
// bin/server.php - Built-in PHP server

$host = 'example.local';
$port = 8000;
$docroot = __DIR__ . '/../public';

echo "Starting PHP development server at http://$host:$port\n";
echo "Document root: $docroot\n";
echo "Press Ctrl+C to stop.\n\n";

// Run server
$cmd = "php -S $host:$port -t $docroot";
exec($cmd);
```

---

## Key Takeaways

**Local Development Checklist:**

1. ✅ Configure virtual hosts
2. ✅ Add domain to hosts file
3. ✅ Set up SSL/TLS certificates
4. ✅ Configure DNS resolution
5. ✅ Create .env configuration
6. ✅ Set up development database
7. ✅ Configure logging
8. ✅ Test all endpoints
9. ✅ Document setup process
10. ✅ Create setup scripts

---

## See Also

- [Project Setup](2-create-project.md)
- [Configuration](2-create-project.md)
- [MVC Basics](0-mvc-basics.md)
