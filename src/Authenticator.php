<?php

namespace Shopify;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Shopify\Interfaces\APICredentialsStore;
use Shopify\Interfaces\TokenStore;

use Shopify\HmacRequestValidator;

class Authenticator {

    private $APICredentials;
    private $tokenStore;

    private $client;
    
    private $redirect_to;
    private $scopes = [];

    function __construct(Client $client, APICredentialsStore $APICredentials, TokenStore $tokenStore){
        $this->APICredentials = $APICredentials;
        $this->tokenStore = $tokenStore;
        $this->client = $client;
    }

    function asShop($shop){

        $shop = str_replace(['.myshopify.com', 'http://', 'https://'], '', $shop);

        $this->tokenStore->setShop($shop);

        return $this;
    }

    function withScopes($scopes = []){

        $this->scopes = $scopes;

        return $this;
    }

    function redirectingTo($redirect_to){

        $this->redirect_to = $redirect_to;

        return $this;
    }

    function requestInstallationUrl(){

        $url = "https://{$this->tokenStore->getShop()}.myshopify.com/admin/oauth/authorize?";

        $payload = [
            'client_id' => $this->APICredentials->getKey(),
            'scope' => implode(",", $this->scopes),
            'redirect_uri' => $this->redirect_to
        ];

        return urldecode(sprintf("%s%s", $url, http_build_query($payload)));

    }

    function requestAccessToken(Request $request){

        HmacRequestValidator::validate($request, $this->APICredentials->getSecret());

        $url = "https://{$this->tokenStore->getShop()}.myshopify.com/admin/oauth/access_token";

        $payload = [
            'client_id' => $this->APICredentials->getKey(),
            'client_secret' => $this->APICredentials->getSecret(),
            'code' => $request->get('code')
        ];

        try {
            
            $resource = $this->client->post($url, [
                'form_params' => $payload
            ]);

        }catch( Exception $e  ){

            throw new Exceptions\InvalidAccessTokenRequest;

        }

        $response = json_decode($resource->getBody());

        $access_token = $response->access_token;

        $this->tokenStore->setToken($access_token);

        return $this->tokenStore->getToken();

    }

}