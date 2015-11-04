<?php

namespace Kevdotbadger\Shopify\Middleware;

use Closure;
use Session;

class ShopifyRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
				
		if( !Session::has('shopify')){
			return redirect(route('auth.install'))->with(['error' => 'Please login.']);		
		}
				
        return $next($request);
    }
}
