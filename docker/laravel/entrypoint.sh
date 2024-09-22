#!/usr/bin/env bash

echo 'Info: Wating for database connection
'

for _ in $(seq 1 10); do
    if
        mysqladmin ping -h "$DB_HOST"
    then
        echo 'Connected
    '
        CONNECTED=true
        break
    else
        echo -n .
        sleep 1
    fi
done

if [ -z "${CONNECTED-}" ]; then
    echo 'Database connection refused
  ' >&2
    exit 1
fi

echo 'Info: Execute database migration'

php artisan migrate:fresh --seed --force
php artisan storage:link --force

php artisan route:cache
php artisan view:cache
php artisan config:cache

apache2-foreground
