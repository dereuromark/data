<?php
/**
 * @var \App\View\AppView $this
 * @var \Data\Model\Entity\Continent $continent
 */
?>
<h2><?php echo __('Add {0}', __('Continent')); ?></h2>

<div class="page form">
<?php echo $this->Form->create($continent);?>
	<fieldset>
		<legend><?php echo __('Add {0}', __('Continent')); ?></legend>
	<?php
		echo $this->Form->control('name');
		//echo $this->Form->control('ori_name');

		echo $this->Form->control('code');

		echo $this->Form->control('parent_id', []);
		//echo $this->Form->control('status');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>

<br/><br/>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List {0}', __('Continents')), ['action' => 'index']);?></li>
	</ul>
</div>
