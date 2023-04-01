<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MemoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind('HelpSpot\API', function ($app) {
        //     return new \HelpSpot\API($app->make('HttpClient'));
        // });
        $this->app->when('App\Http\Controllers\MemoController')
          ->needs('$variableName')
          ->give($value);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
