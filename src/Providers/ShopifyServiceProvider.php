<?php 

namespace Shopify\Providers;

use Illuminate\Support\ServiceProvider;

use Shopify\ShopifyBridge as Bridge;

class ShopifyServiceProvider extends ServiceProvider 
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/shopify.php' => config_path('shopify.php'),
        ], 'config');   
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        app()->bind('Shopify\Interfaces\TokenStore', 'Shopify\Stores\SessionTokenStore');
        app()->bind('Shopify\Interfaces\APICredentialsStore', 'Shopify\Stores\ConfigAPICredentialsStore');
        
        app()->bind('shopify', function(){
            return new Bridge;
        });

    }

}