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

	function setup(&$model, $config = array()) {
		$this->settings[$model->name] = am(array(
			'lookup'		=> 'google',
			'key'			=> null,
			'cacheTable'	=> 'geocodes',
			'fields'		=> array('lat', 'lon')
		), $config);
		extract($this->settings[$model->name]);

		if (!isset($model->Geocode)) {
			if (App::import('Model', 'Geocode')) {
				$model->Geocode =& new Geocode();
			} else {
				$model->Geocode =& new DynamicModel(array('name' => 'Geocode', 'table' => $cacheTable));
			}
		}

		if (!isset($this->lookupServices[low($lookup)])) {
			trigger_error('The lookup service "' . $lookup . '" does not exist.', E_USER_WARNING);
			return false;
		}
		if (!isset($this->connection)) {
			$this->connection = new HttpSocket();
		}
	}
/**
 * Get the geocode latitude/longitude points from given address.
 * Look in the cache first, otherwise get from web service (Google/Yahoo!)
 *
 * @param string $address
 */
	function geocode(&$model, $address) {
		extract($this->settings[$model->name]);

		if (is_array($address)) {
			$out = '';
			if (isset($address[$model->name])) {
				$address = $address[$model->name];
			}
			$vars = array('street', 'address', 'addr', 'address1', 'addr1', 'address2', 'addr2', 'apt', 'city', 'state', 'zip', 'zipcode', 'zip_code', 'postcode', 'pcode', 'country');
			foreach ($vars as $var) {
				if (isset($address[$var])) {
					$out = trim($out) . ' ' . $address[$var];
				}
			}
			$address = trim($out);
		}
		if (empty($address)) {
			// trigger_error
			return false;
		}

		if (!$code = $model->Geocode->findByAddress(low($address))) {
			if ($code = $this->_geocoords($model, $address)) {
				$model->Geocode->create();
				$model->Geocode->save(array('address' => low($address), 'lat' => $code[$fields[0]], 'lon' => $code[$fields[1]]));
			}
			else {
				$code = array();
			}
		} else {
			$code = array($fields[0] => $code['Geocode']['lat'], $fields[1] => $code['Geocode']['lon']);
		}
		return array_reverse($code);
	}
/**
 * Get geocode lat/lon points for given address from web service (Google/Yahoo!)
 *
 * @param string $address
 * @access private
 * @return array Latitude and longitude data, or false on failure
 */
	function _geocoords(&$model, $address) {
		extract($this->settings[$model->name]);

		$url = r(
			array('%key', '%address'),
			array($key, rawurlencode($address)),
			$this->lookupServices[low($lookup)][0]
		);

		$code = false;
		if($result = $this->connection->get($url)) {
			if (preg_match($this->lookupServices[low($lookup)][1], $result, $match)) {
				$code = array($fields[0] => floatval($match[1]), $fields[1] => floatval($match[2]));
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
	function findAllBydistance(&$model, $x, $y, $distance = null) {
		extract($this->settings[$model->name]);
		if (is_array($x)) {
			$distance = $y;
			list($x, $y) = array_values($x);
		}
		list($x2, $y2) = array($model->escapeField($fields[1]), $model->escapeField($fields[0]));
		list($x, $y, $distance) = array(floatval($x), floatval($y), floatval($distance));
		return $model->findAll("(3958 * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / 180) <= {$distance}");
	}
/**
 * Generates an SQL query to calculate the distance between the coordinates of each record and the given x/y values,
 * and compares the result to $distance.
 *
 * @param mixed $x  Either a float or an array.  If an array, it should contain the X and Y values of the coordinate.
 * @param mixed $y  If $x is an array, this value is used as $distance, otherwise, the Y coordinate.
 * @param float $distance  The distance (in miles) to search within
 */
	function findAllNearDistance(&$model, $x, $y, $limit = null) {
		extract($this->settings[$model->name]);
		if (is_array($x)) {
			$limit = $y;
			list($x, $y) = array_values($x);
		}
		list($x2, $y2) = array($model->escapeField($fields[1]), $model->escapeField($fields[0]));
		list($x, $y) = array(floatval($x), floatval($y));
		$fields = array(
			"(3958 * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / 180) as distance",
			$model->alias . '.*'
		);
		$order = array('distance IS NOT NULL DESC', 'distance ASC');
		$records = $model->find('all', compact('fields', 'order', 'limit', 'conditions'));
		return $records;
	}
}

class DynamicModel extends AppModel {

	function __construct($options = array()) {
		if (is_string($options)) {
			$options = array('name' => $options);
		}
		if (!isset($options['name'])) {
			return null;
		}
		$options = am(array(
			'id' => false,
			'table' => null,
			'ds' => null
		), $options);

		$this->name = $options['name'];
		parent::__construct($options['id'], $options['table'], $options['ds']);
	}
}

?>