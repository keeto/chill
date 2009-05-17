<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

/**
 * @class Chill_Document
 *
 * @author Mark Obcena
 */

class Chill_Document extends Chill_Base
{
	protected $db;

	protected $_id;
	protected $_rev;
	protected $_data;

	protected $is_new = false;
	protected $has_changed = false;

	public function __construct($db, $data = array(), $new = ""){
		$this->db = $db;
		$this->host = $db->host;

		if (!empty($data)) {
			if (is_string($data)) $data = json_decode($data, true);
			foreach ($data as $key => $value) {
				switch ($key) {
					case "_id":
					case "id":
						$this->_id = $value;
						break;
					case "_rev":
					case "rev":
						$this->_rev = $value;
						break;
					default:
						$this->_data[$key] = $value;
				}
			}
		}

		$this->is_new = (!is_string($new) || is_bool($new)) ? $new : $this->checkIfExists();

	}

	protected function p_delete($uri, $opts = array(), $data = ""){
		parent::delete();
	}

	private function checkIfExists()
	{
		if ($this->_id) {
			$response = $this->get($this->_id);
			if ($response->headers->{"Status-Code"} == "200") return false;
		}
		return true;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setId($id)
	{
		$this->has_changed = true;
		$this->_id = $id;
		return $this;
	}

	public function getRev()
	{
		return $this->_rev;
	}

	public function __get($key)
	{
		return $this->_data[$key];
	}

	public function __set($key, $value)
	{
		switch ($key) {
			case "_id":
				$this->setId($value);
				break;
			case "_rev":
				break;
			default:
				$this->has_changed = true;
				$this->_data[$key] = $value;
		}
	}

	public function save($opts = array(), $force = false)
	{
		if ($this->has_changed || $this->is_new || $force) {
			$new = $this->db->saveDoc($this->toArray(), $opts);
			if ($new) {
				$this->_rev = $new->getRev();
				$this->has_changed = false;
				$this->is_new = false;
				return $this->_rev;
			}
		}
		return false;
	}

	public function delete()
	{
		if (!$this->is_new) {
			$deleted = $this->db->deleteDoc($this->_id, $this->_rev);
			if ($deleted) {
				$this->_rev = null;
				$this->has_changed = true;
				$this->is_new = true;
				return true;
			}
		}
		return false;
	}

	public function isNew()
	{
		return $this->is_new;
	}

	public function __toString()
	{
		return $this->toJson();
	}

	public function toJson()
	{
		return json_encode($this->toArray());
	}

	public function toArray()
	{
		$data = array();
		if (isset($this->_id)) $data["_id"] = $this->_id;
		if (isset($this->_rev)) $data["_rev"] = $this->_rev;
    if(!empty($this->_data)) {
		foreach ($this->_data as $key => $value) {
			$data[$key] = $value;
		}
    }
		return $data;
	}

}
