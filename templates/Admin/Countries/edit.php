<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="page form">
<?php echo $this->Form->create($country);?>
	<fieldset>
		<legend><?php echo __('Edit {0}', __('Country'));?></legend>
	<?php
		//echo $this->Form->control('id');
		echo $this->Form->control('name');
		echo $this->Form->control('ori_name');
		echo $this->Form->control('iso2');
		echo $this->Form->control('iso3');
		echo $this->Form->control('country_code');
		echo $this->Form->control('special');
		echo $this->Form->control('address_format', ['type' => 'textarea']);
		echo '<div class="input checkbox">Platzhalter sind :name :street_address :postcode :city :country</div>';
		echo '<br/>';

		//echo $this->Form->control('sort');
		echo $this->Form->control('status', ['type' => 'checkbox', 'label' => 'Aktiv']);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br/><br/>

<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->getSourceValue('Country.id')], ['escape' => false], __('Are you sure you want to delete # {0}?', $this->Form->getSourceValue('Country.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List {0}', __('Countries')), ['action' => 'index']);?></li>
	</ul>
</div>