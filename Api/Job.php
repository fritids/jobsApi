<?php

// namespace Api;

class Job {
    public function __construct($data) {
        $this->websiteName     = (isset($data['websiteName']))    ? $data['websiteName']    : NULL;
        $this->websiteUrl      = (isset($data['websiteUrl']))     ? $data['websiteUrl']     : NULL;
        $this->jobTitle        = (isset($data['jobTitle']))       ? $data['jobTitle']       : NULL;
        $this->jobUrl          = (isset($data['jobUrl']))         ? $data['jobUrl']         : NULL;
        $this->jobType         = (isset($data['jobType']))        ? $data['jobType']        : NULL;
        $this->jobPay          = (isset($data['jobPay']))         ? $data['jobPay']         : 0;
        $this->jobCityName     = (isset($data['jobCityName']))    ? $data['jobCityName']    : NULL;
        $this->jobPostalCode   = (isset($data['jobPostalCode']))  ? $data['jobPostalCode']  : NULL;
        $this->jobRegionName   = (isset($data['jobRegionName']))  ? $data['jobRegionName']  : NULL;
        $this->jobGpsLocation  = (isset($data['jobGpsLocation'])) ? $data['jobGpsLocation'] : NULL;
        $this->recoveryDate    = date('Y-m-d');
        $this->publicationDate = (isset($data['publicationDate'])) ? $data['publicationDate'] : NULL;
        $this->companyName     = (isset($data['companyName']))     ? $data['companyName']     : NULL;
        $this->companyUrl      = (isset($data['companyUrl']))      ? $data['companyUrl']      : NULL;
        $this->requiredSkills  = (isset($data['requiredSkills']))  ? $data['requiredSkills']  : array();

        $this->id = generateId();
    }

    private function generateId() {
        $string = $this->slug($this->jobTitle) . $this->slug($this->companyName) . $this->slug($this->jobType);

        return md5($string);
    }

    
}

?>
