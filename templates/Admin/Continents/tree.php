<?php
/**
 * @var \App\View\AppView $this
 * @var \Data\Model\Entity\Continent[]|\Cake\Collection\CollectionInterface $continents
 * @var int[] $countries
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
	<ul class="side-nav nav nav-pills flex-column">
		<li class="nav-item heading"><?= __('Actions') ?></li>
	</ul>
</nav>
<div class="cities index content large-9 medium-8 columns col-sm-8 col-12">

	<h1><?= __('Tree') ?></h1>

	<div class="tree">
		<?php
		$this->Continent->setConfig('countries', $countries);
		?>
		<?php echo $this->Tree->generate($continents, ['callback' => [$this->Continent, 'format']]); ?>
	</div>

</div>
