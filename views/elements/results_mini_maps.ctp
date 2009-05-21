<?php
	$javascript->link('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . Configure::read('StoreFinder.google_maps_api'), false);

	foreach ($stores as $store)
	{
		$id = $store['Store']['id'];
		?>
		<div class="store">
			<div class="details">
				<p><strong><?php echo $store['Store']['name']; ?></strong></p>
				<p>
					<?php echo $store['Store']['address']; ?><br />
					<?php echo $store['Store']['suburb']; ?><br />
					<?php echo $store['Store']['postcode']; ?> <?php echo $store['Store']['state']; ?><br />
				</p>
			</div>
			<div class="callout copy">
				<?php
					if ( $store['Store']['lon'] )
					{
						?>
						<div id="map<?php echo $id; ?>" class="storeMap frame"></div>
						<?php
					}
				?>
				<ul>
					<?php
						if ( $store['Store']['phone'] )
						{
							?>
							<li><?php echo $store['Store']['phone']; ?></li>
							<?php
						}
						if ( $store['Store']['email'] )
						{
							$url = 'mailto:' . $store['Store']['email'];
							$text = $store['Store']['email'];
							?>
							<li><?php echo $html->link($text, $url); ?></li>
							<?php
						}
						if ( $store['Store']['website'] )
						{
							$website = $store['Store']['website'];
							if ( substr($website, 0, 4) != 'http' ) {
								$text = $website;
								$url = 'http://' . $website;
							}
							else {
								$text = preg_replace('|https?://|', '', $website);
								$url = $website;
							}
							?>
							<li><?php echo $html->link($text, $url); ?></li>
							<?php
						}

						// put small map there.
						if ( $store['Store']['lon'] ) {
							echo $javascript->codeBlock('
								var map = new GMap2(document.getElementById("map' . $id . '"));
								var point = new GLatLng(' . $store['Store']['lat'] . ', ' . $store['Store']['lon'] . ');
								var marker = new GMarker(point);
								map.setCenter(point, 15);
								map.addOverlay(marker);
							');							
						}
					?>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}
?>