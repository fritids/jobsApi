<?php

require 'vendor/autoload.php';

class Searcher {
	public function __construct($host, $port) {
		$this->client = new Elasticsearch\Client(array(
			'hosts' => array($host . ':' . $port)
		));
	}

	public function indexElement($index, $type, $element) {
		$params = array(
			'index' => $index,
			'type'  => $type,
			'body'  => $element,
		);

		$res = $this->client->index($params);

		return $res;
	}
}

?>
