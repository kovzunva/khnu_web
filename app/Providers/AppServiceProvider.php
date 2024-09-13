<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\ImageService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SpecificService::class, function ($app) {
            return new BaseItemService();
        });
        $this->app->bind(SpecificService::class, function ($app) {
            return new ImageService();
        });
        $this->app->bind(SpecificService::class, function ($app) {
            return new WorkService();
        });
        $this->app->bind(SpecificService::class, function ($app) {
            return new PersonService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Аватарка
        View::composer('layouts.app', function ($view) {
            $profile_img = ImageService::getImg('profile', auth()->id());
            $view->with('profile_img', $profile_img);
        });
    }
}
