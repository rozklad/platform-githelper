<?php namespace Sanatorium\Githelper\Providers;

use Cartalyst\Support\ServiceProvider;

class GithelperServiceProvider extends ServiceProvider {

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->prepareResources();
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {

    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($config, 'sanatorium-githelper');

        $this->publishes([
            $config => config_path('sanatorium-githelper.php'),
        ], 'config');
    }

}
