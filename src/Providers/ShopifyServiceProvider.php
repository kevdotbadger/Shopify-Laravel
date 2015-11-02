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