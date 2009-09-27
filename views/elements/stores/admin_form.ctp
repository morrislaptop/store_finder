<?php
	echo $form->input('id');
	echo $form->input('Contact.id');
	echo $form->input('Contact.name');
	echo $form->input('Contact.email');
	echo $form->input('Contact.phone');
	echo $form->input('Contact.address1');
	echo $form->input('Contact.address2');
	echo $form->input('Contact.suburb');
	echo $form->input('Contact.postcode');
	echo $form->input('Contact.state', array('empty' => '- select -'));
	echo $form->input('Contact.city');
	echo $form->input('Contact.country');
	echo $form->input('Contact.country');
	
	echo $form->input('website');
	echo $form->input('display_address');
	echo $form->input('visible');
?>