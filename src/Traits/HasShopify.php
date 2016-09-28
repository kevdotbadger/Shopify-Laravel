<?php 

namespace Shopify\Traits;

trait HasShopify {

    private $shopify = null; 

    private function shopify(){

        if( ! $this->shopify ){
            $this->shopify = app()->make('ShopifyRestClient');
        }

        return $this->shopify;

    }

}

?>