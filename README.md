Chill
======
Chill is a basic CouchDB REST client written in PHP that aims to provide an easy way to access CouchDB databases.


Basic Usage
===========
Chill requires PHP5+ and the Curl extension. If you have those, then it's easy as a pillow!

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


License & Copyright
===================
Chill is released under the MIT License. For more information, see the included LICENSE file.

Copyrighted (c) 2009 Mark Obcena.