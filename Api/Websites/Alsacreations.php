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

class Alsacreations {
    public function __construct() {
        $this->website   = 'Alsacréations';
        $this->url       = 'http://emploi.alsacreations.com';
        $this->crawlUrl  = 'http://emploi.alsacreations.com/offres.html';
        $this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
        $this->html      = $this->getPageDom($this->crawlUrl);
        $this->crawler   = new Crawler($this->html);
    }

    public function crawl() {
        $jobsList = $this->crawler->filter('body table.offre');

        if (empty($jobsList) === FALSE) {
            $jobRow = $jobsTable->filter('tr');
            
            if (empty($jobRow) === FALSE) {
                $jobRow->each(function($node, $i) {
                    $data = array();
                    
                    $data['websiteName'] = $this->website;
                    $data['websiteUrl']  = $this->url; 
                    $data['jobTitle']    = $node->filter('a.intitule')->text();
                    $data['companyName'] = $node->filter('b')->text();
                    $data['jobType']     = $node->filter('span.typecdi')->text();
                    $data['jobUrl']      = $this->url . '/' . $link->attr('href');

                    $jobHtml = $this->getPageDom($data['jobUrl']);
                    $jobDom  = new Crawler($jobHtml);

                    $location = $jobDom->filter('b[itemprop=jobLocation]');
                    $pattern  = '/(.*)(\(.*\)$)/';

                    preg_match($pattern, $location->text(), $matches);

                    $data['jobCityName'] = NULL;

                    if (isset($matches[1])) {
                        $data['jobCityName'] = $matches[1];
                    }

                    $data['jobRegionName'] = NULL;

                    if (isset($matches[2])) {
                        $data['jobRegionName'] = trim($matches[2], '()');
                    }

                    $data['publicationDate'] = $jobDom->filter('time')->text();
                    $data['companyUrl']      = $jobDom->filter('a[itemprop=url]')->text();

                    $data['requiredSkills'] = array();

                    $requiredSkills = $jobDom->filter('p.vmid[itemprop=skills] > b');

                    $data['requiredSkills'] = $requiredSkills->each(function($node, $i) {
                        return $node->text();
                    });

                    $job = new Job($data);

                    $job->indexElement();
                });
            }
        }
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

        $dom = curl_exec($ch);

        curl_close($ch);

        return $dom;
    }
}

?>
