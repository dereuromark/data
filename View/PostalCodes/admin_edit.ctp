<div class="page form">
<h2><?php echo __('Edit %s', __('Postal Code')); ?></h2>

<?php echo $this->Form->create('PostalCode');?>
	<fieldset>
		<legend><?php echo __('Edit %s', __('Postal Code')); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('code');
		echo $this->Form->input('country_id');
		echo $this->Form->input('lat');
		echo $this->Form->input('lng');
		echo $this->Form->input('official_address');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>

<div class="actions">
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('PostalCode.id')], ['confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('PostalCode.id'))]); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Postal Codes')), ['action' => 'index']);?></li>
	</ul>
</div>