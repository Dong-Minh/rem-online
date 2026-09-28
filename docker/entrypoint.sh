#!/bin/bash
set -Eeuo pipefail

cd /var/www

# Render secret mounts may be readable by root but not by www-data.
# Copy only the CA certificate at runtime to an app-readable private location.
if [[ -n "${MYSQL_ATTR_SSL_CA:-}" ]]; then
    if [[ ! -f "$MYSQL_ATTR_SSL_CA" || ! -r "$MYSQL_ATTR_SSL_CA" ]]; then
        echo "Cannot read MySQL CA file. Check Render Secret Files and MYSQL_ATTR_SSL_CA." >&2
        exit 1
    fi
    (
        umask 077
        mkdir -p /run/app-certificates
        chown root:www-data /run/app-certificates
        chmod 750 /run/app-certificates
        cp "$MYSQL_ATTR_SSL_CA" /run/app-certificates/mysql-ca.pem
        chown www-data:www-data /run/app-certificates/mysql-ca.pem
        chmod 400 /run/app-certificates/mysql-ca.pem
    )
    export MYSQL_ATTR_SSL_CA=/run/app-certificates/mysql-ca.pem
    su-exec www-data php docker/check-ca.php
fi

# Allow maintenance commands with: docker run ... IMAGE php artisan ...
if (( $# > 0 )); then
    exec su-exec www-data "$@"
fi

: "${APP_KEY:?Set a persistent APP_KEY before starting the application}"
CLEAN_URL=$(echo "${APP_URL:-http://localhost}" | tr -d '"' | tr -d "'" | tr -d '[:space:]')
if [[ -z "$CLEAN_URL" || "$CLEAN_URL" == "http://" || "$CLEAN_URL" == "https://" ]]; then
    CLEAN_URL="http://localhost"
elif [[ ! "$CLEAN_URL" =~ ^https?:// ]]; then
    CLEAN_URL="https://${CLEAN_URL}"
fi
export APP_URL="${CLEAN_URL}"
export PORT="${PORT:-10000}"

if [[ ! "$PORT" =~ ^[0-9]{1,5}$ ]] || (( 10#$PORT < 1 || 10#$PORT > 65535 )); then
    echo "PORT must be an integer between 1 and 65535" >&2
    exit 1
fi

# Substitute PORT only; preserve Nginx variables such as $uri and $query_string.
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create storage symlink if not exists
su-exec www-data php artisan storage:link 2>/dev/null || true

su-exec www-data php artisan config:cache

NORM_RUN_MIGRATIONS=$(echo "${RUN_MIGRATIONS:-true}" | tr -d '"' | tr -d "'" | tr '[:upper:]' '[:lower:]' | xargs)
case "$NORM_RUN_MIGRATIONS" in
    true|1|yes) su-exec www-data php artisan migrate --force --no-interaction ;;
    false|0|no|"") ;;
    *) echo "RUN_MIGRATIONS must be true or false (got: $RUN_MIGRATIONS)" >&2; exit 1 ;;
esac

NORM_RUN_SEEDERS=$(echo "${RUN_SEEDERS:-false}" | tr -d '"' | tr -d "'" | tr '[:upper:]' '[:lower:]' | xargs)
case "$NORM_RUN_SEEDERS" in
    true|1|yes) su-exec www-data php artisan db:seed --force --no-interaction ;;
    false|0|no|"") ;;
    *) echo "RUN_SEEDERS must be true or false (got: $RUN_SEEDERS)" >&2; exit 1 ;;
esac

su-exec www-data php artisan route:cache
su-exec www-data php artisan view:cache

nginx -t
php-fpm -t

# Stop the whole container if either server exits, and forward stop signals.
server_pids=()
cleanup() {
    trap - EXIT TERM INT
    if (( ${#server_pids[@]} )); then
        kill -QUIT "${server_pids[@]}" 2>/dev/null || true
        wait "${server_pids[@]}" 2>/dev/null || true
    fi
}

trap cleanup EXIT
trap 'exit 0' TERM INT

php-fpm -F &
server_pids+=("$!")
nginx -g 'daemon off;' &
server_pids+=("$!")

status=0
wait -n "${server_pids[@]}" || status=$?
echo "A web server exited (status $status); stopping container" >&2
exit 1
