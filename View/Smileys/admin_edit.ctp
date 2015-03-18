<div class="page form">
<?php echo $this->Form->create('Smiley');?>
	<fieldset>
				<legend><?php echo __('Edit %s', __('Smiley')); ?></legend>
	<?php
		echo $this->Form->input('id');
		//echo $this->Form->input('smiley_cat_id');
		echo $this->Form->input('smiley_path');
		echo $this->Form->input('title');
		echo $this->Form->input('prim_code');
		echo $this->Form->input('sec_code');
		echo $this->Form->input('is_base');
		echo $this->Form->input('sort');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('Smiley.id')], ['confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('Smiley.id'))]); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Smileys')), ['action' => 'index']);?></li>
	</ul>
</div>