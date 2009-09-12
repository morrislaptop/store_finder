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
            'conditions' => array('Contact.model' => 'Store')
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
}
?>