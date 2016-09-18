<?php

namespace Kevdotbadger\Shopify;

use GuzzleHttp\Client as Guzzle;
use Session;
use Config;

use Kevdotbadger\Shopify\Exceptions\ShopifyAuthException;
use Kevdotbadger\Shopify\Exceptions\ShopifyConfigException;

class Shopify {
	
	private $api_key;
	private $secret;
	private $password;
		
	function __construct() {
		
		$this->setApiKey(Config::get('shopify.auth.api_key', function(){
			throw new ShopifyConfigException("Api Key not set in config.");
		}));
			
		$this->setSecret(Config::get('shopify.auth.secret', function(){
			throw new ShopifyConfigException("Secret Key not set in config.");
		}));
		
		if( Config::has('shopify.auth.password') ){
			$this->setPassword(Config::get('shopify.auth.password'));
		}
	
	}
	
	/**
	 * Set the API password, setting this will force the API to use the private method (non-oAuth)
	 *
	 * @param string $password 
	 * @return Shopify
	 * @author Kevin Ruscoe
	 */
	function setPassword($password) {
	
		$this->password = $password;
		 
		return $this;
		
	}
	
	/**
	 * Get the API password
	 *
	 * @return string
	 * @author Kevin Ruscoe
	 */
	function getPassword() {
		return $this->password;
	}
	
	/**
	 * Returns if the API should use the private API call, or oAuth
	 *
	 * @return bool
	 * @author Kevin Ruscoe
	 */
	public function isPrivate() {
		return isset($this->password);
	}
	
	/**
	 * Set the application's API Key
	 *
	 * @param string $api_key 
	 * @return Shopify
	 * @author Kevin Ruscoe
	 */
	function setApiKey($api_key) {
	
		$this->api_key = $api_key;
		 
		return $this;
		
	}
	
	/**
	 * Get the application's API Key
	 *
	 * @return string
	 * @author Kevin Ruscoe
	 */
	function getApiKey() {
		return $this->api_key;
	}
	
	/**
	 * Set the application's Secret
	 *
	 * @param string $secret 
	 * @return Shopify
	 * @author Kevin Ruscoe
	 */
	function setSecret($secret) {
	
		$this->secret = $secret;
		 
		return $this;
		
	}
	
	/**
	 * Get the application's Secret
	 *
	 * @return string
	 * @author Kevin Ruscoe
	 */
	function getSecret() {
		return $this->secret;
	}
	
	/**
	 * Set the application's Shop name
	 *
	 * @param string $shop 
	 * @return Shopify
	 * @author Kevin Ruscoe
	 */
	function setShop($shop) {
		
		Session::set('shopify.shop', $shop);
		 
		return $this;
		
	}
	
	/**
	 * Get the application's Shop name
	 *
	 * @return mixed
	 * @author Kevin Ruscoe
	 */
	function getShop() {
		return Session::get('shopify.shop', function(){
			throw new ShopifyAuthException('A shop has not been specified');
		});
	}
	
	/**
	 * Get the shop name from the full URI
	 *
	 * @param string $input 
	 * @return string
	 * @author Kevin Ruscoe
	 */
	public static function uriToShopName($input)
	{
		
		$input = str_replace(['http', 'https', '://'], '', $input);
		
		$regex = "/(.+).myshopify.com/"; 
 
		if( !preg_match($regex, $input, $matches) ){
			return $input;
		}
				
		return $matches[1];
		
	}
	
	/**
	 * Turn a shop name into a shop URI
	 *
	 * @param string $input 
	 * @return string
	 * @author Kevin Ruscoe
	 */
	public static function shopNameToUri($shop_name)
	{
		return sprintf("%s.myshopify.com", $shop_name);
	}
	
	/**
	 * Set the token to make API requests with
	 *
	 * @param string $token 
	 * @return Shopify
	 * @author Kevin Ruscoe
	 */
	function setToken($token) {
	
		Session::set('shopify.token', $token);
		 
		return $this;
		
	}
	
	/**
	 * Get the token to make API requests with, if a $code is passed it will request a token from Shopify's Auth server
	 *
	 * @param string $code 
	 * @return mixed
	 * @author Kevin Ruscoe
	 */
	function getToken() {
		
		return Session::get('shopify.token', function(){
			throw new ShopifyAuthException('A token does not exist.');
		});
		
	}
	
