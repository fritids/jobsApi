<?php

require_once 'classes/Searcher.php';

$protocol = 'http';
$host     = '127.0.0.1';
$port     = '9200';
$index    = 'geographic';
$type     = 'city';
$searcher = new Searcher($protocol, $host, $port);

$input = $_POST['input'];

if (! empty ($input)) {
	$cities = $searcher->autocompleteCities($input);

	if ($cities['hits']['total'] > 0) {
		echo json_encode($cities['hits']['hits']);
	}
	else {
		echo NULL;
	}
}
else {
	echo NULL;
}

?>
