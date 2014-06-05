<?php

// namespace Api;

require 'Api/Website.php';
require 'Api/Job.php';

// use Api\Website;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

// https://github.com/symfony/DomCrawler
// http://symfony.com/fr/doc/current/components/dom_crawler.html
// http://symfony.com/fr/doc/current/components/css_selector.html

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
        $this->jobLocation     = NULL;
        $this->recoveryDate    = date('Y-m-d');
        $this->publicationDate = NULL;
        $this->companyName     = NULL;
        $this->companyUrl      = NULL;
        $this->requiredSkills  = array();
    }

    public function crawl() {
        $jobsTable = $this->extractJobTable();

        if ($jobsTable != FALSE) {
            $jobRow = $jobsTable->filter('tr'); error_log(print_r($jobRow, TRUE));

            foreach ($jobRow as $row) { echo print_r($row, TRUE);
                $columns = $row->filter('td');
                $link    = $columns->filter('td')->first();
                $span    = $columns->filter('span')->eq(1);
                $b       = $columns->filter('b')->first();
                $data    = array();

                $data['websiteName']     = $this->website;
                $data['websiteUrl']      = $this->url;
                $data['jobTitle']        = $link->text();
                $data['jobUrl']          = $this->url . $link->attr('href');
                $data['jobType']         = $span->text();
                // $data['jobPay']          = $this->extractJobPay();
                $data['jobCityName']     = $columns->eq1(1)->text();
                // $data['jobPostalCode']   = $this->extractJobPostalCode();
                $data['jobRegionName']   = $columns->eq(1)->text();
                // $data['jobLocation']     = $this->extractJobLocation();
                // $data['publicationDate'] = $this->ext
                $data['companyName']     = $b->text();
                // $data['companyUrl']      = $this->extractCompanyUrl();
                // $data['requiredSkills']  = $this->extractRequiredSkills();

                $job = new Job($data);

                $this->sendJob($job);
            }
        }
    }

    private function sendJob($job) {
        echo print_r($job);
        echo 'JOB ENVOYE'; exit(0);
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

    private function extractJobTable() {
        $jobsTable = $this->crawler->filter('body table')->first();

        if ($jobsTable->attr('class') == 'offre') {
            return $jobsTable;
        }

        return FALSE;
    }

    private function extractJobType() {
        return NULL;
    }

    private function extractJobPay() {
        return NULL;
    }

    private function extractJobCityName() {
        return NULL;
    }

    private function extractJobPostalCode() {
        return NULL;
    }

    private function extractJobRegionName() {
        return NULL;
    }

    private function extractJobLocation() {
        return NULL;
    }

    private function extractCompanyName() {
        return NULL;
    }

    private function extractCompanyUrl() {
        return NULL;
    }

    private function extractRequiredSkills() {
        return NULL;
    }
}

?>
