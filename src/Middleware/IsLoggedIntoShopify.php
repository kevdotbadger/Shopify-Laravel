<?php 

namespace Shopify\Middleware;

use Closure;

use Shopify;

class IsLoggedIntoShopify
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
        $response = $next($request);

        if( ! Shopify::isLoggedIn() ){
            return redirect(route('auth.shopify.install'))
                ->with('info', 'You have been logged out');
        }        

        return $response;
    }
}
