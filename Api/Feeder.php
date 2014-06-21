<?php

namespace Api;

require 'vendor/autoload.php';

class Feeder {
    public function __construct($host, $port) {
        $this->client = new \Elasticsearch\Client(array(
            'hosts' => array($host . ':' . $port)
        ));
    }

    public function indexElement($index, $type, $element, $id = NULL) {
        echo print_r($element, TRUE); exit(0);

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

    public function searchForNormalize($index, $type, $field, $data) {
        $query = array(
            'index' => $index,
            'type'  => $type,
            'body'  => array(
                'query' => array(
                    'match_all' => array()
                ),
                'filter' => array(
                    'term' => array(
                        $field => 'paris'
                    )
                )
            )
        );

        $results = $this->client->search($query);

        if (empty($results) === FALSE) {
            return $results[0];
        }

        return NULL;
    }
}

?>
