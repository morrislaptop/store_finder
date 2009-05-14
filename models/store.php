<?php
	class Store extends AppModel
	{
		var $name = 'Store';

		function findNearPostcode($postcode)
		{
			$sql = "SELECT
						Store.*
					FROM
						postcodes Postcode1, postcodes Postcode2, stores Store
					WHERE
						Postcode1.region = Postcode2.region
						AND Postcode2.pcode = Store.postcode
						AND Postcode1.pcode = '{$postcode}'
					GROUP BY
						Store.id
					ORDER BY
						Store.suburb, Store.name";
			return $this->query($sql);
		}
	}
?>
