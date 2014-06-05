<?php

// namespace Api;

require 'Api/Website.php';
require 'Api/Job.php';
require 'Api/Grubber.php';

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
        $this->grubber   = new Grubber();
    }

    public function crawl() {
        $jobsTable = $this->extractJobTable();

        if ($jobsTable != FALSE) {
            $jobRow = $jobsTable->filter('tr');
            
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
                // $data['jobPostalCode']   = $this->extractJobPostalCode();
                // $data['jobLocation']     = $this->extractJobLocation();
                $data['recoveryDate']    = date('Y-m-d');
                $data['companyName']     = $b->text();
                // $data['requiredSkills']  = $this->extractRequiredSkills();

                $jobHtml = $this->getPageDom($data['jobUrl']);
                $jobDom  = new Crawler($jobHtml);

                $location = $jobDom->filter('div#emploi div#premier b')->reduce(function($node, $i) {
                    if ($node->attr('itemprop') != 'jobLocation') {
                        return FALSE;
                    }
                });

                $pattern = '/(.*)(\(.*\)$)/';

                preg_match($pattern, $location->first()->text(), $matches);

                $data['jobCityName'] = NULL;

                if (isset($matches[1])) {
                    $data['jobCityName'] = $matches[1];
                }

                $data['jobRegionName'] = NULL;

                if (isset($matches[2])) {
                    $data['jobRegionName'] = trim($matches[2], '()'); 
                }

                $publicationDates = $jobDom->filter('div#emploi p.navinfo > time')->reduce(function($node, $i) {
                    if ($node->attr('pubdate') != NULL) {
                        return FALSE;
                    }
                });

                $data['publicationDate'] = $publicationDates->first()->text();

                $companyUrls = $jobDom->filter('div#emploi div#second > p > a')->reduce(function($node, $i) {
                    if ($node->attr('itemprop') != 'url') {
                        return FALSE;
                    }
                });

                // $data['companyUrl'] = $companyUrls->first()->attr('href');

                // echo print_r($data, TRUE);

                $job = new Job($data);

                $this->sendJob($job);
            });
        }
    }

    private function sendJob($job) {
        $this->grubber->sendJob($job);
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
}

?>
