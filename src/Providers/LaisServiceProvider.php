<?php

namespace LAIS\Scaffold\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

class LaisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $irregularInflectorRules = Config::get('plurals');
        if(!is_null($irregularInflectorRules))
        {
            Inflector::rules('plural', $irregularInflectorRules);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
