<?php

namespace Kevdotbadger\Shopify\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Shopify;
use Session;


class ShopifyController extends Controller
{

    protected $redirectTo = "/";

    protected function redirect(Request $request){

        $this->validate($request, 
            [
                'shop_url' => "required|regex:/[a-zA-Z0-9-_]+\\.myshopify\\.com/"
            ],
            [
                'shop_url.regex' => "Shop URL must be in the format 'shop.myshopify.com'."
            ]
        );

        $shop = str_replace(".myshopify.com", "", $request->get('shop_url'));

        Shopify::setShop($shop);

        $install_url = Shopify::getInstallUrl([
            'write_orders', 'read_orders',
            'write_products', 'read_products',
            'write_content', 'read_content',
        ], url('auth/shopify/callback'));

        return redirect($install_url);

    }
    
    protected function install(){
        return view('auth.shopify.install');     
    }

    protected function callback(Request $request){
        
        $code = $request->get('code');

        Shopify::requestToken($code);
        
        return redirect($this->redirectTo);

    }

    protected function logout(){
        
        Session::flush();
        
        return redirect(route('auth.shopify.install'));

    }

}
