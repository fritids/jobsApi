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
        return error_log($subject->exception);
    }

    public function writeError() {
        $status = $this->feeder->indexElement('jobsapi', 'errors', $this->data, $this->id);
    }

    public function readError() {

    }

    public function getAllErrors() {

    }

    public function deleteError() {

    }
}

?>
