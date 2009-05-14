<div class="stores form">
	<?php echo $uniform->create('Store');?>
		<fieldset>
 			<legend><?php __('Edit Store');?></legend>
			<?php echo $this->element('admin/stores/form'); ?>
		</fieldset>
		<div class="ctrlHolder buttonHolder">
			<?php echo $html->link(__('<< List Stores', true), array('action'=>'index'), array('class' => 'resetButton'));?>
			<?php echo $uniform->submit('Save', array('div' => false, 'class' => 'primaryAction', 'name' => 'saveEdit')); ?>
		</div>
	<?php echo $uniform->end();?>
</div>