#!/bin/bash

# Start the Laravel schedule worker in the background
# This acts like a cron job but stays within the PHP environment
php artisan schedule:work &

# Start the main Apache process (foreground)
exec apache2-foreground