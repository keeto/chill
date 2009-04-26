Chill
=====
Chill is a simple CouchDB REST client written in PHP that aims to provide an easy way to access CouchDB databases.

Requirements
============

- CouchDB 0.9 and above
- PHP5+ with Curl extension.

(Note that Chill is tested with CouchDB 0.10 and PHP 5.2.6)

Basic Usage
===========

	include('Chill/Chill.php')
	$couchDbServer = "http://127.0.0.1:5984";

	# Create a new CouchDb Host.
	$chill = new Chill($couchDbServer);
	$db = $chill->openDb("my_database");

	# Create new documents using arrays..
	$arrayData = array(
		"_id" => "array_doc",
		"type" => "blog",
		"content" => "hello world!"
	);
	$docFromArray = $db->saveDoc($arrayData);

	# ..or using JSON string
	$jsonData = '{"_id": "json_doc", "type": "blog", "content": "hello world!"}';
	$docFromJson = $db->saveDoc($jsonData);

	# Open documents..
	$myDoc = $db->openDoc("json_doc");

	# ..then edit and save them.
	$myDoc->content = "hi universe!";
	$myDoc->save();

Issues & Updates
================
If you have any issues or if you run into problems, be sure to report them via the Issues tab at the official Chill Github repo: http://github.com/keeto/chill/issues/

You can also follow me on twitter for news and updates regarding Chill: http://twitter.com/keeto

License & Copyright
===================
Chill is released under the MIT License. For more information, see the included LICENSE file.

Copyrighted (c) 2009 Mark Obcena.