<?php

namespace Shopify\Interfaces;

interface TokenStore {

    public function setShop($shop);
    public function getShop();

    public function setToken($token);
    public function getToken();

}
