<h2><?php echo __('Add {0}', __('Address')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('Address');?>
	<fieldset>
		<legend><?php echo __('Add {0}', __('Address')); ?></legend>
	<?php
		echo $this->Form->input('country_id', array('empty' => array(0 => ' - [ ' . __('noSelection') . ' ] - ')));
	if (Configure::read('Address.CountryProvince')) {
		echo $this->Form->input('country_province_id', array('empty' => array(0 => ' - [ ' . __('noSelection') . ' ] - ')));
	}
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('street');
		echo $this->Form->input('postal_code');
		echo $this->Form->input('city');
	?>
	</fieldset>
	<fieldset>
		<legend><?php echo __('Relations'); ?></legend>
	<?php
		echo $this->Form->input('model');
		echo $this->Form->input('foreign_id', array('type' => 'text', 'empty' => array(0 => ' - [ ' . __('noSelection') . ' ] - ')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br/><br/>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List {0}', __('Addresses')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List {0}', __('Countries')), array('controller' => 'countries', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List {0}', __('Country Provinces')), array('controller' => 'country_provinces', 'action' => 'index')); ?> </li>
	</ul>
</div>