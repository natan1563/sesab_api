#!/bin/bash
DB_HOST="127.0.0.1"
php artisan migrate
DB_HOST="database"
