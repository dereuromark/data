<div class="page form">
<?php echo $this->Form->create('CountryProvince');?>
	<fieldset>
		<legend><?php echo __('Edit %s', __('CountryProvince'));?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('country_id', ['empty' => ' - [ ' . __('pleaseSelect') . ' ]- ', 'required' => 1]);
		echo $this->Form->input('name', ['required' => 1]);
		echo $this->Form->input('abbr');

	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('CountryProvince.id')], ['escape' => false, 'confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('CountryProvince.id'))]); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('CountryProvinces')), ['action' => 'index']);?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Countries')), ['controller' => 'countries', 'action' => 'index']); ?> </li>
		<li><?php echo $this->Html->link(__('Add %s', __('Country')), ['controller' => 'countries', 'action' => 'add']); ?> </li>
	</ul>
</div>