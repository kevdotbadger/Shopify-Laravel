<?php

namespace Shopify\Stores;

use Session;
use Shopify\Interfaces\TokenStore;

class SessionToken implements TokenStore { 

    public function setShop($shop){
        Session::set('shopify.shop', $shop);
    }

    public function getShop(){
        return Session::get('shopify.shop');
    }

    public function setToken($token){
        Session::set('shopify.access_token', $token);
    }

    public function getToken(){
        return Session::get('shopify.access_token');
    }

}
