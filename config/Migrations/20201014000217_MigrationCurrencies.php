<?php
declare(strict_types = 1);

use Migrations\AbstractMigration;

class MigrationCurrencies extends AbstractMigration {

	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 *
	 * @return void
	 */
	public function change() {
		$this->table('currencies')
			->addColumn('id', 'integer', [
				'autoIncrement' => true,
				'default' => null,
				'limit' => null,
				'null' => false,
				'signed' => false,
			])
			->addPrimaryKey(['id'])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 190,
				'null' => false,
			])
			->addColumn('code', 'char', [
				'default' => null,
				'limit' => 3,
				'null' => false,
			])
			->addColumn('symbol_left', 'string', [
				'default' => null,
				'limit' => 12,
				'null' => true,
			])
			->addColumn('symbol_right', 'string', [
				'default' => null,
				'limit' => 12,
				'null' => true,
			])
			->addColumn('decimal_places', 'char', [
				'default' => null,
				'limit' => 1,
				'null' => true,
			])
			->addColumn('value', 'float', [
				'default' => null,
				'null' => true,
				'precision' => 9,
				'scale' => 4,
			])
			->addColumn('base', 'boolean', [
				'comment' => 'is base currency',
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addColumn('modified', 'datetime', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->create();
	}

}
