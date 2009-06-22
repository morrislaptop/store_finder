<?php
	class Store extends StoreFinderAppModel
	{
		var $name = 'Store';
	    var $actsAs = array();

	    function __construct($id = false, $table = null, $ds = null) {
	    	$this->actsAs = array(
	    		'StoreFinder.Geocoded' => array(
			        'key' => Configure::read('StoreFinder.google_maps_api')
			    )
	    	);
			parent::__construct($id, $table, $ds);
	    }

	    /**
	    * Finds all stores in the same region as the postcode, as supplied
	    * by the Australia Post data.
	    *
	    * @param mixed $postcode
	    * @return array
	    */
		function findNearPostcode($postcode)
		{
			#return $this->find('all', array('limit' => '20', 'order' => 'RAND()'));
			$sql = "SELECT
						Store.*
					FROM
						postcodes Postcode1, postcodes Postcode2, stores Store
					WHERE
						Postcode1.region = Postcode2.region
						AND Postcode2.pcode = Store.postcode
						AND Postcode1.pcode = '{$postcode}'
					GROUP BY
						Store.id
					ORDER BY
						Store.postcode = '{$postcode}' DESC, Store.suburb, Store.name";
			$results = $this->query($sql);
			$results = $this->__filterResults($results);
			return $results;
		}

		/**
		* Finds stores within a distance
		*
		* @param mixed $postcode
		*/
		function findClosest($from, $limit)
		{
			$from = $this->geocode($from);
			$results = $this->findAllNearDistance($from, $limit);
			return $results;
		}

		function findInStateFromPostcode($postcode)
		{
			// get the state
			App::import('Model', 'Postcode');
			$Postcode = new Postcode();
			$postcode = $Postcode->findByPcode($postcode);
			if ( !$postcode ) {
				return array();
			}

			// get all in the state
			return $this->findAllByState($postcode['Postcode']['state']);
		}

		function beforeSave() {
			if ( empty($this->data['Store']['lat']) || empty($this->data['Store']['lon']) ) {
				if ($coords = $this->geocode($this->data)) {
					$this->set($coords);
				}
			}
			return true;
		}
	}
?>
