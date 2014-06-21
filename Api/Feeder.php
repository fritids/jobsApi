<?php

namespace Api;

require 'vendor/autoload.php';

class Searcher {
    public function __construct($host, $port) {
        $this->client = new \Elasticsearch\Client(array(
            'hosts' => array($host . ':' . $port)
        ));
    }

    public function sendJob($job) {
        $searcher = new Searcher('127.0.0.1', '9200');
        $index    = 'jobsapi';
        $type     = 'job';
        $id       = $job->generateId();

        // echo print_r($job, TRUE); exit(0);

        return $searcher->indexElement($index, $type, $job, $id);
    }

    public function indexElement($index, $type, $element, $id = NULL) {
        $data = array(
            'index' => $index,
            'type'  => $type,
            'body'  => $element,
        );

        if ($id != NULL) {
            $data['id'] = $id;
        }

        return $this->client->index($data);
    }
}

?>
