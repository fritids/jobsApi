<?php

namespace Api;

require 'Api/Searcher.php';

class Grubber {
    public function sendJob($job) {
        $searcher = new Searcher('127.0.0.1', '9200');
        $index    = 'jobsapi';
        $type     = 'job';
        $id       = $job->generateId();

        echo print_r($job, TRUE); exit(0);

        return $searcher->indexElement($index, $type, $job, $id);
    }
}

?>
