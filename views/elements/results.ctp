<?php
	$states = array();

	foreach ($stores as $store)
	{
		if ( !isset($states[$store['Store']['state']]) ) {
			$states[$store['Store']['state']] = array();
		}
		$states[$store['Store']['state']][] = $this->element('store', array('plugin' => 'store_finder', 'store' => $store));
	}

	foreach ($states as $state => $stores)
	{
		?>
		<h2><?php echo $state; ?></h2>
		<?php echo implode("\n\n", $stores); ?>
		<?php
	}
?>