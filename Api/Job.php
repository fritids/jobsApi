<?php

namespace Api;

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

	private function __toString() {
        return '<tr>' .
        	'<td>' . $this->jobTitle . '</td>' .
        	'<td>' . $this->jobType . '</td>' .
        	'<td>' . $this->jobCityName . '</td>' .
        	'<td>' . $this->jobRegionName . '</td>' .
        '</tr>';
    }

    private function generateId() {
    	$string = $this->slug($this->jobTitle) . $this->slug($this->companyName) . $this->slug($this->jobType);

    	return md5($string);
    }

    private function slug($text) {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $text    = mb_convert_encoding((string)$text, 'UTF-8', mb_list_encodings());
        $options = array(
            'delimiter'     => '-',
            'limit'         => NULL,
            'lowercase'     => TRUE,
            'replacements'  => array(),
            'transliterate' => FALSE,
        );
    }
}

?>
