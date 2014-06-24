<?php

namespace Api;

require 'utils/data/regions.php';
require 'utils/data/jobsTypes.php';
require 'utils/data/requiredSkills.php';
require 'Api/Feeder.php';
require 'Api/Utils.php';

use Api\Feeder;
use Api\Utils;

class Job implements \SplSubject {
    private $observers = array();
    public $exception;

    public function __construct($data) {
        $this->feeder = new Feeder('127.0.0.1', 9200);
        $this->data   = array();

        // Data considered as safe
        $this->data['websiteName']     = (isset($data['websiteName']))     ? $data['websiteName']     : NULL;
        $this->data['websiteUrl']      = (isset($data['websiteUrl']))      ? $data['websiteUrl']      : NULL;
        $this->data['jobTitle']        = (isset($data['jobTitle']))        ? $data['jobTitle']        : NULL;
        $this->data['jobUrl']          = (isset($data['jobUrl']))          ? $data['jobUrl']          : NULL;
        $this->data['companyName']     = (isset($data['companyName']))     ? $data['companyName']     : NULL;
        $this->data['companyUrl']      = (isset($data['companyUrl']))      ? $data['companyUrl']      : NULL;
        $this->data['publicationDate'] = (isset($data['publicationDate'])) ? $data['publicationDate'] : NULL;
        $this->data['recoveryDate']    = date('d/m/Y');

        $this->cityData = array();

        if (empty($data['jobCityName']) === FALSE) {
            $this->cityData = $this->feeder->searchCityData(Utils::slug($data['jobCityName']));
        }

        // Data considered as not safe and need to be normalized        
        $this->data['jobCityName']   = $this->normalizeJobCityName($data['jobCityName']);
        $this->data['jobPostalCode'] = $this->normalizeJobPostalCode($data['jobCityName']);
        // $this->jobPay         = $this->normalizeJobPay($data['jobPay']);
        $this->data['jobType']        = $this->normalizeJobType($data['jobType']);
        $this->data['jobRegionName']  = $this->normalizeRegionName($data['jobRegionName']);
        $this->data['requiredSkills'] = $this->normalizeRequiredSkills($data['requiredSkills']);
        $this->data['jobLocation']    = $this->normalizeJobLocation();
    
        $this->id = $this->generateId();
    }

    public function attach(SplObserver $observer) {
        $hash = spl_observer_hash($observer);

        $this->observers[$hash] = $observer;

        return $this;
    }

    public function detach(SplObserver $observer) {
        $hash = spl_observer_hash($observer);

        unset($this->observers[$hash]);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function handle(Exception $e) {
        $this->exception = $e;
        
        $this->notify();
    }

    private function normalizeJobCityName($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['city_name']) === FALSE) {
                return $this->cityData['_source']['city_name'];
            }
        }

        return NULL;
    }

    private function normalizeJobPostalCode($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            if (Utils::slug($jobCityName) == 'paris') {
                return 75000;
            }
            else if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['city_postal_code']) === FALSE) {
                return $this->cityData['_source']['city_postal_code'];
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

     private function normalizeJobLocation() {
        if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['location']) === FALSE) {
            return array($this->cityData['_source']['location']['lat'], $this->cityData['_source']['location']['lon']);
        }

        return NULL;
    }

    public function generateId() {
        return md5(Utils::slug($this->data['jobTitle']) . Utils::slug($this->data['companyName']) . Utils::slug($this->data['jobType']));
    }

    public function checkExistingId() {
        return $this->feeder->searchExistingCityId($this->id);
    }

    public function indexElement() {
        if ($this->checkExistingId()) {
            $status = $this->feeder->indexElement('jobsapi', 'job', $this->data, $this->id);
        
            // @TODO: check if job is indexed
            /*
            if ($status) {
                return TRUE;
            }
            else {
                return FALSE;
            }*/
        }
        else {
            return FALSE;
        }

        $this->notify();
    }
}

?>
