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
        $params = array(
            'index' => $index,
            'type'  => $type,
            'body'  => $element,
        );

        if ($id != NULL) {
            $params['id'] = $id;
        }

        return $this->client->index($params);
    }

    public function searchCityData($cityName) {
        $query = array(
            'index' => 'geographic',
            'type'  => 'city',
            'body'  => array(
                'query' => array(
                    'match_all' => array()
                ),
                'filter' => array(
                    'term' => array(
                        'city_name' => $cityName
                    )
                ),
                'sort' => array(
                    'city_population' => array(
                        'order' => 'desc'
                    )
                )
            )
        );

        $results = $this->client->search($query);

        if (empty($results['hits']['hits']) === FALSE) {
            return $results['hits']['hits'][0];
        }

        return NULL;
    }

    public function searchExistingCityId($id) {
        $query = array(
            'index' => 'jobsapi',
            'type'  => 'city',
            'body'  => array(
                'query' => array(
                    'match' => array(
                        '_id' => $id
                    )
                )
            )
        );

        $results = $this->client->search($query);

        if ($results['hits']['total'] == 0) {
            return TRUE;
        }

        return FALSE;
    }
}

?>
