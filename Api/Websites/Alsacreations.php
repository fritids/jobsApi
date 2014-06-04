<?php

// namespace Api;

require 'Api/Website.php';
require 'Api.Job.php';

// use Api\Website;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

// https://github.com/symfony/DomCrawler
// http://symfony.com/fr/doc/current/components/dom_crawler.html

class Alsacreations extends Website {
    public function __construct() {
        $this->website   = 'Alsacréations';
        $this->url       = 'http://emploi.alsacreations.com/';
        $this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
        $this->html      = $this->getPageDom($this->url);
        $this->crawler   = new Crawler($this->html);
   
        $this->websiteName     = NULL;
        $this->websiteUrl      = NULL;
        $this->jobTitle        = NULL;
        $this->jobUrl          = NULL;
        $this->jobType         = NULL;
        $this->jobPay          = NULL;
        $this->jobCityName     = NULL;
        $this->jobPostalCode   = NULL;
        $this->jobRegionName   = NULL;
        $this->jobGpsLocation  = NULL;
        $this->recoveryDate    = date('Y-m-d');
        $this->publicationDate = NULL;
        $this->companyName     = NULL;
        $this->companyUrl      = NULL;
        $this->requiredSkills  = array();
    }

    private function getPageDom() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_COOKIE, 'someCookie=2127;onlineSelection=C');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);

        $html = curl_exec($ch);

        curl_close($ch);

        return $html;
    }

    public function getJobDom() {

    }

    public function extractJobTitle() {
        return $this->crawler->filter('body table');
    }

    private function extractJobUrl() {

    }

    private function extractJobType() {

    }

    private function extractJobPay() {

    }

    private function extractCityName() {

    }

    private function extractPostalCode() {

    }

    private function extractRegionName() {

    }

    private function extractGpsLocation() {

    }

    private function extractCompanyName() {

    }

    private function extractCompanyUrl() {

    }

    private function extractRequiredSkills() {

    }

    public function sendJob($job) {
        
    }
}

?>
