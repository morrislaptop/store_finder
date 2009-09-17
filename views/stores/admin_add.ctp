<div class="stores form">
<?php echo $form->create('Store');?>
	<fieldset>
 		<legend><?php __('Add Store');?></legend>
		<?php echo $this->element('stores' . DS . 'admin_form'); ?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>