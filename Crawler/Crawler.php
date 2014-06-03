<?php

namespace \Crawler;

use Symfony\Component\DomCrawler\Crawler;



// http://www.phpro.org/examples/Get-Links-With-DOM.html
// http://www.phpro.org/examples/Parse-HTML-With-PHP-And-DOM.html
/*
class Crawler {
	public function __construct($url) {
		$this->url       = $url;
		$this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
		$this->html      = $this->getHtmlPage($this->url);
	}
	
	public function getUrl() {
		return $this->url;
	}

	public function getUserAgent() {
		return $this->userAgent;
	}

	public function getHtml() {
		return $this->html;
	}

	private function getHtmlPage($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_COOKIE, "someCookie=2127;onlineSelection=C");
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
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

	public function getJobs() {
		$dom  = new domDocument;
		$jobs = array();

		@$dom->loadHTML($this->getHtml());

		$dom->preserveWhiteSpace = FALSE;

		$tables = $dom->getElementsByTagName('table');

		if ($tables->item(0)->getAttribute('class') == 'offre') {
			$rows = $tables->item(0)->getElementsByTagName('tr');

			foreach ($rows as $row) {
				$job  = new Job();
				$cols = $row->getElementsByTagName('td');
				$link = $cols->item(0)->getElementsByTagName('a');
				$b    = $cols->item(0)->getElementsByTagName('b');
				$span = $cols->item(1)->getElementsByTagName('span');

				$job->websiteName     = 'Alsacréations';
				$job->websiteUrl      = $this->url;
				$job->jobTitle        = $link->item(0)->nodeValue;
				$job->jobUrl          = $this->getUrl() . $link->item(0)->getAttribute('href');
				$job->jobType         = $span->item(0)->nodeValue;
				$job->jobCityName     = $cols->item(1)->nodeValue;
				$job->jobRegionName   = $cols->item(1)->nodeValue;
				// $job->jobGpsLocation  = $this->geocode($job->jobCityName, NULL);
				$job->companyName     = $b->item(0)->nodeValue;

				$jobs []= $job;

				$jobDom  = new domDocument;
				$jobHtml = $this->getHtmlPage($this->getUrl() . $link->item(0)->getAttribute('href'));

				@$jobDom->loadHTML($jobHtml);

				$jobDom->preserveWhiteSpace = FALSE;

				/*$p = $jobDom->getElementsByTagName('p');
				
				foreach ($p as $paragraph) {
					$pClass = $paragraph->getAttribute('class');

					if ($pClass == 'vmid') {
						$b          = $paragraph->getElementsByTagName('b');
						$bAttribute = $b->item(0)->getAttribute('itemprop');

						if ($bAttribute == 'jobLocation') {
							echo $b->item(0)->nodeValue . '<br /><hr />';
						}
					}
				}*/
		/*	}

			return $jobs;
		}
		else {
			throw new Exception('Crawler.php : <table class="offre"> n\'a pas été trouvé', 1);
		}
	}

	private function geocode($cityName, $postalCode) {
		$address = $cityName;

		if (! empty($postalCode)) {
			$address .= ' ' . $postalCode;
		}

		$geocoder   = 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';
		$urlAddress = urlencode(utf8_encode($address));
		$query      = sprintf($geocoder, $urlAddress);
        $results    = file_get_contents($query);
 
error_log('Adresse : ' . $address . ' => ' . print_r($results, TRUE)); exit(0);

		return json_decode($results);
	}

	private function indexJob($job) {
		$searcher = new Searcher();
	}
}*/

?>
