<?php

namespace TomatoPHP\TomatoFlutter;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\TomatoFlutter\Console\TomatoFlutterGenerate;


class TomatoFlutterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           TomatoFlutterGenerate::class
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/tomato-flutter.php', 'tomato-flutter');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/tomato-flutter.php' => config_path('tomato-flutter.php'),
        ], 'tomato-flutter-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'tomato-flutter-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tomato-flutter');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/tomato-flutter'),
        ], 'tomato-flutter-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tomato-flutter');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => app_path('lang/vendor/tomato-flutter'),
        ], 'tomato-flutter-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    public function boot(): void
    {
        //you boot methods here
    }
}
