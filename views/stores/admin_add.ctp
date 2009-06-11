<div class="stores form">
<?php echo $advform->create('Store');?>
	<fieldset>
 		<legend><?php __('Add Store');?></legend>
		<?php echo $this->element('admin/stores/form'); ?>
	</fieldset>
<?php echo $advform->end('Submit');?>
</div>