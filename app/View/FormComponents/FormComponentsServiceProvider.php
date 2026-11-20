<?php

namespace App\View\FormComponents;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use App\View\FormComponents\FormDataBinder;

// Copied from protonemedia/laravel-form-components 3.8.0 (MIT, see LICENSE.md): the package stops at Laravel 10.
class FormComponentsServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(resource_path('views/vendor/form-components'), 'form-components');

        //

        Blade::directive('bind', function ($bind) {
            return '<?php app(\App\View\FormComponents\FormDataBinder::class)->bind(' . $bind . '); ?>';
        });

        Blade::directive('endbind', function () {
            return '<?php app(\App\View\FormComponents\FormDataBinder::class)->pop(); ?>';
        });

        Blade::directive('wire', function ($modifier) {
            return '<?php app(\App\View\FormComponents\FormDataBinder::class)->wire(' . $modifier . '); ?>';
        });

        Blade::directive('endwire', function () {
            return '<?php app(\App\View\FormComponents\FormDataBinder::class)->endWire(); ?>';
        });

        //

        $prefix = config('form-components.prefix');

        Collection::make(config('form-components.components'))->each(
            fn ($component, $alias) => Blade::component($alias, $component['class'], $prefix)
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(FormDataBinder::class, fn () => new FormDataBinder);
    }
}
