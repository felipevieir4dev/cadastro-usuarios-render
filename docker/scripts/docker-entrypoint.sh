#!/bin/sh
set -e

echo "Starting with configuration:"
echo "DB_HOST=${DB_HOST}"
echo "DB_PORT=${DB_PORT}"
echo "DB_NAME=${DB_NAME}"
echo "DB_USER=${DB_USER}"

# Aguardar o MySQL estar pronto
if [ -n "$DB_HOST" ]; then
    echo "Waiting for MySQL..."
    for i in $(seq 1 30); do
        if nc -z "$DB_HOST" "${DB_PORT:-3306}"; then
            echo "MySQL is up!"
            break
        fi
        echo "Attempt $i: MySQL is not ready yet..."
        sleep 1
    done
fi

# Iniciar o Apache em foreground
exec apache2-foreground
