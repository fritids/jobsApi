<?php

namespace Api\Websites;

require 'Api/Website.php';
require 'Api/Job.php';

use Api\Website;
use Api\Job;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

// https://github.com/symfony/DomCrawler
// http://symfony.com/fr/doc/current/components/dom_crawler.html
// http://symfony.com/fr/doc/current/components/css_selector.html

class Alsacreations extends Website {
    public function __construct() {
        $this->website   = 'Alsacréations';
        $this->url       = 'http://emploi.alsacreations.com';
        $this->crawlUrl  = 'http://emploi.alsacreations.com/offres.html';
        $this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
        $this->html      = $this->getPageDom($this->crawlUrl);
        $this->crawler   = new Crawler($this->html);
    }

    public function crawl() {
        $jobsTable = $this->extractJobTable();

        if ($jobsTable != NULL) {
            $jobRow = $jobsTable->filter('tr');
            
            if (empty($jobRow) === FALSE) {
                $jobRow->each(function($node, $i) {
                    $data = array();
                    $link = $node->filter('a')->first();
                    $span = $node->filter('span')->first();
                    $b    = $node->filter('b')->first();
                    
                    $data['jobType']     = $span->text();
                    $data['websiteName'] = $this->website;
                    $data['websiteUrl']  = $this->url; 
                    $data['jobTitle']    = $link->text();
                    $data['jobUrl']      = $this->url . '/' . $link->attr('href');
                    $data['companyName'] = $b->text();

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

                    $data['requiredSkills'] = array();
                    $requiredSkills         = $jobDom->filter('p.vmid[itemprop=skills] > b');

                    $data['requiredSkills'] = $requiredSkills->each(function($node, $i) {
                        return $node->text();
                    });

                    // $data['companyUrl'] = $companyUrls->first()->attr('href');

                    $job = new Job($data);

                    $job->indexElement('jobsApi', 'job', $job);
                });
            }
        }
    }

    protected function getPageDom($url) {
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
                    return NULL;
                }
            })
            ->first();
    }
}

?>
