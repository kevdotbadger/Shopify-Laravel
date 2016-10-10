<?php 

namespace Shopify\Stores;

use Shopify\Interfaces\APICredentialsStore;

use Config;

class ConfigAPICredentialsStore implements APICredentialsStore {

    function setKey($key){
        return;
    }

    function getKey(){
        return Config::get('shopify.auth.api_key');
    }

    function setSecret($secret){
        return;
    }

    function getSecret(){
        return Config::get('shopify.auth.secret');
    }
    
}