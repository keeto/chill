<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

class Chill_Base
{
	public $host = "http://127.0.0.1:5984/";

	public function __construct()
	{
		if (!Chill::$Reader) Chill::$Reader = new Chill_Reader();
	}

	protected function get($uri, $opts = array(), $data = "")
	{
		return Chill::$Reader->request($this->host, "GET", $uri, $opts, $data);
	}

	protected function post($uri, $opts = array(), $data = "")
	{
		return Chill::$Reader->request($this->host, "POST", $uri, $opts, $data);
	}

	protected function put($uri, $opts = array(), $data = "")
	{
		return Chill::$Reader->request($this->host, "PUT", $uri, $opts, $data);
	}

	protected function delete($uri, $opts = array(), $data = "")
	{
		return Chill::$Reader->request($this->host, "DELETE", $uri, $opts, $data);
	}

}

class Chill_ConnectionException extends Exception
{

}