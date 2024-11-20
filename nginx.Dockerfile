FROM --platform=$BUILDPLATFORM node AS static_builder
    WORKDIR /var/www/html
    COPY . /var/www/html
    RUN yarn && yarn build

FROM nginx AS production
    ENV PHP_FPM_HOST="php-fpm:9000"
    COPY --chown=www-data:www-data . /var/www/html
    COPY --from=static_builder --chown=www-data:www-data /var/www/html/public /var/www/html/public
    # Map the PHP-FPM host from the PHP_FPM_HOST environment variable to an nging variable
    RUN mkdir /etc/nginx/templates && cat <<EOF > /etc/nginx/templates/20-invoiceshelf.conf.template
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /var/www/html/public;
    index index.html index.htm index.php;

    server_name _;

    charset utf-8;

    client_max_body_size 2048M;

    gzip on;
    gzip_types text/css application/javascript application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;
    gzip_vary on;
    gzip_min_length 256;
    gzip_proxied any;

    location /healthcheck {
        access_log off;
        fastcgi_read_timeout 5s;

        include        fastcgi_params;
        fastcgi_param  SCRIPT_NAME     /healthcheck;
        fastcgi_param  SCRIPT_FILENAME /healthcheck;
        fastcgi_pass   \${PHP_FPM_HOST};
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include fastcgi_params;
        fastcgi_pass   \${PHP_FPM_HOST};
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;

        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_read_timeout 300;
    }
    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options SAMEORIGIN;
    add_header X-XSS-Protection "1; mode=block";
}
EOF

