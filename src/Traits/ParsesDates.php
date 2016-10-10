<?php 

namespace Shopify\Traits;

use Carbon\Carbon;

trait ParsesDates {

    function parseDates($item){
        
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