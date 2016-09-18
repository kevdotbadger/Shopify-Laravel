# Quickguide

* Add `Kevdotbadger\Shopify\Providers\ShopifyServiceProvider.php` to your providers array.
* Add `'Shopify' => Kevdotbadger\Shopify\Facades\Shopify::class` to your aliases array.
* Run `php artisan vendor:publish --provider="Kevdotbadger\Shopify\Providers\ShopifyServiceProvider" --tag="config"` to create a shopify.php config file, and fill out the API vars (defaults to looking in the .env file).

To install an app (redirect the user to the install page), setup a route like /auth/redirect with:

	Shopify::setShop('myshop');
				
	$install_url = Shopify::getInstallUrl([
		'write_orders', 'read_orders',
		'write_products', 'read_products',
		'write_content', 'read_content',
	]);
		
	return redirect($install_url);		

Once the user has accepted the Shopify install T+C, and are redirected back to your app, simply request the `code` $_GET variable and turn it into a token to make API calls.

	$code = $request->get('code');
	
	Shopify::requestToken($code);

Then do stuff like `Shopify::get('products.json')` to call the shopify API.

The package also comes with a sample controller and routes. Add `Shopify::routes()` to your routes file to add them. 

### todo

* better docs ;)
* validate all requests
* allow users to use this library to make private requests (non oAuth requests)
