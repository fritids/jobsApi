<?php

namespace Monitoring;

require 'Api/Feeder.php';
require 'Api/Utils.php';

use Api\Feeder;
use Api\Utils;

class Monitor implements \SplObserver {
    public function __construct() {
        $this->feeder = new Feeder('localhost', 9200);
    }

    public function update(SplSubject $subject) {
        $this->writeError($subject->exceptions, $subject->id);
    }

    public function writeError($exceptions, $jobId) {
        $error = array(
            'exception' => array($exception),
            'jobId'     => $jobId,
            'date'      => date('d/m/Y'),
        );

        $status = $this->feeder->indexElement('jobsapi', 'error', $error);
    }

    public function readError() {

    }

    public function getAllErrors() {
        return $this->feeder->getAll('jobsapi', 'error');
    }

    public function deleteError() {

    }
}

?>
