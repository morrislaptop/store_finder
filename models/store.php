<?php
	class Store extends StoreFinderAppModel
	{
		var $name = 'Store';
	    var $actsAs = array();
	    var $belongsTo = array(
	    	'Postcode' // we cant join this so ALWAYS contain
	    );

	    function __construct($id = false, $table = null, $ds = null) {
	    	$this->actsAs = array(
	    		'StoreFinder.Geocoded' => array(
			        'key' => Configure::read('StoreFinder.google_maps_api')
			    )
	    	);
			parent::__construct($id, $table, $ds);
	    }

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

		function findInStateFromPostcode($postcode)
		{
			// get the state
			$postcode = $this->Postcode->findByPcode($postcode);
			if ( !$postcode ) {
				return array();
			}

			// get all in the state.
			$this->contain();
			return $this->findAllByState($postcode['Postcode']['state']);
		}

		/** adds lat / long info to results **/
		function afterFind($results) {
			if ( !isset($results[0]['Store']) ) {
				return $results;
			}
			foreach ($results as &$result) {
				$latlong = $this->geocode($result['Store']);
				if ( $latlong ) {
					$result['Store'] = array_merge($result['Store'], $latlong);
				}
			}
			return $results;
		}
	}
?>
