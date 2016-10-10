# Quickguide

* Add `Shopify\Providers\ShopifyServiceProvider::class,` to your providers array.
* Run `php artisan vendor:publish --provider="Shopify\Providers\ShopifyServiceProvider" --tag="config"` to create a shopify.php config file, and fill out the API vars (defaults to looking in the .env file).

To install an app (redirect the user to the install page), setup a route like /auth/redirect with:

```php
$url = $auth->asShop('kevinruscoe')
		->withScopes(['read_products', 'write_products'])
		->redirectingTo(route('auth.callback'))
		->requestAccess();

return redirect(url($url));		
```

Once the user has accepted the Shopify install T+C, and are redirected back to your app, in that route (like /auth/callback) do

```php
$token = $auth->requestAccessToken($request);

return redirect(url('products'));
```

Now, any controller that needs Shopify, add the `HasShopify` trait, then you can do somthing like

`$this->shopify()->Product->all()`