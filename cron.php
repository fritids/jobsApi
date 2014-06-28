<?php

require 'vendor/autoload.php';
// require 'Api/Websites/Alsacreations.php';
require 'Api/Websites/Remixjobs.php';

// use Api\Websites\Alsacreations;
use Api\Websites\Remixjobs;

// $alsacreations = new Alsacreations();
$remixjobs     = new Remixjobs();

// $alsacreations->crawl();
$remixjobs->crawl();

?>
