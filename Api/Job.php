<?php

namespace Api;

require 'utils/data/regions.php';
require 'utils/data/jobsTypes.php';
require 'Api/Feeder.php';
require 'Api/Util.php';

use Api\Feeder;
use Api\Utils;

class Job {
    public function __construct($data) {
        $this->feeder = new Feeder('127.0.0.1', 9200);

        // Data considered as safe
        $this->websiteName     = (isset($data['websiteName']))     ? $data['websiteName']     : NULL;
        $this->websiteUrl      = (isset($data['websiteUrl']))      ? $data['websiteUrl']      : NULL;
        $this->jobTitle        = (isset($data['jobTitle']))        ? $data['jobTitle']        : NULL;
        $this->jobUrl          = (isset($data['jobUrl']))          ? $data['jobUrl']          : NULL;
        $this->companyName     = (isset($data['companyName']))     ? $data['companyName']     : NULL;
        $this->companyUrl      = (isset($data['companyUrl']))      ? $data['companyUrl']      : NULL;
        $this->publicationDate = (isset($data['publicationDate'])) ? $data['publicationDate'] : NULL;
        $this->recoveryDate    = date('d/m/Y');

        $this->cityData = array();

        if (empty($jobCityName) === FALSE) {
            $this->cityData = $this->feeder->searchForNormalize('jobsapi', 'job', 'jobCityName', $jobCityName);
        }

        // Data considered as not safe and need to be normalized        
        $this->jobCityName    = $this->normalizeJobCityName($data['jobCityName']);
        $this->jobPostalCode  = $this->normalizeJobPostalCode($data['jobPostalCode']);
        $this->jobPay         = $this->normalizeJobPay($data['jobPay']);
        $this->jobType        = $this->normalizeJobType($data['jobType']);
        $this->jobRegionName  = $this->normalizeRegionName($data['jobRegionName']);
        $this->requiredSkills = $this->normalizeRequiredSkills($data['requiredSkills']);
        $this->jobLocation    = $this->getLocation();
    }

    private function normalizeJobCityName($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['jobCityName']) === FALSE) {
                return $this->cityData['_source']['jobCityName'];
            }
        }

        return NULL;
    }

    private function normalizeJobPostalCode($jobPostalCode) {
        if (empty($jobPostalCode) === FALSE) {
            if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['jobPostalCode']) === FALSE) {
                return $this->cityData['_source']['jobPostalCode'];
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
                if (Utils::slug($jobType) == Utils::slug($type)) {
                    return $type;
                }
            }
        }
        
        return NULL;
    }

    private function normalizeRegionName($regionName) {
        if (empty($regionName) === FALSE) {
            foreach ($GLOBALS['regions'] as $code => $name) {
                if (Utils::slug($regionName) == Utils::slug($name)) {
                    return $name;
                }
            }
        }

        return NULL;
    }

    private function normalizeRequiredSkills($requiredSkills) {
        if (empty($requiredSkills) === FALSE) {
            $normalizedSkills = array();

            foreach ($requiredSkills as $skill) {
                foreach ($GLOBALS['requiredSkills'] as $name) {
                    if (Utils::slug($skill) == Utils::slug($name)) {
                        $normalizedSkills []= $name;
                    }
                }
            }

            return $normalizedSkills;
        }

        return array();
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

    public function generateId() {
        return md5(Utils::slug($this->jobTitle) . Utils::slug($this->companyName) . Utils::slug($this->jobType));
    }
}

?>
