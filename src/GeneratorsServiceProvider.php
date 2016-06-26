<?php
namespace Scaffold\Builder;

use Illuminate\Support\ServiceProvider;
class GeneratorsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('Scaffold\Builder\Scaffold');
    }
}
