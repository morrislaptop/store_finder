<?php
	$javascript->link('http://maps.google.com/maps/api/js?sensor=false', false);
	$javascript->link('/store_finder/js/gmaps.point', false);
	$javascript->codeBlock('	
		$(function() {
		    var myLatlng = new google.maps.LatLng(' . $this->data['Store']['lat'] . ', ' . $this->data['Store']['lon'] . ');
		    var myOptions = {
		      zoom: 15,
		      center: myLatlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    }
		    var map = new google.maps.Map(document.getElementById("map"), myOptions);		    
		    
		    marker = new google.maps.Marker({
		        position: myLatlng, 
		        map: map,
		        title:"Hello World!",
		        draggable: true,
		        dragCrossMove: true,
		        clickable: false
		    }); 
		    
			google.maps.event.addListener(marker, "dragend", function(event) {
				setForm(event.latLng);
			});
			google.maps.event.addListener(map, "click", function(event) {
				setForm(event.latLng);
				marker.setPosition(event.latLng);
			});
		});
		
		function setForm(point) {
			$("#StoreLat").val(point.lat());
			$("#StoreLon").val(point.lng());
		}
	', array('inline' => false));

?>
<div class="stores form">
<?php echo $form->create('Store');?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td width="50%">
				<fieldset>
 					<legend><?php __('Edit Store');?></legend>
					<?php echo $this->element('stores' . DS . 'form'); ?>
				</fieldset>
			</td>
			<td width="50%">
				<div id="map" style="width: 400px; height: 400px; border: 1px solid #eaeaea; margin-right: 30px;">map not loaded?</div>
				<p>Marker not in the right spot? Use the map as normal and click on or drag the marker to a new spot to adjust the address.</p>
				<?php echo $advform->input('lat'); ?>
				<?php echo $advform->input('lon'); ?>
			</td>
		</table>
<?php echo $form->end('Submit');?>
</div>