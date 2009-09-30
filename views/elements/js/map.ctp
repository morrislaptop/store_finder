var myOptions = {
	zoom: 4,
	mapTypeId: google.maps.MapTypeId.ROADMAP,
	center: new google.maps.LatLng(-25.335448, 135.745076)
}
var map = new google.maps.Map(document.getElementById("map"), myOptions);
<?php
	if ( !empty($stores) )
	{
		?>
		var bounds = new google.maps.LatLngBounds();
		<?php
			foreach ($stores as $store)
			{
				$infoContent = '<strong>' . $store['Contact']['name'] . '</strong><br />';
				$infoContent .= $store['Store']['display_address'] . '<br />';
				if ( $store['Store']['website'] ) {
					$infoContent .= '<br />' . $html->link($store['Store']['website']) . '<br />';
				}
				if ( $store['Contact']['suburb'] ) {
					$infoContent .= $store['Contact']['suburb'] . '<br />';
				}
				if ( $store['Contact']['postcode'] ) {
					$infoContent .= $store['Contact']['postcode'] . '<br />';
				}
				?>
				var latLng = new google.maps.LatLng(<?php echo $store['Store']['lat']; ?>, <?php echo $store['Store']['lon']; ?>);
				var marker = new google.maps.Marker({
					position: latLng, 
					map: map, 
					<?php echo !empty($marker) ? 'icon: "' . $html->webroot($marker, true) . '",' : ''; ?>
					title: "<?php echo $javascript->escapeString($store['Store']['display_address']); ?>"
				});
				
				google.maps.event.addListener(marker, "click", function(event) {
					var infowindow = new google.maps.InfoWindow({
					    content: "<?php echo $javascript->escapeString($infoContent); ?>"
					});
					infowindow.open(map, this);
				});
				
				bounds.extend(latLng);
				<?php
			}
		?>
		map.fitBounds(bounds);	
		<?php
	}
?>