	/**
	 * Turn a temporary code into a useable token from Shopify
	 *
	 * @param string $code 
	 * @return string
	 * @author Kevin Ruscoe
	 */
	public function requestToken($code)
	{
		
		try {
			
			$client = (new Guzzle)->post(
				sprintf("https://%s.myshopify.com/admin/oauth/access_token", $this->getShop()),
				[
					'form_params' => ([
						'client_secret' => $this->getSecret(),
						'client_id'     => $this->getApiKey(),
						'code'          => $code
					])
				]
			);
			
		}catch(\Exception $e){
			throw new ShopifyAuthException(sprintf("Couldn't get access token (%s)", $e->getMessage()));
		}
		
		$body = json_decode($client->getBody());
		
		$token = $body->access_token;
		
		$this->setToken($token);
		
		return $this->getToken();
				
	}
	
	/**
	 * Create an installer URL to instal lthis application
	 *
	 * @param array  $scopes 
	 * @param string $redirect_uri
	 * @return string
	 * @author Kevin Ruscoe
	 */
	public function getInstallUrl($scopes, $redirect_uri = null)
	{
		
		$shop = self::uriToShopName($this->getShop());
		$api_key = $this->getApiKey();
		
		$url = sprintf(
			"https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s",
			$shop, $api_key, implode($scopes, ",")
		);
		
		if( !is_null($redirect_uri) ){
			$url .= sprintf("&redirect_uri=%s", $redirect_uri);
		}
		
		return $url;
		
	}
	
	/**
	 * Return array of default headers
	 *
	 * @return array
	 * @author Kevin Ruscoe
	 */
	private function getHeaders()
	{
		return [
			'X-Shopify-Access-Token' => $this->getToken(),
			'Content-Type'           => 'application/json'
		];
	}
	
	/**
	 * Return a fully resolved path to the API call
	 *
	 * @param string $url 
	 * @param string $options 
	 * @return string
	 * @author Kevin Ruscoe
	 */
	private function getFullyResolvedPath($url, $options = null)
	{
		
		$querystring = (!is_null($options)) ? http_build_query($options) : '';
		
		if( $this->isPrivate() ){
			return sprintf("https://%s:%s@%s/admin/%s?%s",
				$this->getApiKey(), $this->getPassword(), self::shopNameToUri($this->getShop()), $url, $querystring
			);
		}
		
		return sprintf("https://%s/admin/%s?%s",
			self::shopNameToUri($this->getShop()), $url, $querystring
		);
		
	}
	
	/**
	 * Perform a GET request to a URI
	 *
	 * @param string $url 
	 * @param array $options 
	 * @return Object
	 * @author Kevin Ruscoe
	 */
	public function get($uri, $options = null)
	{
		
		$url = $this->getFullyResolvedPath($uri, $options);
				
		$client = (new Guzzle)->get($url, [
			'headers' => $this->getHeaders()
		]);

		return json_decode((string)$client->getBody());
		
	}
	
	/**
	 * Perform a POST request to a URI
	 *
	 * @param string $uri 
	 * @param string $body 
	 * @param array $options 
	 * @return Object
	 * @author Kevin Ruscoe
	 */
	public function post($uri, $body = null, $options = null)
	{
				
		$url = $this->getFullyResolvedPath($uri, $options);
								
		$client = (new Guzzle)->post($url, [
			'headers' => $this->getHeaders(),
			'body'  => json_encode($body)
		]);

		return json_decode((string)$client->getBody());
		
	}
	
	/**
	 * Perform a PUT request to a uri
	 *
	 * @param string $uri 
	 * @param string $body 
	 * @param array $options 
	 * @return Object
	 * @author Kevin Ruscoe
	 */
	public function put($uri, $body = null, $options = null)
	{
				
		$url = $this->getFullyResolvedPath($uri, $options);
								
		$client = (new Guzzle)->put($url, [
			'headers' => $this->getHeaders(),
			'body'  => json_encode($body)
		]);

		return json_decode((string)$client->getBody());
		
	}
	
	/**
	 * Perform a DELETE request to a uri
	 *
	 * @param string $uri 
	 * @return void
	 * @author Kevin Ruscoe
	 */
	public function delete($uri)
	{
				
		$url = $this->getFullyResolvedPath($uri);
								
		$client = (new Guzzle)->delete($url, [
			'headers' => $this->getHeaders(),
		]);

		return json_decode((string)$client->getBody());
		
	}
	
}

?>