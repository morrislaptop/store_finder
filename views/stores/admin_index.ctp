<div class="stores index">
<h2><?php __('Stores');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<p>
	<?php echo $advindex->export('Export as CSV'); ?>
	|
	<?php echo $html->link('Import from CSV', '#', array('onclick' => "\$('#StoreImportForm').toggle();")); ?>
</p>
<?php echo $advindex->import('Store', array('style' => 'display: none;')); ?>
<?php echo $advindex->create('Store'); ?>
<p> Display <?php echo $advindex->perPage(); ?> per page</p>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php echo $paginator->sort('id');?></th>
			<th><?php echo $paginator->sort('name');?></th>
			<th><?php echo $paginator->sort('email');?></th>
			<th><?php echo $paginator->sort('suburb');?></th>
			<th><?php echo $paginator->sort('postcode');?></th>
			<th><?php echo $paginator->sort('state');?></th>
			<th><?php echo $paginator->sort('created');?></th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<tr>
			<th><?php echo $advindex->filter('id');?></th>
			<th><?php echo $advindex->filter('name');?></th>
			<th><?php echo $advindex->filter('email');?></th>
			<th><?php echo $advindex->filter('suburb');?></th>
			<th><?php echo $advindex->filter('postcode');?></th>
			<th><?php echo $advindex->filter('state');?></th>
			<th><?php echo $advindex->filter('Store.created', array('type' => 'date'));?></th>
			<th class="actions"><?php echo $advindex->search(); ?></th>
		</tr>
	</thead>
<?php
$i = 0;
foreach ($stores as $store):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $store['Store']['id']; ?>
		</td>
		<td>
			<?php echo $store['Store']['name']; ?>
		</td>
		<td>
			<?php echo $store['Store']['email']; ?>
		</td>
		<td>
			<?php echo $store['Store']['suburb']; ?>
		</td>
		<td>
			<?php echo $store['Store']['postcode']; ?>
		</td>
		<td>
			<?php echo $store['Store']['state']; ?>
		</td>
		<td>
			<?php echo $store['Store']['created']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $store['Store']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $store['Store']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $store['Store']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $advindex->end(); ?>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Store', true), array('action'=>'add')); ?></li>
	</ul>
</div>
