# Quickquide

Add `Kevdotbadger\Shopify\Providers\ShopifyServiceProvider.php` to your providers array, and `'Shopify' => Kevdotbadger\Shopify\Facades\Shopify::class` to your aliases array.

Run `php artisan vendor:publish --provider="Kevdotbadger\Shopify\Providers\ShopifyServiceProvider" --tag="config"` to create a shopify.php config file, and fill out the API vars.

Use something like

		Shopify::setShop('myshop');
				
		$install_url = Shopify::getInstallUrl([
			'write_orders', 'read_orders',
			'write_products', 'read_products',
			'write_content', 'read_content',
		]);
		
		return redirect($install_url);		

to install an app, then 

	$code = $request->get('code');
	
	Shopify::requestToken($code);

to turn a code into a token used to make API calls.

Then do stuff like `Shopify::get('products.json')` to call the shopify API.

### todo

* better docs ;)
* validate all requests
* allow users to use this library to make private requests (non oAuth requests)