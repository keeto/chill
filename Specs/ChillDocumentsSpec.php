<?php
require_once('SpecBase.php');
require_once('../Chill/Chill.php');

class DescribeChillDocuments extends SpecBase
{

	function beforeAll()
	{
		$this->setDescription("A Chill_Documents instance");
		$this->chill = new Chill("http://localhost:5984");
		$this->db = $this->chill->createDb("chill_test");
	}

	function afterAll()
	{
		$this->chill->deleteDb("chill_test");
		unset($this->db);
		unset($this->chill);
	}

	function itShouldLoadUsingAJsonString()
	{
		$data = '[
			{"_id":"chill1", "author": "mark", "type": "document"},
			{"_id":"chill2", "author": "keeto", "type": "document"}
		]';
		$docs = new Chill_Documents($this->db, $data);
		$this->spec(count($docs))->should->be(2);
	}

	function itShouldLoadUsingAnArrayOfArrays()
	{
		$data = array(
			array("_id" => "chill1", "author" => "mark", "type" => "document"),
			array("_id" => "chill2", "author" => "keeto", "type" => "document")
		);
		$docs = new Chill_Documents($this->db, $data);
		$this->spec(count($docs))->should->be(2);
	}

	function itShouldLoadUsingAnArrayOfJsonStrings()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);
		$this->spec(count($docs))->should->be(2);
	}

	function itShouldLoadUsingAnArrayOfMixedArraysAndJsonStrings()
	{
		$data = array(
			array("_id" => "chill1", "author" => "mark", "type" => "document"),
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);
		$this->spec(count($docs))->should->be(2);
	}

	function itShouldReturnAnArrayOfChillDocumentInstances()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$checkIfDocument = create_function('$doc',
			'return get_class($doc) == "Chill_Document";');
		$docsAreValid = array_map($checkIfDocument, $docs->toArray());

		$this->spec(in_array(false, $docsAreValid))->should->beFalse();
	}

	function itShouldReturnAChillDocumentInstanceInEachIterationOfAForEachLoop()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$valid = true;
		foreach ($docs as $doc) {
			if (get_class($doc) !== "Chill_Document") $valid = false;
		}

		$this->spec($valid)->should->beTrue();
	}

	function itShouldReturnAValidDocumentIfGivenAValidId()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$doc = $docs->getDocById('chill1');
		$this->spec($doc)->should->beAnInstanceOf('Chill_Document');
		$this->spec($doc->author)->should->be('mark');
	}

	function itShouldReturnAValidDocumentIfGivenAValidKey()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$doc = $docs->getDocByKey('author', 'mark');
	}

	function itShouldAddNewDocuments()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$docs->addDoc(new Chill_Document($this->db, '{"_id":"chill3", "author":"joe"}'));

		$this->spec(count($docs))->should->be(3);
	}

	function itShouldNotAddDuplicateDocs()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);
		$duplicateData = '{"_id":"chill1", "author": "mark", "type": "document"}';
		$duplicateDoc = new Chill_Document($this->db, $duplicateData);
		$docs->addDoc($duplicateDoc);

		$this->spec(count($docs))->should->be(2);
	}

	function itShouldReturnAnArrayOfDocumentKeyValues()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$authors = $docs->getValues("author");

		$this->spec($authors)->should->be(array("mark", "keeto"));
	}

	function itShouldSetTheValueOfAKeyInAllDocuments()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$docs->setValues('type', 'blog');
		$postTypes = $docs->getValues('type');

		$this->spec($postTypes)->should->be(array('blog', 'blog'));
	}

	function itShouldSaveAllDocumentsToTheDatabase()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$success = $docs->save();
		// $this->pending();
		// $this->spec($success)->should->be(array(true,true));
	}

	function itShouldMergeDocsFromAnotherInstanceIfPassedAsAConstructorArgument()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$mergeDocs = new Chill_Documents($this->db, $docs);
		$this->spec(count($mergeDocs))->should->be(2);
	}

	function itShouldMergeDocsFromAnotherInstanceIfPassedToAddDoc()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$mergeDocs = new Chill_Documents($this->db);
		$mergeDocs->addDoc($docs);

		$this->spec(count($mergeDocs))->should->be(2);
	}

	function itShouldMergeDocsFromAnotherInstanceIfPassedToAddDocs()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$mergeDocs = new Chill_Documents($this->db);
		$mergeDocs->addDocs($docs);

		$this->spec(count($mergeDocs))->should->be(2);
	}

	function itShouldNotMergeDuplicateDocsFromAnotherInstance()
	{
		$data = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$docs = new Chill_Documents($this->db, $data);

		$mergeData = array(
			'{"_id":"chill1", "author": "mark", "type": "document"}',
			'{"_id":"chill2", "author": "keeto", "type": "document"}'
		);
		$mergeDocs = new Chill_Documents($this->db, $mergeData);
		$mergeDocs->addDocs($docs);

		$this->spec(count($mergeDocs))->should->be(2);
	}

}