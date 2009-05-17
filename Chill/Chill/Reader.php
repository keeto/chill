<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

require_once("Curl.php");

/**
 * @class Couch_Reader
 *
 * @author Mark Obcena
 */

class Chill_Reader {
	private $host;
	private $port;

	public function __construct()
	{
		$this->curl = new Chill_Curl();
		$this->curl->setHeaders(array(
			"Accept" => "application/json, text/javascript, */*",
			"User-Agent" => "Chill/$this->version (PHP Relax)",
			"Content-Type" => "application/json"
		));
	}

	public function request($url, $method, $sub, $opts = array(), $data = "")
	{
		if (!empty($opts) && is_array($opts)) $sub = Chill_Reader::buildURL($sub, $opts);
		$response = $this->curl->{strtolower($method)}($url . $sub, $data);
		if ($response) {
			$response->body = json_decode($response->body);
			$response->headers = json_decode(json_encode($response->headers));
			return $response;
		}
		return false;
	}

	public function get($base, $uri, $opts = array(), $data = "")
	{
    //var_dump($base,$uri,$opts,$data);
		return $this->request($base, "GET", $uri, $opts, $data);
	}

	public function post($base, $uri, $opts = array(), $data = "")
	{
		return $this->request($base, "POST", $uri, $opts, $data);
	}

	public function put($base, $uri, $opts = array(), $data = "")
	{
		return $this->request($base, "PUT", $uri, $opts, $data);
	}

	public function delete($base, $uri, $opts = array(), $data = "")
	{
		return $this->request($base, "DELETE", $uri, $opts, $data);
	}

	private function buildURL($uri, $opts = array(), $encodeURI = true)
	{
		$buf = array();
		$uri = ($encodeURI) ? urlencode(rtrim($uri,"/")) : rtrim($uri,"/");
		foreach ($opts as $key => $value) {
			$jsonify = ($key == "key" || $key == "startkey" || $key == "endkey");
			$value = ($jsonify || $value === true) ? json_encode($value) : $value;
			$buf[] = urlencode($key)."=".urlencode($value);
		}
		if (!empty($buf)) $uri .= "?".implode("&", $buf);
		return $uri;
	}
}
