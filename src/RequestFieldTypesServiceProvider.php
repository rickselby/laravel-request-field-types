<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class RequestFieldTypesServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRequestFields($this->app);
    }

    /**
     * Register the Mapper decorator.
     *
     * @param Container $app
     */
    public function registerRequestFields(Container $app)
    {
        $app->singleton(FieldTypes::class, function () {
            return new FieldTypes();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            FieldTypes::class,
        ];
    }
}
