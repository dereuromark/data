<?php
declare(strict_types = 1);

namespace Data\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TimezonesFixture
 */
class TimezonesFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'country_code' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => 'ISO_3166-2', 'precision' => null],
        'offset' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'offset_dst' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'type' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'lat' => ['type' => 'float', 'length' => 10, 'precision' => 6, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'lng' => ['type' => 'float', 'length' => 10, 'precision' => 6, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'covered' => ['type' => 'string', 'length' => 190, 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'notes' => ['type' => 'string', 'length' => 190, 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => ''],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ],
    ];
    // phpcs:enable
	/**
	 * Init method
	 *
	 * @return void
	 */
	public function init(): void {
		$this->records = [
			[
				'id' => 1,
				'name' => 'Lorem ipsum dolor sit amet',
				'country_code' => 'Lo',
				'offset' => 'Lorem ip',
				'offset_dst' => 'Lorem ip',
				'type' => 'Lorem ipsum dolor sit amet',
				'active' => 1,
				'lat' => 1,
				'lng' => 1,
				'covered' => 'Lorem ipsum dolor sit amet',
				'notes' => 'Lorem ipsum dolor sit amet',
				'created' => '2020-12-06 08:33:15',
				'modified' => '2020-12-06 08:33:15',
			],
		];
		parent::init();
	}

}