#!/bin/bash

echo "🚀 Starting Laravel Optimization..."

# Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
echo "📦 Optimizing Composer autoloader..."
composer dump-autoload --optimize

# Clear and optimize Laravel caches
echo "🔧 Optimizing Laravel..."
php artisan optimize

echo "✅ Optimization complete!"
echo ""
echo "📊 Performance tips applied:"
echo "- Database queries optimized with caching"
echo "- Images set to lazy loading"
echo "- CSS/JS assets optimized"
echo "- Database indexes added"
echo "- Route and config caching enabled"
echo ""
echo "🎯 Next steps for production:"
echo "1. Enable Redis/Memcached for better caching"
echo "2. Use CDN for static assets"
echo "3. Enable Gzip compression on web server"
echo "4. Use image optimization service"
