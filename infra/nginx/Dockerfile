FROM nginxinc/nginx-unprivileged:1.29-alpine

COPY infra/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --chown=nginx:nginx public/ /var/www/html/public/
