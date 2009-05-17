<?php
/**
 * Chill
 * A Basic PHP CouchDB REST Client
 * Copyright (c) 2009, Mark Obcena
 * Released Under The MIT License
 * See included README for more information.
 */

/**
 * @class Chill_Documents
 *
 * @author Mark Obcena
 */

class Chill_Documents extends Chill_Base implements Iterator, Countable
{

	private $_docs = array();

	# For Iterator
	private $currentDoc;
	private $currentIndex = 0;

	public function __construct($db, $docs = array())
	{
		$this->host = $db->host;
		$this->addDocs($docs);
		$this->rewind();
	}

	public function toArray()
	{
		$buff = array();
		foreach ($this->_docs as $doc) $buff[] = new Chill_Document($this->db, $doc);
		return $buff;
	}

	public function addDoc($doc)
	{
		if (get_class($doc) == 'Chill_Documents') {
			$this->addDocs($doc);
		} else {
			if (is_string($doc)) $doc = json_decode($doc, true);
			if ($this->doesntExist($doc)) $this->_docs[] = $doc;
		}
	}

	public function addDocs($docs = array())
	{
		if (!empty($docs)) {
			if (get_class($docs) == 'Chill_Documents') $docs = $docs->toArray();
			if (is_string($docs)) $docs = json_decode($docs, true);
			if (is_array($docs)) foreach ($docs as $doc) $this->addDoc($doc);
		}
	}

	private function doesntExist($doc)
	{
		if (get_class($doc) == 'Chill_Document')  $doc = $doc->toArray();
		$doc = json_encode($doc);
		$returnJson = create_function('$doc',
			'return json_encode((get_class($doc) == "Chill_Document") ? $doc->toArray() : $doc);');
		$current = array_map($returnJson, $this->_docs);
		return !in_array($doc, $current);
	}

	public function removeDoc($index)
	{
	    $this->getDoc($index);
      return true;
	}

	public function getDoc($index)
	{
		$doc = $this->_docs[$index];
		if ($doc) {
			if (is_array($doc)) $doc = new Chill_Document($this->db, $doc);
			return $doc;
		}
		return false;
	}

	public function getDocByKey($key, $value, $indexOnly = false)
	{
		$result = false;
		$resultIndex = false;
		foreach ($this->_docs as $index => $doc) {
			if (is_array($doc)) {
				if ($doc[$key] == $value) {
					$result = new Chill_Document($this->db, $doc);
					$resultIndex = $index;
				}
			} elseif (get_class($doc) == "Chill_Document") {
				switch ($key) {
					case '_id':
						if ($doc->getId() == $value) {
							$result = $doc;
							$resultIndex = $index;
						}
						break;
					case '_rev':
						if ($doc->getRev() == $value) {
							$result = $doc;
							$resultIndex = $index;
						}
						break;
					default:
						if ($doc->{$key} == $value) {
							$result = $doc;
							$resultIndex = $index;
						}
				}
			}
		}
		if ($result && $indexOnly) $result = $resultIndex;
		return $result;
	}

	public function getDocIndexByKey($key, $value)
	{
		return $this->getDocByKey($key, $value, true);
	}

	public function getDocById($id, $indexOnly = false)
	{
		return $this->getDocByKey("_id", $id, $indexOnly);
	}

	public function getDocIndexById($id)
	{
		return $this->getDocByKey("_id", $id, true);
	}

	public function getValues($key)
	{
		$values = array();
		foreach ($this->_docs as $doc) {
			if (is_array($doc)) {
				$val = $doc[$key];
			} elseif (get_class($doc) == "Chill_Document") {
				switch ($key) {
					case "_id":
						$val = $doc->getId();
						break;
					case "_rev":
						$val = $doc->getRev();
						break;
					default:
						$val = $doc->{$key};
				}
			}
			$values[] = $val;
		}
		return $values;
	}

	public function setValues($key, $value)
	{
		foreach ($this->_docs as $index => $doc) {
			if (is_array($doc)) {
				$this->_docs[$index][$key] = $value;
			} elseif (get_class($doc) == "Chill_Document") {
				$this->_docs[$index]->{$key} = $value;
			}
		}
		return $this;
	}

	public function save()
	{
		$buf = array();
		foreach ($this->_docs as $doc) {
			if (get_class($doc) == "Chill_Document") {
       $doc->save();
       $doc = $doc->toArray();
			 $buf[] = $doc;
      } else {}
		}
		return json_encode($buf);
	}

	# Iterator functions.

	public function rewind()
	{
		$this->next();
		$this->currentIndex = 0;
	}

	public function valid()
	{
		return $this->currentIndex < count($this->_docs);
	}

	public function next()
	{
		$current = $this->_docs[$this->currentIndex];
		if (get_class($current) !== "Chill_Document") $current = new Chill_Document($this->db, $current);
		$this->currentDoc = $current;
		++$this->currentIndex;
	}

	public function current()
	{
		return $this->currentDoc;
	}

	public function key()
	{
		return $this->currentIndex;
	}

	# Implement Countable

	public function count()
	{
		return count($this->_docs);
	}

}
