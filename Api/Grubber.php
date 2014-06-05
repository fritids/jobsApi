<?php

// namespace Api;

require 'Api/Searcher.php';

class Grubber {
    private function sendJob($job) {
        $searcher = new Searcher();
        $index    = 'jobs';
        $type     = 'job';

        // return $searcher->indexElement($index, $type, $job);
    }
}

?>
