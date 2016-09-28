<?php 

namespace Shopify\Stores;

use Config;

class APICredentials {

    function getKey(){
        return Config::get('shopify.auth.api_key');
    }

    function getSecret(){
        return Config::get('shopify.auth.secret');
    }

    function getPassword(){
        return Config::get('shopify.auth.password');
    }

}