<?php

// require_once 'Crawler/classes/Searcher.php';

$host     = '127.0.0.1';
$port     = '9200';
$index    = 'jobs';
$type     = 'job';
// $searcher = new Searcher($host, $port);

$columnsNames = array(
    'website_name',
    'website_url',
    'job_name',
    'job_url',
    'job_type',
    'job_pay',
    'recovery_date',
    'publication_date',
    'company_name',
    'company_url',
    'required_skills',
    'city_name',
    'city_postal_code',
    'city_insee_code',
    'department_name',
    'department_code',
    'region_name',
    'region_code',
    'location',
);
$handle = fopen('utils/data/jobs.csv', 'r');

if ($handle !== FALSE) {
    $jobCounter = 0;

    while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
        $job = array();

        for ($field = 0; $field < count($row); $field++) {
            $columnName = $columnsNames[$field];

            switch($columnName) {
                case 'job_pay':
                    $job[$columnName] = (float)$row[$field];
                break;
                case 'recovery_date':
                    $job[$columnName] = DateTime::createFromFormat('d/m/Y', $row[$field])->format('Y-m-d');
                break;
                case 'publication_date':
                    $job[$columnName] = DateTime::createFromFormat('d/m/Y', $row[$field])->format('Y-m-d');
                break;
                default:
                    $job[$columnName] = $row[$field];
            }

            // echo $columnName . ' : ' . $row[$field] . ' du type : ' . gettype($row[$field]) . "\n";
        }
        
        $job['location'] = array(
            'lat' => 0,
            'lon' => 0
        );

        // echo print_r($job, TRUE); exit(0);

        $success = $searcher->indexElement($index, $type, $job);

        $jobCounter++;

        if ($jobCounter % 100 == 0) {
            echo $jobCounter . ' emplois importes' . "\n";
        }

        // echo $success;
    }

    fclose($handle);
}
else {
    echo 'Fichier utils/data/jobs.csv pas trouve ou pas accessible en lecture';
}

?>
