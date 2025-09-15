<?php

namespace Schorpy\TriggerDev;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TriggerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/triggerdev.php',
            'triggerdev',
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootRoutes();
        $this->bootPublishing();
    }

    /**
     * Boot the package routes.
     *
     * @return void
     */
    protected function bootRoutes()
    {
        if (TriggerDev::$registersRoutes) {
            Route::group([
                'prefix' => config('triggerdev.path'),
                'namespace' => 'Schorpy\TriggerDev\Http\Controllers',
                'as' => 'trigger.',
            ], function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }
    }

    protected function bootPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/triggerdev.php' => $this->app->configPath('triggerdev.php'),
            ], 'triggerdev-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'triggerdev-migrations');
        }
    }
}
