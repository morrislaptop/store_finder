<?php
	if ( $stores ) {
		App::import('Vendor', 'StoreFinder.GoogleMapAPI', array('file' => 'GoogleMapAPI.class.php'));
		$javascript->link('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . Configure::read('StoreFinder.google_maps_api'), false); // dont use GoogleMapAPI for js file call
		$map = new GoogleMapAPI('map_wrap');
		$map->map_controls = false;
		$map->type_controls = false;
		$map->scale_control = false;
		$map->overview_control = false;

		foreach ($stores as $store)
		{
			if ( isset($store['Store']['lon']) ) {
				$lines = array(
					'<strong>' . $store['Store']['name'] . '</strong>',
					$store['Store']['address'],
					$store['Store']['suburb'],
					$store['Store']['postcode'] . ' ' . $store['Store']['state']
				);
				$map->addMarkerByCoords($store['Store']['lon'], $store['Store']['lat'], trim($store['Store']['name']), implode('<br />', $lines));
			}
		}

		$this->addScript($map->getMapJS());
		$javascript->codeBlock('$(function() { onLoad(); })', array('inline' => false));
		
		echo $html->div('frame', ' ', array('id' => 'map_wrap'));
	}
?>