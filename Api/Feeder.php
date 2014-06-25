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
        // echo print_r($element, TRUE); // exit(0);

        /*foreach ($element as $e) {
            echo gettype($e) . ' => ' . print_r($e, TRUE) . "\n";
        }*/

        /*$element = array(
            'index' => 'jobsapi',
            'type'  => 'job',
            'body'  => array(
                'websiteName' => 'Alsacréations',
                'websiteUrl'  => 'http://emploi.alsacreations.com',
                'jobTitle'    => 'Intégrateur-développeur web mobile',
                'jobUrl'      => 'http://emploi.alsacreations.com/offre-554465-Integrateur-developpeur-web-mobile.html',
                'companyName' => 'L2lconsulting',
                'companyUrl'  => '',
                'publicationDate' => '25/06/2014',
                'recoveryDate'    => '25/06/2014',
                'jobCityName'     => 'Bouy-Luxembourg',
                'jobPostalCode'   => '10220',
                'jobType'         => 'CDI',
                'jobRegionName'   => '',
                'requiredSkills'  => array(),
                'jobLocation'     => array(
                    0 => 48.3833,
                    1 => 4.26667
                )
            ),
            'id' => 'b699ea876f57f168e7552289886b16a0'
        );*/

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
