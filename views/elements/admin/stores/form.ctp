<?php
	echo $html->css('forms', false, false, false);
	echo $advform->input('id');
	echo $advform->input('name');
	echo $advform->input('email');
	echo $advform->input('phone');
	echo $advform->input('website');
	echo $advform->input('address1');
	echo $advform->input('address2');
	echo $advform->input('suburb');
	echo $advform->input('postcode');
	echo $advform->input('state', array('empty' => '- select -'));
	echo $advform->input('country');
?>