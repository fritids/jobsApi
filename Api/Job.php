<?php

namespace Api;

require 'utils/data/regions.php';
require 'utils/data/jobsTypes.php';
require 'Api/Feeder.php';

use Api\Feeder;

class Job {
    public function __construct($data) {
        // Data considered as safe
        $this->websiteName  = (isset($data['websiteName'])) ? $data['websiteName'] : NULL;
        $this->websiteUrl   = (isset($data['websiteUrl']))  ? $data['websiteUrl']  : NULL;
        $this->jobTitle     = (isset($data['jobTitle']))    ? $data['jobTitle']    : NULL;
        $this->jobUrl       = (isset($data['jobUrl']))      ? $data['jobUrl']      : NULL;
        $this->companyName  = (isset($data['companyName'])) ? $data['companyName'] : NULL;
        $this->companyUrl   = (isset($data['companyUrl']))  ? $data['companyUrl']  : NULL;
        $this->recoveryDate = date('d/m/Y');

        $this->cityData = array();

        // Data considered as not safe and need to be normalized
        $this->jobPostalCode   = (isset($data['jobPostalCode']))   ? $data['jobPostalCode']   : NULL;
        $this->publicationDate = (isset($data['publicationDate'])) ? $data['publicationDate'] : NULL;
        $this->requiredSkills  = (isset($data['requiredSkills']))  ? $data['requiredSkills']  : array();
        
        $this->jobCityName   = $this->normalizeJobCityName(['jobCityName']);
        $this->jobPay        = $this->normalizeJobPay(['jobPay']);
        $this->jobType       = $this->normalizeJobType($data['jobType']);
        $this->jobRegionName = $this->normalizeRegionName($data['jobRegionName']);
        $this->jobLocation   = $this->getLocation();
    }

    private function normalizeJobCityName($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            $feeder         = new Feeder('127.0.0.1', 9200);
            $this->cityData = $feeder->searchForNormalize('jobsapi', 'job', 'jobCityName', $jobCityName);

            if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['jobCityName']) === FALSE) {
                return $this->cityData['_source']['jobCityName'];
            }
        }

        return NULL;
    }

    private function normalizeJobPay($jobPay) {
        if (empty($jobPay) === FALSE) {
            if (is_float($jobPay) === FALSE) {
                $jobPay = (float) $jobPay;

                if ($jobPay >= 0 && $jobPay <= 100000) {
                    return $jobPay;
                }
            }
        }

        return 0;
    }

    private function normalizeJobType($jobType) {
        if (empty($jobType) === FALSE) {
            foreach ($GLOBALS['jobsTypes'] as $type) {
                if ($this->slug($jobType) == $this->slug($type)) {
                    return $type;
                }
            }
        }
        
        return NULL;
    }

    private function normalizeRegionName($regionName) {
        if (empty($regionName) === FALSE) {
            foreach ($GLOBALS['regions'] as $code => $name) {
                if ($this->slug($regionName) == $this->slug($name)) {
                    return $name;
                }
            }
        }

        return NULL;
    }

    private function getLocation() {
        $string = str_replace (' ', '+', urlencode($this->jobCityName . ' ' . $this->jobRegionName));
        $api    = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $string . '&sensor=false';
         
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = json_decode(curl_exec($ch), true);
         
        // echo print_r(array_keys($response['results'][0]), TRUE);
        // echo print_r($response['results'][0]['address_components'], TRUE);
        // echo print_r($response['results'][0]['formatted_address'], TRUE);

        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return NULL;
        }
        
        $geometry  = $response['results'][0]['geometry'];
        $longitude = $geometry['location']['lat'];
        $latitude  = $geometry['location']['lng'];
         
        return array(
            'lat' => $geometry['location']['lng'],
            'lon' => $geometry['location']['lat'],
        );
    }

    private function slug($string) {
        $a = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
            'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
            'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
            'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
            'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
            'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
            'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
            'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 
            'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 
            'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
            'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'
        );

        $b = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
            'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
            'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
            'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
            'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
            'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
            'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
            'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
            's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
            'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
            'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'
        );
    
        $string = trim($string);
        $string = str_replace($a, $b, $string);
        $string = str_replace('-', ' ', $string);
        $string = mb_strtolower($string, 'UTF-8');

        return $string;
    }

    public function generateId() {
        return md5($this->slug($this->jobTitle) . $this->slug($this->companyName) . $this->slug($this->jobType));
    }
}

?>
