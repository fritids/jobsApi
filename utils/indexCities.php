<?php

// http://sql.sh/736-base-donnees-villes-francaises

require_once 'data/french_departments.php';
require_once '../Api/Searcher.php';

use \Api\Searcher;

$host     = '127.0.0.1';
$port     = '9200';
$index    = 'geographic';
$type     = 'city';
$searcher = new Searcher($host, $port);

$columnsNames = array(
	'ville_id',
	'ville_departement',
	'ville_slug',
	'ville_nom',
	'ville_nom_reel',
	'ville_nom_soundex',
	'ville_nom_metaphone',
	'ville_code_postal',
	'ville_commune',
	'ville_code_commune',
	'ville_arrondissement',
	'ville_canton',
	'ville_amdi',
	'ville_population_2010',
	'ville_population_1999',
	'ville_population_2012',
	'ville_densite_2010',
	'ville_surface',
	'ville_longitude_deg',
	'ville_latitude_deg',
	'ville_longitude_grd',
	'ville_latitude_grd',
	'ville_longitude_dms',
	'ville_longitude_dms',
	'ville_zmin',
	'ville_zmax',
	'ville_population_2010_order_france',
	'ville_densite_2010_order_france',
	'ville_surface_order_france',
);
$columnsNamesToIndex = array(
	'ville_departement',
	'ville_nom_reel',
	'ville_code_postal',
	'ville_code_commune',
	'ville_population_2012',
	'ville_longitude_deg',
	'ville_latitude_deg',
);
$handle = fopen('data/french_cities.csv', 'r');

if ($handle !== FALSE) {
	$cityCounter = 0;

	while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
		$city = array();

		for ($field = 0; $field < count($row); $field++) {
			$columnName = $columnsNames[$field];

			if (in_array($columnName, $columnsNamesToIndex)) {
				if ($columnName == 'ville_departement') {
					foreach ($departments as $key => $value) {
						// echo print_r($value, TRUE) . "\n";
						// echo print_r($row[1], TRUE) . "\n";

						if ($key == $row[1]) {
							$city['ville_departement'] = $key;
							$city['nom_department']    = $value['department_name'];
							$city['nom_prefecture']    = $value['prefecture_name'];
							$city['code_region']       = $value['region_code'];
							$city['nom_region']        = $value['region_name'];
						}
					}
				}
				else {
					$city[$columnName] = $row[$field];
				}

				// echo $columnName . ' : ' . $row[$field] . "\n";
			}
		}

		$city = dataFormatting($city);
		
		// echo print_r($city, TRUE);

		$success = $searcher->indexElement($index, $type, $city);

		$cityCounter++;

		if ($cityCounter % 100 == 0) {
			echo $cityCounter . ' villes importees' . "\n";
		}

		// echo $success;
	}

	fclose($handle);
}
else {
	echo 'Fichier utils/french_cities.csv pas trouve ou pas accessible en lecture';
}

function dataFormatting($data) {
	$city        = array();
	$location    = array();
	$translation = array(
		'ville_departement'     => 'department_code',
		'ville_nom_reel'        => 'city_name',
		'ville_code_postal'     => 'city_postal_code',
		'ville_code_commune'    => 'city_insee_code',
		'ville_population_2012' => 'city_population',
		'ville_surface'         => 'city_surface',
		'ville_latitude_deg'    => '',
		'ville_longitude_deg'   => '',
		'nom_department'        => 'department_name',
		'nom_prefecture'        => 'prefecture_name',
		'code_region'           => 'region_code',
		'nom_region'            => 'region_name',
	);

	foreach ($data as $key => $value) {
		if (array_key_exists($key, $translation)) {
			if ($key == 'ville_latitude_deg') {
				$location['lat'] = $value;
			}
			else if ($key == 'ville_longitude_deg') {
				$location['lon'] = $value;
			}
			else {
				$city[$translation[$key]] = $value;
			}
		}
	}

	$city['location'] = $location;

	//echo print_r($city, TRUE); exit(0);

	return $city;
}

?>
