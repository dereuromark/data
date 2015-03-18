<h2><?php echo __('Edit %s', __('Language')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('Language');?>
	<fieldset>
		<legend><?php echo __('Edit %s', __('Language')); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('ori_name');
		echo $this->Form->input('code');
		echo $this->Form->input('locale');
		echo $this->Form->input('locale_fallback');
		echo $this->Form->input('direction', ['options' => Language::directions()]);
		echo $this->Form->input('status');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('Language.id')], ['confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('Language.id'))]); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Languages')), ['action' => 'index']);?></li>
	</ul>
</div>