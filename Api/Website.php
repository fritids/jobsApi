<?php

// namespace Api;

abstract class Website {
    abstract protected function getPageDom();
    abstract protected function getJobDom();

    public function dataToJob($data) {
        
    }
}

?>
