<?php
require_once('SpecBase.php');
require_once('../Chill/Chill.php');

class DescribeChillDocument extends SpecBase
{

	function helperCreateDoc()
	{
		$this->doc = $this->db->saveDoc('{"_id":"chill_doc1", "author":"chill", "type":"test"}');
	}

	function helperRefreshDoc()
	{
		$this->db->deleteDoc($this->doc->getId(), $this->doc->getRev());
		$this->helperCreateDoc();
	}

	function beforeAll()
	{
		$this->setDescription("A Chill Document instance");
		$this->chill = new Chill("http://localhost:5984");
		$this->db = $this->chill->createDb("chill_test");
		$this->helperCreateDoc();
	}

	function afterAll()
	{
		$this->chill->deleteDb("chill_test");
		unset($this->db);
		unset($this->chill);
	}

	function itShouldReturnACorrectId()
	{
		$id = $this->doc->getId();
		$this->spec($id)->should->be('chill_doc1');
	}

	function itShouldReturnAProperJsonString()
	{
		$json = $this->doc->toJson();
		$this->spec(json_decode($json))->should->beAnInstanceOf('stdClass');
	}

	function itShouldReturnAProperArray()
	{
		$array = $this->doc->toArray();
		$this->spec($array["_id"])->should->be("chill_doc1");
	}

	function itShouldSaveChangesToDocument()
	{
		$originalRev = $this->doc->getRev();

		$this->doc->author = "chilly";
		$success = $this->doc->save();
		$newRev = $this->doc->getRev();

		$loadedDoc = $this->db->openDoc("chill_doc1");
		$loadedRev = $loadedDoc->getRev();

		$this->spec($newRev)->shouldNot->be($originalRev);
		$this->spec($newRev)->should->beEqual($loadedRev);

		$this->helperRefreshDoc();
	}

	function itShouldNotSaveUnchangedDocuments()
	{
		$saved = $this->doc->save();
		$this->spec($saved)->shouldNot->beTrue();
	}

	function itShouldDeleteItself()
	{
		$data = '{"_id":"chill_doc", "author":"chill", "type":"test"}';
		$doc = new Chill_Document($this->db, $data);
		$doc->save();
		$this->spec($doc->delete())->should->beTrue();
	}
}