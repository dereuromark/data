<?php
namespace Data\Model\Table;

use Geo\Geocoder\Geocoder;
use Tools\Lib\GeocodeLib;
use Tools\Model\Table\Table;

class StatesTable extends Table {

	public $actsAs = ['Tools.Slugged' => ['case' => 'low', 'mode' => 'ascii', 'unique' => false, 'overwrite' => false]];

	public $order = ['name' => 'ASC'];

	public $validate = [
		'country_id' => ['numeric'],
		'abbr' => [
			'validateUnique' => [
				'rule' => ['validateUnique', ['country_id']],
				'message' => 'valErrRecordNameExists',
				'allowEmpty' => true
			],
		],
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'valErrMandatoryField',
				'last' => true
			],
			'isUnique' => [
				'rule' => ['validateUnique', ['country_id']],
				'message' => 'valErrRecordNameExists',
			],
		],
	];

	public $hasMany = [
		'County' => [
			'className' => 'Data.County',
			'foreignKey' => 'state_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
		]
	];

	public $belongsTo = [
		'Country' => [
			'className' => 'Data.Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		]
	];

	public function getStateId($conditions) {
		if ($id = $this->field('id', $conditions)) {
			return $id;
		}
		$this->create();
		$this->set($conditions);

		if ($this->save(null, false)) {
			return $this->id;
		}

		die('ERROR: ' . returns($this->validationErrors));
	}

	/**
	 * @param int $cid
	 *
	 * @return array|\Cake\ORM\Query
	 */
	public function getListByCountry($cid) {
		if (empty($cid)) {
			return [];
		}
		return $this->find('list', [
			'conditions' => [$this->alias() . '.country_id' => $cid],
			'order' => [$this->alias() . '.name' => 'ASC']
		]);
	}

	public function afterSave($created, $options = []) {
		if ($created) {
			$this->updateCoordinates($this->id);
		}
	}

	/**
	 * Lat and lng + abbr if available!
	 *
	 * @param id|null
	 * - NULL: update all records with missing coordinates only
	 * - otherwise: specific update
	 */
	public function updateCoordinates($id = null) {
		$geocoder = new Geocoder();

		$override = false;
		if ($id == -1) {
			$id = '';
			$override = true;
		}

		if (!empty($id)) {
			$res = $this->find('first', ['conditions' => [$this->alias() . '.id' => $id], 'contain' => ['Country.name']]);
			if (!empty($res['name']) && !empty($res[$this->Countries->alias]['name']) && $geocoder->geocode($res['name'] .
				', ' . $res[$this->Countries->alias]['name'])) {

				$data = $geocoder->getResult();
				//pr($data); die();
				$saveArray = ['id' => $id, 'lat' => $data['lat'], 'lng' => $data['lng'], 'country_id' => $res['country_id']];

				if (!empty($data['country_province_code']) && mb_strlen($data['country_province_code']) <= 3 && preg_match('/^([A-Z])*$/', $data['country_province_code'])) {
					$saveArray['abbr'] = $data['country_province_code'];
				}

				$this->id = $id;
				if (!$this->save($saveArray, true, ['id', 'lat', 'lng', 'abbr', 'country_id'])) {
					if ($data['country_province_code'] !== 'DC') {
						echo returns($this->id);
						pr($res);
						pr($data);
						pr($saveArray);
						die(returns($this->validationErrors));
					}
				}
				return true;
			}
		} else {

			$conditions = [];
			if (!$override) {
				$conditions = [$this->alias() . '.lat' => 0, $this->alias() . '.lng' => 0];
			}

			$results = $this->find('all', ['conditions' => $conditions, 'contain' => ['Country.name'], 'order' => ['CountryProvince.modified' =>
				'ASC']]);
			$count = 0;

			foreach ($results as $res) {
				if (!empty($res['name']) && !empty($res[$this->Countries->alias]['name']) && $geocoder->geocode($res['name'] .
					', ' . $res[$this->Countries->alias]['name'])) {

					$data = $geocoder->getResult();
					//pr($data); die();
					//pr ($geocoder->debug());
					$saveArray = ['id' => $res['id'], 'country_id' => $res['country_id']];
					if (isset($data['lat']) && isset($data['lng'])) {
						$saveArray = array_merge($saveArray, ['lat' => $data['lat'], 'lng' => $data['lng']]);
					}

					if (!empty($data['country_province_code']) && mb_strlen($data['country_province_code']) <= 3 && preg_match('/^([A-Z])*$/', $data['country_province_code'])) {
						$saveArray['abbr'] = $data['country_province_code'];
					}

					$this->id = $res['id'];
					if ($this->save($saveArray, true, ['lat', 'lng', 'abbr', 'country_id'])) {
						$count++;

						if (!empty($saveArray['abbr']) && $saveArray['abbr'] != $res['abbr']) {
							$this->log('Abbr for country province \'' . $data['country_province'] . '\' changed from \'' . $res['abbr'] . '\' to \'' . $saveArray['abbr'] .
								'\'', LOG_NOTICE);
						}

					} else {
						//pr($data); pr($geocoder->debug()); die();

						if ($data['country_province_code'] !== 'DC') {
							echo returns($this->id);
							pr($res);
							pr($data);
							pr($saveArray);
							die(returns($this->validationErrors));
						}
					}
					$geocoder->pause();
				}
			}

			return $count;
		}

		return false;
	}

}
