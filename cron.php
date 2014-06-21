<?php

require 'vendor/autoload.php';
require 'Api/Websites/Alsacreations.php';

use Api\Websites\Alsacreations;

$alsacreations = new Alsacreations();

$alsacreations->crawl();

?>
