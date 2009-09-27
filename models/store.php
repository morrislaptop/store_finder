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
		$data = array(
			'Contact' => array(
				'phone' => trim($row['Store']['TELEPHONE NUMBER']),
				'address' => trim($row['Store']['ADDRESS']),
				'suburb' => trim($row['Store']['SUBURB']),
				'postcode' => trim($row['Store']['POSTCODE']),
				'name' => trim($row['Store']['PHARMACY NAME']),
				'model' => 'Store'
			),
			'Store' => array(
				'display_address' => trim($row['Store']['ADDRESS']),
				'visible' => 1
			)
		);
		return $data;
	}*/
}
?>