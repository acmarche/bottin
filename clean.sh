#!/bin/bash
rm bootstrap/cache/*.php
rm -fr bootstrap/cache/filament/panels/
rm storage/framework/views/*.php
php artisan config:clear --silent
php artisan optimize:clear --silent
php artisan filament:optimize-clear --silent
rm -f storage/logs/*.log
