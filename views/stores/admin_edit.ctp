<?php
	$javascript->link('http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . Configure::read('StoreFinder.google_maps_api'), false);
	$javascript->link('/store_finder/js/gmaps.point', false);
	$this->viewVars['body'] = array('onload' => 'load(new GLatLng(' . $this->data['Store']['lat'] . ', ' . $this->data['Store']['lon'] . '));', 'onunload' => 'GUnload();');
?>
<div class="stores form">
<?php echo $advform->create('Store');?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td width="50%">
				<fieldset>
 					<legend><?php __('Edit Store');?></legend>
					<?php echo $this->element('admin/stores/form'); ?>
				</fieldset>
			</td>
			<td width="50%">
				<div id="map" style="width: 400px; height: 400px; border: 1px solid #eaeaea; margin-right: 30px;">map not loaded?</div>
				<p>Marker not in the right spot? Use the map as normal and click on or drag the marker to a new spot to adjust the address.</p>
				<?php echo $advform->input('lat'); ?>
				<?php echo $advform->input('lon'); ?>
			</td>
		</table>
<?php echo $advform->end('Submit');?>
</div>