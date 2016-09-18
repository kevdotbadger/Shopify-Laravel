<?php

namespace Kevdotbadger\Shopify\Providers;

use Illuminate\Support\ServiceProvider;

	
class ShopifyServiceProvider extends ServiceProvider {
	
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/shopify.php' => config_path('shopify.php'),
		], 'config');

		if( config('shopify.auto_register_routes') ){
			app('router')->get('auth/shopify/install', ['uses' => '\Kevdotbadger\Shopify\Controllers\Auth\ShopifyController@install']);
			app('router')->post('auth/shopify/install', ['uses' => '\Kevdotbadger\Shopify\Controllers\Auth\ShopifyController@redirect', 'as' => 'auth.shopify.install']);
			app('router')->get('auth/shopify/callback', ['uses' => '\Kevdotbadger\Shopify\Controllers\Auth\ShopifyController@callback', 'as' => 'auth.shopify.callback']);
			app('router')->get('auth/shopify/logout', ['uses' => '\Kevdotbadger\Shopify\Controllers\Auth\ShopifyController@logout', 'as' => 'auth.shopify.logout']);
		}

	}
	
	/**
	* Register the service provider.
	*
	* @return void
	*/
	public function register()
	{
		$this->app->bind('shopify', function(){
			return new \Kevdotbadger\Shopify\Shopify;
		});
	}
	   
}

?>