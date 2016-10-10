<?php

namespace Shopify;

use GuzzleHttp\Client as GuzzleClient;

use Shopify\Resources\Product;
use Shopify\Resources\ProductMetafield;
use Shopify\Resources\Metafield;

use Shopify\Authenticator;
use Snorlax\RestClient;

use Config;

class ShopifyBridge {

    function getShopName(){
        return $this->getTokenStore()->getShop();
    }

    function getShopUrl(){
        return "https://{$this->getTokenStore()->getName()}.myshopify.com";
    }

    function getAPIKey(){
        return $this->getAPICredentials()->getKey();
    }

    function getAPICredentials(){
        return app()->make('Shopify\Interfaces\APICredentialsStore');
    }

    function getTokenStore(){
        return app()->make('Shopify\Interfaces\TokenStore');
    }

    function getAuthenticator(){
        return new Authenticator(
            new GuzzleClient,
            $this->getAPICredentials(), 
            $this->getTokenStore()
        );
    }

    function getClient(){

        $tokenStore = $this->getTokenStore();

        $access_token = $tokenStore->getToken();
        $shop = $tokenStore->getShop(); 

        $base_uri = "https://{$shop}.myshopify.com/admin/";

        return new RestClient([
            'resources' => Config::get('shopify.resources'),
            'client' => [
                'params' => [
                    'base_uri' => $base_uri,
                    'headers' => [
                        'X-Shopify-Access-Token' => $access_token
                    ]
                ]
            ]
        ]);

    }

    function isLoggedIn(){

        $tokenStore = $this->getTokenStore();

        return is_null($tokenStore->getShop()) ? false : true; 


    }
    

}