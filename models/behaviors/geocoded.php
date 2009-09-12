<?php

uses('http_socket');

class GeocodedBehavior extends ModelBehavior {
/**
 * Index of geo-data lookup services.  Each item contains a lookup URL with placeholders,
 * and a regular expression to parse latitude and longitude values.
 *
 * @var array
 * @access public
 */
	var $lookupServices = array(
		'google' => array(
			'http://maps.google.com/maps/geo?&q=%address&output=csv&key=%key',
			'/200,[^,]+,([^,]+),([^,\s]+)/'
		),
		'yahoo'  => array(
			'http://api.local.yahoo.com/MapsService/V1/geocode?appid=%key&location=%address',
			'/<Latitude>(.*)<\/Latitude><Longitude>(.*)<\/Longitude>/U'
		)
	);
/**
 * Index of measurement unit factors relative to miles.  These values can be specified in any method that
 * accepts a $unit parameter.  All $unit parameters also accept an arbitrary float value to use for distance
 * conversions.  Unit values are represented as follows:
 * M: miles, K: kilometers, N: nautical miles, I: inches, F: feet
 *
 * @var array
 * @access public
 */
	var $units = array('K' => 1.609344, 'N' => 0.868976242, 'F' => 5280, 'I' => 63360, 'M' => 1);

	function setup(&$model, $config = array()) 
	{
		$this->settings[$model->name] = am(array(
			'lookup' => 'google',
			'key' => null,
			'lat' => 'lat',
			'lon' => 'lon',
			'geoFields' => array(
				'street', 'address', 'addr', 'address1', 'addr1', 'address2', 'addr2', 'apt', 'city', 'state', 'zip', 'zipcode', 
				'zip_code', 'postcode', 'pcode', 'country'
			)
		), $config);
		extract($this->settings[$model->name]);

		if (!isset($this->lookupServices[low($lookup)])) {
			trigger_error('The lookup service "' . $lookup . '" does not exist.', E_USER_WARNING);
			return false;
		}
		if (!isset($this->connection)) {
			$this->connection = new HttpSocket();
		}
	}
/**
 * Retrieves the geo coordinates and sticks it in the model data before saving.
 */
 	function beforeSave(&$model) {
 		extract($this->settings[$model->name]);
 		if ( empty($model->data[$model->alias][$lat]) ) {
			$address = $this->constructAddress($model);
			$coords = $this->geocoords($model, $address);
			$model->set($coords);
 		}
 		return true;
 	}
/**
 * Constructs a string of the address from the fields.
 */
 	function constructAddress(&$model, $data = null) {
		extract($this->settings[$model->name]);
		if ( !$data ) {
			$data = $model->data;
		}
		$addressParts = array();
		foreach ($geoFields as $field) {
			$alias = $model->alias;
			if ( strpos($field, '.') !== false ) {
				list($alias, $field) = explode('.', $field);
			}
			if ( !empty($data[$alias][$field]) ) {
				$addressParts[] = $data[$alias][$field];
			}
		}
		return low(implode(' ', $addressParts));
 	}

/**
 * Get geocode lat/lon points for given address from web service (Google/Yahoo!)
 *
 * @param string $address
 * @access private
 * @return array Latitude and longitude data, or false on failure
 */
	function geocoords(&$model, $address) {
		extract($this->settings[$model->name]);
		
		// Make sure its a string.
		if ( is_array($address) ) {
			$address = $this->constructAddress();
		}

		$url = r(
			array('%key', '%address'),
			array($key, rawurlencode($address)),
			$this->lookupServices[low($lookup)][0]
		);

		$code = false;
		if($result = $this->connection->get($url)) {
			if (preg_match($this->lookupServices[low($lookup)][1], $result, $match)) {
				$code = array($lat => floatval($match[1]), $lon => floatval($match[2]));
			}
		}
		return $code;
	}
/**
 * Calculate the distance between to geographic coordinates using the circle distance formula
 *
 * @param float $lat1
 * @param float $lat2
 * @param float $lon1
 * @param float $lon2
 * @param float $unit   M=miles, K=kilometers, N=nautical miles, I=inches, F=feet
 */
	function distance($lat1, $lon1, $lat2 = null, $lon2 = null, $unit = 'M') {
		$m =  69.09 * rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2))));
		if (isset($this->units[up($unit)])) {
			$m *= $this->units[up($unit)];
		}
		return $m;
	}

/**
 * Generates an SQL query to calculate the distance between the coordinates of each record and the given x/y values,
 * and compares the result to $distance.
 *
 * @param mixed $x  Either a float or an array.  If an array, it should contain the X and Y values of the coordinate.
 * @param mixed $y  If $x is an array, this value is used as $distance, otherwise, the Y coordinate.
 * @param float $distance  The distance (in miles) to search within
 */
	function findAllNear(&$model, $x, $y, $limit = null) {
		extract($this->settings[$model->name]);
		if (is_array($x)) {
			$limit = $y;
			list($x, $y) = array_values($x);
		}
		list($x2, $y2) = array($model->escapeField($lon), $model->escapeField($lat));
		list($x, $y) = array(floatval($x), floatval($y));
		debug(compact('x', 'y', 'x2', 'y2', 'limit'));
		$fields = array(
			" as distance",
			$model->alias . '.*'
		);
		debug($fields);
		$order = array('distance IS NOT NULL DESC', 'distance ASC');
		$records = $model->find('all', compact('fields', 'order', 'limit', 'conditions'));
		return $records;
	}
/**
 * Returns the equation that computes the difference between 2 points.
 */
 	function getEquation(&$model, $x, $y) {
 		extract($this->settings[$model->name]);
 		list($lat, $lon) = array($model->escapeField($lat), $model->escapeField($lon));
		return "(3958 * 3.1415926 * SQRT(({$lon} - {$y}) * ({$lon} - {$y}) + COS({$lon} / 57.29578) * COS({$y} / 57.29578) * ({$lat} - {$x}) * ({$lat} - {$x})) / 180)";		
 	}
/**
 * Custom find type
 */
 	function findDistance(&$model, $state, $query, $results) {
 		extract($this->settings[$model->name]);
 		list($lat, $lon) = array_values($query['from']);
		if ( 'before' == $state ) {
			
			$equation = $this->getEquation($model, $lat, $lon);
			if ( empty($query['fields']) ) {
				$query['fields'] = $this->_getDefaultFields($model);
			}
			$query['fields'][] = $equation . ' AS distance';
			
			if ( !empty($query['conditions']) ) {
				$newConditions = array();
				foreach ($query['conditions'] as $key => $val ) {
					$newConditions[str_replace('distance', $equation, $key)] = $val;
				}
				$query['conditions'] = $newConditions;
			}
			return $query;
		}
		else if ( 'after' == $state ) {
			return $results;
		}
 	}
 	
 	function _getDefaultFields(&$model) {
		$db = ConnectionManager::getDataSource($model->useDbConfig);
		$fields = $db->fields($model);
		foreach (array('hasOne', 'belongsTo') as $assoc) {
			$assocs = array_keys($model->$assoc);
			foreach ($assocs as $alias) {
				$fields = array_merge($fields, $db->fields($model->$alias));
			}
		}
		return $fields;
 	}
}

?>