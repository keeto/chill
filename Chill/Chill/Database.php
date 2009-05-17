<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

/**
 * @class Chill_Database
 *
 * @author Mark Obcena
 */

class Chill_Database extends Chill_Base
{
	public $name;

	public function __construct($chill, $name)
	{
		$this->name = trim($name, '/') . "/";
		$this->host = $chill->host.$this->name;
		$this->_handshake();
	}

	private function _handshake()
	{
		$success = $this->get("");
		if (isset($success->body->error)) {
			throw new Chill_ConnectionException("CouchDB Database Error: " . $success->body->reason);
		} elseif ($success == false) {
			throw new Chill_ConnectionException("Could not connect to Database:". $this->name);
		}
	}
  public function getDocumentCollection($filter=array(),$count=0) {
    $r = $this->getAllDocs(array('include_docs'=>true),true);
    $coll = array();
    
    $count = (int) $count;
    $rows = (int) count($r->rows);
    //var_dump($count,$rows);
    $num = ($count > 0)?$count:$rows;
    if($rows < $num){ $num = $rows; }
    for($idx=0;$idx< $num; $idx++) { 
      if(count($filter) > 0){
      //var_dump($key,$value);
      $filt = explode(':',$filter[0]);
      $key = $filt[0];
      $value = $filt[1];
      $tmp = $r->rows[$idx]->doc->toArray();
      if(array_key_exists($key,$tmp)) { array_push($coll,$r->rows[$idx]->doc); }
      } else {
      array_push($coll,$r->rows[$idx]->doc);
      }
    }
    return new Chill_Documents($this, $coll);
  }
	public function getAllDocs($opts = array(), $asDocs = false)
	{
		$response = $this->get("_all_docs", $opts);
		if ($response) {
			if ($asDocs && (is_array($opts) && $opts["include_docs"] === true)) {
				foreach ($response->body->rows as $index => $row) {
					$doc = new Chill_Document($this, $response->body->rows[$index]->doc);
					$response->body->rows[$index]->doc = $doc;
				}
			}
			return $response->body;
		}
		return false;
	}

	public function getAllDesignDocs($opts = array(), $asDocs = false)
	{
		$opts = array_merge($opts, array(
			"startkey"=>'"_design"',
			"endkey" => '"_design0"'
		));
		return $this->getAllDocs($opts, $asDocs);
	}

	public function query($mapfn, $reducefn = "", $opts = array())
	{
		$data = array(
			"language" => "javascript",
			"map" => $mapfn
		);
		if (!empty($reducefn)) $data["reduce"] = $reducefn;
		$response = $this->post("_temp_view", $opts, json_encode($data));
		return ($response) ? $response->body : false;
	}

	public function openDoc($id)
	{
		$response = $this->get($id);
		return ($response->headers->{"Status-Code"} == "200") ? new Chill_Document($this, $response->body, false) : false;
	}

	public function deleteDoc($id, $rev)
	{
		$response = $this->delete($id, array("rev"=>$rev));
		return ($response->body->ok) ? true : false;
	}

	public function saveDoc($doc, $opts = array())
	{
		if (is_string($doc)) $doc = json_decode($doc, true);
		if (!isset($doc["_id"])) $doc["_id"] = Chill::getUuid();
		$response = $this->put($doc["_id"], $opts, json_encode($doc));
		if ($response->body->ok) {
			$doc["_rev"] = $response->body->rev;
			$data = new Chill_Document($this, $doc, false);
		} else {
			$data = false;
		}
		return $data;
	}
}
