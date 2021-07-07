<?php

namespace MahmoudMAbadi\ExcelExportWithRelation;

use Illuminate\Support\ServiceProvider;

class ExcelExportWithRelationServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        //
    }

    /**
     *
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'ExcelExportWithRelation');

        if ($this->app->runningInConsole()) {
            // Publish views
            $this->publishes([
                __DIR__.'/Resources/views' => resource_path('views/vendor/MahmoudMAbadi/ExcelExportWithRelation'),
            ], 'views');

        }

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
    }
}
