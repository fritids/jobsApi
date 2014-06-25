<?php

namespace Api;

require 'utils/data/regions.php';
require 'utils/data/jobsTypes.php';
require 'utils/data/requiredSkills.php';
require 'Api/Feeder.php';
require 'Api/Utils.php';
require 'Monitoring/Monitor.php';

use Api\Feeder;
use Api\Utils;
use Monitoring\Monitor;

class Job implements \SplSubject {
    private $observers = array();
    public $exceptions = array();

    public function __construct($data) {
        $this->feeder = new Feeder('localhost', 9200);
        $this->data   = array();

        $this->attach(new Monitor($this->feeder));

        // Data considered as safe
        $this->data['websiteName']     = (isset($data['websiteName']))     ? trim($data['websiteName'])     : '';
        $this->data['websiteUrl']      = (isset($data['websiteUrl']))      ? trim($data['websiteUrl'])      : '';
        $this->data['jobTitle']        = (isset($data['jobTitle']))        ? trim($data['jobTitle'])        : '';
        $this->data['jobUrl']          = (isset($data['jobUrl']))          ? trim($data['jobUrl'])          : '';
        $this->data['companyName']     = (isset($data['companyName']))     ? trim($data['companyName'])     : '';
        $this->data['companyUrl']      = (isset($data['companyUrl']))      ? trim($data['companyUrl'])      : '';
        $this->data['publicationDate'] = (isset($data['publicationDate'])) ? trim($data['publicationDate']) : '';
        $this->data['recoveryDate']    = date('d/m/Y');

        $this->id = $this->generateId();

        $this->cityData = array();

        if (empty($data['jobCityName']) === FALSE) {
            $this->cityData = $this->feeder->searchCityData(Utils::slug($data['jobCityName']));
        }

        // Data considered as not safe and need to be normalized        
        $this->data['jobCityName']    = $this->normalizeJobCityName($data['jobCityName']);
        $this->data['jobPostalCode']  = $this->normalizeJobPostalCode($data['jobCityName']);
        // $this->jobPay         = $this->normalizeJobPay($data['jobPay']);
        $this->data['jobType']        = $this->normalizeJobType($data['jobType']);
        $this->data['jobRegionName']  = $this->normalizeRegionName($data['jobRegionName']);
        $this->data['requiredSkills'] = $this->normalizeRequiredSkills($data['requiredSkills']);
        $this->data['jobLocation']    = $this->normalizeJobLocation();
    }

    public function attach(\SplObserver $observer) {
        $hash = spl_object_hash($observer);

        $this->observers[$hash] = $observer;
    }

    public function detach(\SplObserver $observer) {
        $hash = spl_object_hash($observer);

        unset($this->observers[$hash]);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function handle($category, $message) {
        $this->exceptions[$category] = $message;
        
        $this->notify();
    }

    private function normalizeJobCityName($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['city_name']) === FALSE) {
                return $this->cityData['_source']['city_name'];
            }

            $this->handle('city', $jobCityName);
        }

        return '';
    }

    private function normalizeJobPostalCode($jobCityName) {
        if (empty($jobCityName) === FALSE) {
            if (Utils::slug($jobCityName) == 'paris') {
                return 75000;
            }
            else if (empty($this->cityData) === FALSE && empty($this->cityData['_source']['city_postal_code']) === FALSE) {
                return $this->cityData['_source']['city_postal_code'];
            }

            $this->handle('postal code', $jobCityName);
        }

        return '';
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

            $this->handle('job type', $jobType);
        }
        
        return '';
    }

    private function normalizeRegionName($regionName) {
        if (empty($regionName) === FALSE) {
            foreach ($GLOBALS['regions'] as $code => $name) {
                if (Utils::slug($regionName) == Utils::slug($name)) {
                    return $name;
                }
            }

            $this->handle('region', $regionName);
        }

        return '';
    }

    private function normalizeRequiredSkills($requiredSkills) {
        if (empty($requiredSkills) === FALSE) {
            $normalizedSkills = array();
            $notFoundedSkills = array();

            foreach ($requiredSkills as $skill) {
                foreach ($GLOBALS['requiredSkills'] as $name => $replacement) {
                    if (Utils::slug($skill) == Utils::slug($name)) {
                        if (isset($replacement)) {
                            $normalizedSkills []= $replacement;
                        }
                        else {
                            $normalizedSkills []= $name;
                        }
                    }
                }
            }

            if (empty($notFoundedSkills) === FALSE) {
                $this->handle('skill', implode(', ', $notFoundedSkills));
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
        return md5(Utils::slug($this->data['websiteName']) . Utils::slug($this->data['jobTitle']) . Utils::slug($this->data['companyName']));
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
