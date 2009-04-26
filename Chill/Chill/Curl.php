<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

/**
 * @class Chill_Curl
 *
 * @author Mark Obcena
 */

class Chill_Curl
{
	public $headers = array();
	public $error = "";

	public function __init()
	{
		return $this;
	}

	public function setHeader($key, $value)
	{
		$this->headers[$key] = $value;
		return $this;
	}

	public function setHeaders($headers = array())
	{
		foreach ($headers as $key => $value) {
			$this->setHeader($key, $value);
		}
		return $this;
	}

	public function request($method, $url, $data = array())
	{
		$handle = curl_init();

		// Common curl options..
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

		// Set custom headers..
		$headers = array();
		if (!empty($data) && ($method == "POST" || $method == "PUT")) {
			$headers['Content-Length'] = strlen($data);
		}
		foreach ($this->headers as $key => $value) {
			$headers[] = $key . ': ' . $value;
		};
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

		// Execute then parse..
		$response = curl_exec($handle);
		if ($response) {
			$response = new Chill_CurlResponse($response);
		} else {
			$this->error = curl_errno($handle).': '.curl_error($handle);
		}
		curl_close($handle);
		return $response;
	}

	public function get($url, $data = array())
	{
		return $this->request("GET", $url, $data);
	}

	public function post($url, $data = array())
	{
		return $this->request("POST", $url, $data);
	}

	public function put($url, $data = array())
	{
		return $this->request("PUT", $url, $data);
	}

	public function delete($url, $data = array())
	{
		return $this->request("DELETE", $url, $data);
	}

}

class Chill_CurlResponse
{

	public $body = "";
	public $headers = array();

	public function __construct($response)
	{
		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
		preg_match_all($pattern, $response, $matches);
		$headers = split("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));

		$version_and_status = array_shift($headers);
		preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
		$this->headers['Http-Version'] = $matches[1];
		$this->headers['Status-Code'] = $matches[2];
		$this->headers['Status'] = $matches[2].' '.$matches[3];

		foreach ($headers as $header) {
			preg_match('#(.*?)\:\s(.*)#', $header, $matches);
			$this->headers[$matches[1]] = $matches[2];
		}

		$this->body = preg_replace($pattern, '', $response);
	}

	public function __toString()
	{
		return $this->body;
	}
}
