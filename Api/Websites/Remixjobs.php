<?php

namespace Api\Websites;

require 'Api/Website.php';
require 'Api/Job.php';

use Api\Website;
use Api\Job;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class Remixjobs extends Website {
    public function __construct() {
        $this->website   = 'RemixJobs';
        $this->url       = 'https://remixjobs.com/';
        $this->crawlUrl  = 'https://remixjobs.com/';
        $this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
        $this->html      = $this->getPageDom($this->crawlUrl);
        $this->crawler   = new Crawler($this->html);
    }

    public function crawl() {
        
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

    private function extractJobsContainer() {
        return $this->crawler
            ->filter('body ul')
            ->reduce(function($node, $i) {
                if ($node->attr('class') != 'jobs-list') {
                    return NULL;
                }
            })
            ->first();
    }
}

?>
