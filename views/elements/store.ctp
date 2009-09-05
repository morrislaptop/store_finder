<?php
	$store = $data;
	$id = $store['Store']['id'];
?>
<td class="store">
	<div class="details">
		<p>
			<?php
				if ( !isset($store['Store']['ilve']) || !$store['Store']['ilve'] ) {
					echo $html->image('markers/marker' . ($i-1) . '.png', array('style' => 'vertical-align: middle;'));
				}
			?>
			<strong><?php echo $store['Store']['name']; ?></strong>
		</p>
		<p>
			<?php echo $store['Store']['display_address']; ?><br />
			<?php echo $store['Store']['suburb']; ?><br />
			<?php echo $store['Store']['postcode']; ?> <?php echo $store['Store']['state']; ?><br />
		</p>
	</div>
	<div class="callout copy">
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
			?>
		</ul>
	</div>
	<div class="clear"></div>
</td>