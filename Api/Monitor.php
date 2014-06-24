<?php

namespace Api;

class Monitor implements \SplObserver {
    public function update(SplSubject $subject) {
        return error_log($subject->exception);
    }

    public function writeError($error) {
        // $error = array('nom', 'prenom', 'age');
        $csv = new SplFileObject('errors.csv', 'w');

        $csv->fputcsv($error, ',');
    }

    public function readError() {
        $csv = new SplFileObject('errors.csv', 'r');
        
        $csv->setFlags(SplFileObject::READ_CSV);

        foreach ($csv as $line) {
            echo 'valeur de la 1ere colonne : ' . $line[0];
        }
    }

    public function removeError() {
        
    }
}

?>
