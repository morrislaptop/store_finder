<?php
	App::import('Vendor', 'StoreFinder.GoogleMapAPI', array('file' => 'GoogleMapAPI.class.php'));
	$javascript->link('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . Configure::read('StoreFinder.google_maps_api'), false); // dont use GoogleMapAPI for js file call
	$map = new GoogleMapAPI('map_wrap');
	$map->setBoundsFudge(0.07);
	$map->map_controls = true;
	$map->type_controls = false;
	$map->scale_control = false;
	$map->overview_control = false;
	$map->disableSidebar();

	if ( !isset($addIcons) ) {
		$addIcons = true;
	}

	if ( !$stores ) {
		$map->setZoomLevel(4);
		$map->setCenterCoords(142.745076, -30.335448);
	}
	$i = 1;
	foreach ($stores as $store)
	{
		if ( isset($store['Store']['lon']) ) {
			$lines = array(
				'<strong>' . $store['Store']['name'] . '</strong>',
				$store['Store']['display_address'],
				$store['Store']['suburb'],
				$store['Store']['postcode'] . ' ' . $store['Store']['state']
			);
			$map->addMarkerIcon('/img/markers/marker' . $i . '.png', '/img/markers/shadow.png');
			$map->addMarkerByCoords($store['Store']['lon'], $store['Store']['lat'], trim($store['Store']['name']), implode('<br />', $lines));
			$i++;
		}
	}

	$this->addScript($map->getMapJS());
	$javascript->codeBlock('$(window).load(onLoad);', array('inline' => false));
	echo $html->div('frame', ' ', array('id' => 'map_wrap'));
?>