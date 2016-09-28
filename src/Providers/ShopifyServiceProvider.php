<?php 

namespace Shopify\Providers;

use Illuminate\Support\ServiceProvider;

use Snorlax\RestClient;

use Shopify\Resources\Product;

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

        app()->bind('Shopify\Interfaces\TokenStore', 'Shopify\Stores\SessionToken');

        app()->singleton('ShopifyRestClient', function($app){

            $tokenStore = $app->make('Shopify\Stores\SessionToken');

            $access_token = $tokenStore->getToken();
            $shop = $tokenStore->getShop(); 

            $base_uri = "https://{$shop}.myshopify.com/admin/";

            return new RestClient([
                'resources' => [
                    'Product' => Product::class,
                    'ProductMetafield' => ProductMetafield::class,
                ],
                'client' => [
                    'params' => [
                        'base_uri' => $base_uri,
                        'headers' => [
                            'X-Shopify-Access-Token' => $access_token
                        ]
                    ]
                ]
            ]);

        });

    }

}