<?php
require_once('SpecBase.php');
require_once('../Chill/Chill.php');

class DescribeChill extends SpecBase
{
	function beforeAll()
	{
		$this->setDescription("A new Chill instance");
		$this->correctHost = "http://localhost:5984";
		$this->wrongHost = "http://localhost:598";
	}

	function afterAll()
	{
		unset($this->chill);
	}

	function itShouldThrowAnExceptionIfHostIsWrong()
	{
		try {
			new Chill($this->wrongHost);
		} catch (ConnectionException $e) {
			$this->spec($e)->should->beAnInstanceOf('Chill_ConnectionException');
		}
	}

	function itShouldReturnAChillObjectIfIsConnectsOkay()
	{
		$this->chill = new Chill($this->correctHost);
		$this->spec($this->chill)->should->beAnInstanceOf('Chill');
	}

	function itShouldReturnAllAvailableDbsAsAnArray()
	{
		$dbs = $this->chill->listDbs();
		$this->spec(is_array($dbs))->should->beTrue();
	}

	function itShouldCreateANewDb()
	{
		$db = $this->chill->createDb('chill_test');
		$this->spec($db)->should->beAnInstanceOf('Chill_Database');
	}

	function itShouldNotRecreateAnExistingDb()
	{
		$db = $this->chill->createDb('chill_test');
		$this->spec($db)->shouldNot->beTrue();
	}

	function itShouldOpenAnExistingDb()
	{
		$db = $this->chill->openDb("chill_test");
		$this->spec($db)->shouldNot->beFalse();
	}

	function itShouldNotOpenAMissingDb()
	{
		$db = $this->chill->openDb("chillxtest");
		$this->spec($db)->should->beFalse();
	}

	function itShouldNotCreateAnIllegallyNamedDb()
	{
		$db = $this->chill->createDb('_chill_test_');
		$this->spec($db)->shouldNot->beTrue();
	}

	function itShouldDeleteAnExistingDb()
	{
		$success = $this->chill->deleteDb('chill_test');
		$this->spec($success)->should->beTrue();
	}

	function itShouldNotDeleteAMissingDb()
	{
		$success = $this->chill->deleteDb('chill_test');
		$this->spec($success)->shouldNot->beTrue();
	}
}