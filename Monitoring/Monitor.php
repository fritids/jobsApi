<?php

namespace Monitoring;

// http://devzone.zend.com/1732/implementing-the-observer-pattern-with-splobserver-and-splsubject/

class Monitor implements \SplObserver {
    public function __construct($feeder) {
        $this->feeder = $feeder;
    }

    public function update(\SplSubject $subject) {
        $this->writeError($subject->exceptions, $subject->id, $subject->data['websiteName']);
    }

    public function writeError($exceptions, $jobId, $websiteName) {
        $error = array(
            'exception'   => array($exceptions),
            'jobId'       => $jobId,
            'websiteName' => $websiteName,
            'date'        => date('d/m/Y'),
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
