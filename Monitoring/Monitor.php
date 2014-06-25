<?php

namespace Monitoring;

class Monitor implements \SplObserver {
    public function update(SplSubject $subject) {
        return error_log($subject->exception);
    }
}

?>
