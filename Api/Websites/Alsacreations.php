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
    }

    public function crawl() {
        $jobsTable = $this->extractJobTable();

        if ($jobsTable != FALSE) {
            $jobRow = $jobsTable->filter('tr'); 

            /*
            $myFile     = 'debug.txt';
            $fh         = fopen($myFile, 'w') or die('Can\'t open file');
            $stringData = print_r($jobRow, TRUE);
            
            fwrite($fh, $stringData);
            fclose($fh);
            */
            
            $jobRow->each(function($node, $i) {
                $data = array();
                $link = $node->filter('a')->first();
                $span = $node->filter('span')->first();
                $b    = $node->filter('b')->first();
                
                $allowedJobTypes = array('CDI', 'CDD', 'Stage', 'Apprentissage', 'Contrat Pro', 'Télétravail');

                if (in_array($span->text(), $allowedJobTypes)) {
                    $data['jobType'] = $span->text();   
                }

                $data['jobTitle']        = $link->text();
                $data['jobUrl']          = $this->url . $link->attr('href');
                // $data['jobPay']          = $this->extractJobPay();
                // $data['jobCityName']     = $node->text();
                // $data['jobPostalCode']   = $this->extractJobPostalCode();
                // $data['jobRegionName']   = $node->eq(1)->text();
                // $data['jobLocation']     = $this->extractJobLocation();
                $data['recoveryDate']    = date('Y-m-d');
                $data['companyName']     = $b->text();
                // $data['companyUrl']      = $this->extractCompanyUrl();
                // $data['requiredSkills']  = $this->extractRequiredSkills();

                $jobHtml = $this->getPageDom($data['jobUrl']);
                $jobDom  = new Crawler($jobHtml);

                $data['publicationDate'] = $jobDom->filter('div#emploi p.navinfo > time')->each(function($node, $i) {
                    return $node->text();
                })[0];

                $data['companyUrl'] = $jobDom->filter('div#emploi div#second > p > a')->each(function($node, $i) {
                    return $node->attr('href');
                })[0];

                /*
                $jobDom->filter('body table')
                    ->reduce(function($node, $i) {
                        if ($node->attr('class') != 'offre') {
                            return FALSE;
                        }
                    })
                    ->first();
                }); 
                */

                echo print_r($data, TRUE);

                // $job = new Job($data);

                // $this->sendJob($job);
            });
        }
    }

    private function sendJob($job) {
        echo print_r($job);
        echo 'JOB ENVOYE'; exit(0);
    }

    private function getPageDom($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_COOKIE, 'someCookie=2127;onlineSelection=C');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);
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
        return $this->crawler
            ->filter('body table')
            ->reduce(function($node, $i) {
                if ($node->attr('class') != 'offre') {
                    return FALSE;
                }
            })
            ->first();
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
