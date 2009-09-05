<?php echo $html->css('tables', false, false, false); ?>
<div class="stores index">
<h2><?php __('Stores');?></h2>
<p><?php echo $advindex->export('Export as CSV'); ?> | <?php echo $html->link('Import from CSV', '#', array('onclick' => "\$('#StoreImportForm').toggle();")); ?></p>
<?php echo $this->element('import_form', array('plugin' => 'advindex', 'model' => 'Store')); ?>
<?php echo $advindex->create('Store'); ?>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php echo $paginator->sort('id'); ?></th>
			<th><?php echo $paginator->sort('name'); ?></th>
			<th><?php echo $paginator->sort('email'); ?></th>
			<th><?php echo $paginator->sort('suburb'); ?></th>
			<th><?php echo $paginator->sort('postcode'); ?></th>
			<th><?php echo $paginator->sort('state'); ?></th>
			<th><?php echo $paginator->sort('visible'); ?></th>
			<th><?php echo $paginator->sort('created'); ?></th>

		</tr>
		<tr class="filter">
			<td><?php echo $advindex->filter('id'); ?></td>
			<td><?php echo $advindex->filter('name'); ?></td>
			<td><?php echo $advindex->filter('email'); ?></td>
			<td><?php echo $advindex->filter('suburb'); ?></td>
			<td><?php echo $advindex->filter('postcode'); ?></td>
			<td><?php echo $advindex->filter('state'); ?></td>
			<td><?php echo $advindex->filter('visible'); ?></td>
			<td><?php echo $advindex->filter('created'); ?></td>
		</tr>
	</thead>
	<tbody>
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
					<?php echo $store['Store']['visible']; ?>
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
	</tbody>
	<?php echo $this->element('tfoot', array('plugin' => 'advindex')); ?>
</table>
<?php echo $advindex->end(); ?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Store', true), array('action'=>'add')); ?></li>
	</ul>
</div>
