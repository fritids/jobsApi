<?php

namespace Api;

abstract class Website {
    abstract protected function getPageDom($url);
    abstract protected function sendjob($job);
}

?>
