#!/usr/bin/env bash

set -e

echo "Installing PHP dependencies..."
composer install

echo "Installing Node dependencies..."
npm install

if [ ! -f ".env" ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
else
    echo ".env already exists, skipping copy."
fi

echo "Preparing SQLite database..."
mkdir -p database
rm -f database/database.sqlite
touch database/database.sqlite

echo "Cleaning public storage..."
rm -rf storage/app/public/*
rm -rf public/storage

echo "Clearing cached configuration..."
php artisan config:clear

echo "Generating application key..."
php artisan key:generate

echo "Running migrations and seeders..."
php artisan migrate:fresh --seed

echo "Creating storage link..."
php artisan storage:link

echo "Building frontend assets..."
npm run build

echo "Setup completed."
echo "Run the application with:"
echo "php artisan serve"