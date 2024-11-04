<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


use App\Repository\Classes\TestRepositoryClass; 
use App\Repository\Interfaces\TestRepositoryInterface; 

use App\Repository\Classes\FileRepositoryClass; 
use App\Repository\Interfaces\FileRepositoryInterface; 
class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       $this->app->bind(FileRepositoryInterface::class, function () {
         return new FileRepositoryClass();
       });

       $this->app->bind(TestRepositoryInterface::class, function () {
         return new TestRepositoryClass();
       });

        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
