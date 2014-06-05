<?php

require 'vendor/autoload.php';

require 'Api/Grubber.php';
require 'Api/Websites/Alsacreations.php';

// use Api\Grubber;
// use Api\Websites\Alsacreations;

$grubber       = new Grubber();
$alsacreations = new Alsacreations();

$alsacreations->crawl();

?>
