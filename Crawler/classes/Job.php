<?php

class Job {
	public function __construct() {
		$this->websiteName     = NULL;
		$this->websiteUrl      = NULL;
		$this->jobTitle        = NULL;
		$this->jobUrl          = NULL;
		$this->jobType         = NULL;
		$this->jobPay          = NULL;
		$this->jobCityName     = NULL;
		$this->jobPostalCode   = NULL;
		$this->jobRegionName   = NULL;
		$this->jobGpsLocation  = NULL;
		$this->recoveryDate    = date('Y-m-d');
		$this->publicationDate = NULL;
		$this->companyName     = NULL;
		$this->companyUrl      = NULL;
		$this->requiredSkills  = array();
	}

	public function __get($attribute) {
        return $this->$attribute;
    }

	public function __set($attribute, $value) {
		$this->$attribute = $value;
	}

	public function __toString() {
        return '<tr>' .
        	'<td>' . $this->jobTitle . '</td>' .
        	'<td>' . $this->jobType . '</td>' .
        	'<td>' . $this->jobCityName . '</td>' .
        	'<td>' . $this->jobRegionName . '</td>' .
        '</tr>';
    }
}

?>
