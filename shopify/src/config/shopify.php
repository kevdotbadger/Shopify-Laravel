<?php

return [

	'auth' => [
		
		// Your API Key
		'api_key' => env('SHOPIFY_API_KEY'),
		
		// Your App secret (also known as a shared ecret)
		'secret'  => env('SHOPIFY_SECRET'),
		
		// Your API Password. If this is set, all API calls will be made using the "Prvate API" method (i.e. the non-oAuth way)
		'password' => env('SHOPIFY_PASSWORD')
			
	],

	'resources' => [
		
	]

];