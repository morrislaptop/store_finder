<?php
class Store extends StoreFinderAppModel
{
	var $name = 'Store';
	var $actsAs = array(
	    'StoreFinder.Geocoded' => array(
	    	'key' => null,
	    	'geoFields' => array(
				'Contact.address1',
				'Contact.address2',
				'Contact.suburb',
				'Contact.postcode',
				'Contact.country'
			)
	    ),
	    'Containable'
	);
	var $hasOne = array(
		'Contact' => array(
			'className' => 'Crm.Contact',
            'foreignKey' => 'foreign_key',
            'conditions' => array('Contact.model' => 'Store'),
            'dependent' => true
        )
	);
	var $_findMethods = array('distance' => true);

	function __construct($id = false, $table = null, $ds = null) {
	    $this->actsAs['StoreFinder.Geocode']['key'] = Configure::read('StoreFinder.google_maps_api');
		parent::__construct($id, $table, $ds);
	}
	
	/**
	* Dispatches the custom find type onto the Geocoded behavior
	*/
	function _findDistance($state, $queryData, $results = array()) {
		return $this->Behaviors->Geocoded->findDistance($this, $state, $queryData, $results);
	}
	
	/*function beforeImport($row) {
		$row['Store'] = array_map('trim', $row['Store']);
		$data = array(
			'Contact' => array(
				'phone' => $row['Store']['TELEPHONE NUMBER'],
				'address1' => $row['Store']['ADDRESS'],
				'suburb' => $row['Store']['SUBURB'],
				'postcode' => $row['Store']['POSTCODE'],
				'name' => $row['Store']['PHARMACY NAME'],
				'state' => $this->findState($row['Store']['POSTCODE']),
				'model' => 'Store'
			),
			'Store' => array(
				'display_address' => trim($row['Store']['ADDRESS']),
				'visible' => 1
			)
		);
		return $data;
	}*/
	
	/**
	* Returns the state for a postcode.
	* eg. NSW
	* 
	* @link http://en.wikipedia.org/wiki/Postcodes_in_Australia#States_and_territories
	*/
	function findState($postcode) {
		$ranges = array(
			'NSW' => array(
				1000, 1999,
				2000, 2599,
				2619, 2898,
				2921, 2999
			),
			'ACT' => array(
				200, 299,
				2600, 2618,
				2900, 2920
			),
			'VIC' => array(
				3000, 3999,
				8000, 8999
			),
			'QLD' => array(
				4000, 4999,
				9000, 9999
			),
			'SA' => array(
				5000, 5999
			),
			'WA' => array(
				6000, 6797,
				6800, 6999
			),
			'TAS' => array(
				7000, 7999
			),
			'NT' => array(
				800, 999
			)
		);
		$exceptions = array(
			872 => 'NT',
			2540 => 'NSW',
			2611 => 'ACT',
			2620 => 'NSW',
			3500 => 'VIC',
			3585 => 'VIC',
			3586 => 'VIC',
			3644 => 'VIC',
			3707 => 'VIC',
			2899 => 'NSW',
			6798 => 'WA',
			6799 => 'WA',
			7151 => 'TAS'
		);
		
		$postcode = intval($postcode);
		if ( array_key_exists($postcode, $exceptions) ) {
			return $exceptions[$postcode];
		}
		
		foreach ($ranges as $state => $range)
		{
			$c = count($range);
			for ($i = 0; $i < $c; $i+=2) {
				$min = $range[$i];
				$max = $range[$i+1];
				if ( $postcode >= $min && $postcode <= $max ) {
					return $state;
				}
			}
		}
		
		return null;
	}
}
?>