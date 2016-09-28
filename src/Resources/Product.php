<?php

namespace Shopify\Resources;

use Snorlax\Resource;
use Carbon\Carbon;

class Product extends Resource
{  
    
    protected $dates = ['updated_at', 'published_at', 'created_at'];
    
    public function getBaseUri(){
        return 'products';
    }

    public function getActions(){
        return [
            'all' => [
                'method' => 'GET',
                'path' => '.json'
            ],
            'get' => [
                'method' => 'GET',
                'path' => '/{0}.json'
            ]
        ];
    }
    
    public function parse($action, $response){
        $response = parent::parse($action, $response);
                
        switch( $action ){
            case 'all':
                
                $collection = collect([]);
                
                foreach($response->products as $product){
                    $collection->push($this->parseDates($product));
                }
                
                $response = $collection;
                
            break;
            case 'get':
                $response = $this->parseDates($response->product);
            break;
        }
                
        return $response;
    }
    
    private function parseDates($item){
        
        $attrs = get_object_vars($item);
        
        foreach( $attrs as $key => $value ){
            
            if( in_array($key, $this->dates) ){
                $item->{$key} = Carbon::parse($value);
            }
            
            if( is_object($value) ){
                $item->{$key} = $this->parseDates($value);
            }
            
            if( is_array($value) ){
                
                $item->{$key} = collect($item->{$key});
                
                $item->{$key} = $item->{$key}->each(function($item, $key){
                    return $this->parseDates((object)$item);
                });
                
            }
            
        }
        
        return $item;
        
    }
}