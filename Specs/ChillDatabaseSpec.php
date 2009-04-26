<?php
require_once('SpecBase.php');
require_once('../Chill/Chill.php');

class DescribeChillDatabase extends SpecBase
{
	function beforeAll()
	{
		$this->setDescription("A Chill_Database");
		$this->chill = new Chill("http://localhost:5984");
		$this->chill->createDb("chill_test");
	}

	function afterAll()
	{
		$this->chill->deleteDb("chill_test");
		unset($this->db);
		unset($this->chill);
	}

	function itShouldConnectProperly()
	{
		$this->db = new Chill_Database($this->chill, "chill_test");
		$this->spec($this->db)->should->beAnInstanceOf('Chill_Database');
	}

	function itShouldSaveDocumentsFromJsonStrings()
	{
		$data = '{"_id":"chill_doc1", "name":"test"}';
		$doc = $this->db->saveDoc($data);
		$this->spec($doc)->should->beAnInstanceOf('Chill_Document');
	}

	function itShouldSaveDocumentsFromArray()
	{
		$data = array("_id" => "chill_doc2", "name" => "another test");
		$doc = $this->db->saveDoc($data);
		$this->spec($doc)->should->beAnInstanceOf('Chill_Document');
	}

	function itShouldReturnAnArrayOfAllDocuments()
	{
		$docs = $this->db->getAllDocs();
		$this->spec(count($docs->rows))->should->be(2);
	}

	function itShouldReturnAnArrayOfAllDocumentsGivenOptions()
	{
		$options = array("descending" => "true", "key" => "chill_doc2", "include_docs" => true);
		$docs = $this->db->getAllDocs($options);
		$this->spec(count($docs->rows))->should->be(1);
	}

	function itShouldTurnDocsIntoChillDocumentsIfIncludeDocsAndTurnDocsIntoObjectsIsTrue()
	{
		$turnDocsIntoObjects = true;
		$options = array("include_docs" => true);
		$docs = $this->db->getAllDocs($options, $turnDocsIntoObjects);
		$this->spec($docs->rows[0]->doc)->should->beAnInstanceOf('Chill_Document');
	}

	function itShouldReturnProperResultsUsingTemporaryViews()
	{
		$mapfn = 'function(doc){ emit(doc._id, doc); }';
		$docs = $this->db->query($mapfn);
		$this->spec(count($docs->rows))->should->be(2);
	}

	function itShouldReturnProperResultsUsingTemporaryViewsWithOptions()
	{
		$mapfn = 'function(doc){ emit(doc._id, doc); }';
		$options = array("key" => "chill_doc2");
		$docs = $this->db->query($mapfn, "", $options);
		$this->spec($docs->rows[0]->id)->should->be("chill_doc2");
		$this->spec(count($docs->rows))->should->be(1);
	}

	function itShouldReturnAnOpenedDocumentAsAChillDocument()
	{
		$doc = $this->db->openDoc("chill_doc1");
		$this->spec($doc)->should->beAnInstanceOf('Chill_Document');
	}

	function itShouldNotOpenNonExistingDocuments()
	{
		$doc = $this->db->openDoc("chill_missing");
		$this->spec($doc)->should->beFalse();
	}

	function itShouldDeleteExistingDocuments()
	{
		$doc = $this->db->openDoc("chill_doc1");
		$docId = $doc->getId();
		$docRev = $doc->getRev();
		$doc = $this->db->deleteDoc($docId, $docRev);
		$this->spec($doc)->should->beTrue();
	}
}