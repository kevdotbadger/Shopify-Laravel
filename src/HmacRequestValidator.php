<?php

namespace Shopify;

use Exceptions\InvalidHMACException;
use Illuminate\Http\Request;

class HmacRequestValidator {

    public static function validate(Request $request, $secret){

        $data = $request->all();

        $hmac = $data['hmac'];

        unset($data['hmac']);

        ksort($data);

        $data = http_build_query($data);

        $calculated = hash_hmac('sha256', $data, $secret);

        if( $calculated !== $hmac ){
            throw new InvalidHMACException;
        }

        return true;

    }

}