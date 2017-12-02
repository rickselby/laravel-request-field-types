<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

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
        $this->registerCollectionMacros();
    }

    /**
     * Register the Mapper decorator.
     *
     * @param Container $app
     */
    protected function registerRequestFields(Container $app)
    {
        $app->singleton(FieldTypes::class, function () use ($app) {
            return new FieldTypes($app);
        });
    }

    protected function registerCollectionMacros()
    {
        /*
         * Order a macro by passing in an array of keys in the desired order
         */
        Collection::macro('setKeyOrder', function (array $keyOrder) {
            // Can't touch original, but we want to pull items from it, so make a copy
            $original = clone $this;
            $replacement = new Collection();

            foreach ($keyOrder as $key) {
                if ($original->has($key)) {
                    $replacement->put($key, $original->pull($key));
                }
            }

            // Tack on any remaining rules at the end
            return $replacement->union($original);
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
