<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AssetOptimizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register asset optimization services
        $this->app->singleton('asset.optimizer', function ($app) {
            return new \App\Services\AssetOptimizer();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add global view composers for performance
        view()->composer('*', function ($view) {
            $view->with('assetVersion', config('assets.cache.version', '1.0.0'));
        });

        // Add performance headers
        if (config('assets.optimization.enabled', true)) {
            $this->addPerformanceHeaders();
        }
    }

    /**
     * Add performance-related headers
     */
    private function addPerformanceHeaders()
    {
        if (app()->environment('production')) {
            // Add cache headers for static assets
            header('Cache-Control: public, max-age=31536000'); // 1 year for assets
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
    }
}
