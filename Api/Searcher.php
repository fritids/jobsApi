<?php

namespace Api;

require 'vendor/autoload.php';

class Searcher {
    public function __construct($host, $port) {
        $this->client = new \Elasticsearch\Client(array(
            'hosts' => array($host . ':' . $port)
        ));
    }

    public function indexElement($index, $type, $element) {
        return $this->client->index(array(
            'index' => $index,
            'type'  => $type,
            'body'  => $element,
        ));
    }
}

?>
