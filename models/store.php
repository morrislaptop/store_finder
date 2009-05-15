<?php
	class Store extends AppModel
	{
		var $name = 'Store';

	    var $actsAs = array();

	    function __construct($id = false, $table = null, $ds = null) {
	    	$this->actsAs = array(
	    		'StoreFinder.Geocoded' => array(
			        'key' => Configure::read('App.google_maps_api')
			    )
	    	);
			parent::__construct($id, $table, $ds);
	    }

		function findNearPostcode($postcode)
		{
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
						Store.suburb, Store.name";
			$results = $this->query($sql);
			$results = $this->__filterResults($results);
			return $results;
		}

		/** adds lat / long info to results **/
		function afterFind($results) {
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
