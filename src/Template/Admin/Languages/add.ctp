<?php
/**
 * @var \App\View\AppView $this
 * @var \Data\Model\Entity\Language $language
 */
?>
<h2><?php echo __('Add {0}', __('Language')); ?></h2>

<div class="page form">
<?php echo $this->Form->create($language);?>
	<fieldset>
		<legend><?php echo __('Add {0}', __('Language')); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('ori_name');
		echo $this->Form->input('code');
		echo $this->Form->input('locale');
		echo $this->Form->input('locale_fallback');
		echo $this->Form->input('direction', ['options' => $language::directions()]);
		//echo $this->Form->input('status');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br/><br/>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List {0}', __('Languages')), ['action' => 'index']);?></li>
	</ul>
</div>