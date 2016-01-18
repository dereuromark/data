<?php
App::uses('DataAppModel', 'Data.Model');
App::uses('GeocodeLib', 'Data.Lib');
App::uses('GeolocateLib', 'Tools.Lib');

class Country extends DataAppModel {

	public $order = ['sort' => 'DESC', 'name' => 'ASC'];

	public $actsAs = ['Tools.Sortable'];

	public $validate = [
		'name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				'message' => 'Manditory field',
				'last' => true,
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'record (with this name) already exists',
			],
		],
		'ori_name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				'message' => 'Manditory field',
				'last' => true,
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'record (with this name) already exists',
			],
		],
		'iso2' => [
			'isUnique' => [
				'rule' => ['isUnique'],
				'allowEmpty' => true,
				'message' => 'record (with this name) already exists',
			],
		],
		'iso3' => [
			'isUnique' => [
				'rule' => ['isUnique'],
				'allowEmpty' => true,
				'message' => 'record (with this name) already exists',
			],
		],
		'country_code' => ['numeric'],
		//'special' => array('notEmpty'),
		//'sort' => array('numeric')
	];

	public $hasMany = [
		'CountryProvince' => [
			'className' => 'Data.CountryProvince',
			'foreignKey' => 'country_id',
			'dependent' => true, # !!!
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
		],
		'State' => [
			'className' => 'Data.State',
			'foreignKey' => 'country_id',
			'dependent' => true, # !!!
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
		],
/*
		'Location' => array(
			'className' => 'Location',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
		)
*/
	];

	public $filterArgs = [
		'search' => ['type' => 'like', 'field' => ['name', 'ori_name', 'iso2', 'iso3', 'country_code']],
		//'status' => array('type' => 'value')
	];

	public function __construct($id = false, $table = false, $ds = null) {
		if (Configure::read('Country.provinces') === false) {
			unset($this->hasMany['CountryProvince']);
		}
		if (Configure::read('Country.State') !== true) {
			unset($this->hasMany['State']);
		}

		parent::__construct($id, $table, $ds);
	}

	/**
	 * Country::active()
	 *
	 * @param string $type
	 * @param mixed $customOptions
	 * @return array
	 */
	public function active($type = 'all', $customOptions = []) {
		$options = ['conditions' => [$this->alias . '.status' => 1], ];
		if (!empty($customOptions)) {
			$options = array_merge($options, $customOptions);
		}
		return $this->find($type, $options);
	}

	/**
	 * Country::getList()
	 *
	 * @return array
	 */
	public function getList() {
		$options = [
			//'conditions'=>array('active'=>1),
		];
		return $this->find('list', $options);
	}

	/**
	 * Lat and lng + abbr if available!
	 *
	 * @param id
	 * - NULL: update all records with missing coordinates only
	 * - otherwise: specific update
	 * @return bool Success
	 */
	public function updateCoordinatesNew($id = null) {
		$Geocoder = new GeocodeLib();

		$override = false;
		if ($id == -1) {
			$id = '';
			$override = true;
		}

		if (!empty($id)) {
			$res = $this->find('first', ['conditions' => [$this->alias . '.id' => $id], 'contain' => []]);
			if (!empty($res[$this->alias]['ori_name']) && $Geocoder->geocode($res[$this->alias]['ori_name']) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && $Geocoder->geocode($res[$this->alias]['name'])) {

				$data = $Geocoder->getResult();
				//pr($data); die();
				$saveArray = ['lat' => $data['lat'], 'lng' => $data['lng']];

				if (!empty($data['country_code']) && mb_strlen($data['country_code']) === 3 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
					$saveArray['iso3'] = $data['country_code'];
					throw new CakeException(print_r($saveArray, true));

				} elseif (!empty($data['country_code']) && mb_strlen($data['country_code']) === 2 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
					$saveArray['iso2'] = $data['country_code'];
					throw new CakeException(print_r($saveArray, true));
				}

				$this->id = $id;
				if (!$this->save($saveArray, ['fieldList' => ['lat', 'lng', 'iso2', 'iso3']])) {
					throw new CakeException($this->id);
				}
				return true;
			}
		} else {

			$conditions = [];
			if (!$override) {
				$conditions = [$this->alias . '.lat' => 0, $this->alias . '.lng' => 0];
			}

			$results = $this->find('all', ['conditions' => $conditions, 'contain' => []]);

			$count = 0;
			foreach ($results as $res) {
				if (!empty($res[$this->alias]['ori_name']) && $Geocoder->geocode($res[$this->alias]['ori_name']) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && $Geocoder->geocode($res[$this->alias]['name'])) {

					$data = $Geocoder->getResult();
					throw new CakeException('todo');
					# seems to be very problematic: country "Georgien" results in "Georgia, USA"

					$saveArray = [];
					if (isset($data['lat']) && isset($data['lng'])) {
						$saveArray = ['lat' => $data['lat'], 'lng' => $data['lng']];
					}

					if (!empty($data['country_code']) && mb_strlen($data['country_code']) === 3 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
						$saveArray['iso3'] = $data['country_code'];
						//die(print_r($saveArray));

					} elseif (!empty($data['country_code']) && mb_strlen($data['country_code']) === 2 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
						$saveArray['iso2'] = $data['country_code'];
						//die(print_r($saveArray));
					}

					$this->id = $res[$this->alias]['id'];
					if ($this->save($saveArray, ['fieldList' => ['lat', 'lng', 'iso2', 'iso3']])) {
						$count++;

						if (!empty($saveArray['iso2']) && $saveArray['iso2'] != $res[$this->alias]['iso2']) {
				$this->log('Iso2 for country \'' . $data['country'] . '\' changed from \'' . $res[$this->alias]['iso2'] . '\' to \'' . $saveArray['iso2'] . '\'', LOG_NOTICE);
					}
					if (!empty($saveArray['iso3']) && $saveArray['iso3'] != $res[$this->alias]['iso3']) {
				$this->log('Iso3 for country \'' . $data['country'] . '\' changed from \'' . $res[$this->alias]['iso3'] . '\' to \'' . $saveArray['iso3'] . '\'', LOG_NOTICE);
					}

					} else {
						//pr($data); pr($Geocoder->debug()); die();
					}
					$Geocoder->pause();
				}
			}

			return $count;
		}

		return false;
	}

	//TODO: test

	public function updateAbbr($id = null) {
		$Geocoder = new GeocodeLib();

		$override = false;
		if ($id == -1) {
			$id = '';
			$override = true;
		}

		if (!empty($id)) {
			$res = $this->find('first', ['conditions' => [$this->alias . '.id' => $id], 'contain' => []]);
			if (!empty($res[$this->alias]['ori_name']) && $Geocoder->geocode($res[$this->alias]['ori_name']) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && $Geocoder->geocode($res[$this->alias]['name'])) {

			}
		} else {
			$conditions = [];
			if (!$override) {
				$conditions = [$this->alias . '.iso2' => '']; # right now only iso2
			}

			$results = $this->find('all', ['conditions' => $conditions, 'contain' => []]);

			$count = 0;
			foreach ($results as $res) {
				if (!empty($res[$this->alias]['ori_name']) && $Geocoder->geocode($res[$this->alias]['ori_name']) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && $Geocoder->geocode($res[$this->alias]['name'])) {

					$data = $Geocoder->getResult();
					throw new CakeException('todo');
					# seems to be very problematic: country "Georgien" results in "Georgia, USA"

					$saveArray = [];
					if (!empty($data['country_code']) && mb_strlen($data['country_code']) === 3 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
						$saveArray['iso3'] = $data['country_code'];
						//die(print_r($saveArray));

					} elseif (!empty($data['country_code']) && mb_strlen($data['country_code']) === 2 && preg_match('/^([A-Z])*$/', $data['country_code'])) {
						$saveArray['iso2'] = $data['country_code'];
						//die(print_r($saveArray));
					}

					$this->id = $res[$this->alias]['id'];
					if ($this->save($saveArray, ['fieldList' => ['iso2', 'iso3']])) {
						$count++;

						if (!empty($saveArray['iso2']) && $saveArray['iso2'] != $res[$this->alias]['iso2']) {
				$this->log('Iso2 for country \'' . $data['country'] . '\' changed from \'' . $res[$this->alias]['iso2'] . '\' to \'' . $saveArray['iso2'] . '\'', LOG_NOTICE);
					}
					if (!empty($saveArray['iso3']) && $saveArray['iso3'] != $res[$this->alias]['iso3']) {
				$this->log('Iso3 for country \'' . $data['country'] . '\' changed from \'' . $res[$this->alias]['iso3'] . '\' to \'' . $saveArray['iso3'] . '\'', LOG_NOTICE);
					}

					} else {
						//pr($data); pr($Geocoder->debug()); die();
					}
					$Geocoder->pause();
				}
			}

			return $count;
		}

		return false;
	}

	/**
	 * @param id
	 * - NULL: update all records with missing coordinates only
	 * - otherwise: specific update
	 * @deprecated but seems to have better lat/lng for countries...
	 */
	public function updateCoordinates($id = null) {
		$Geocoder = new GeocodeLib();
		//$Geocoder->setup();

		$override = false;
		if ($id == -1) {
			$id = '';
			$override = true;
		}

		$conditions = [];
		if (!$override) {
			$conditions = [$this->alias . '.lat' => 0, $this->alias . '.lng' => 0];
		}

		if (!empty($id)) {
			$res = $this->find('first', ['fields' => [$this->alias . '.id', $this->alias . '.name', $this->alias . '.ori_name'], 'conditions' => [$this->alias . '.id' => $id], 'contain' => []]);
			if (!empty($res[$this->alias]['ori_name']) && ($data = $Geocoder->geocode($res[$this->alias]['ori_name'])) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && ($data = $Geocoder->geocode($res[$this->alias]['name']))) {

				//echo print_r($data); echo print_r($Geocoder->debug()); die();

				$this->id = $id;
				$this->save($data, ['fieldList' => ['lat', 'lng']]);
				return true;
			}
		} else {

			$results = $this->find('all', ['fields' => [$this->alias . '.id', $this->alias . '.name', $this->alias . '.ori_name'], 'conditions' => $conditions, 'contain' => []]);
			$count = 0;
			foreach ($results as $res) {
				if (!empty($res[$this->alias]['ori_name']) && ($data = $Geocoder->geocode($res[$this->alias]['ori_name'])) || $res[$this->alias]['name'] != $res[$this->alias]['ori_name'] && ($data = $Geocoder->geocode($res[$this->alias]['name']))) {
					//echo print_r($data); echo print_r($Geocoder->debug()); die();

					$this->id = $res[$this->alias]['id'];
					if ($this->save($data, ['fieldList' => ['lat', 'lng']])) {
						$count++;
					} else {
						//echo print_r($data); echo print_r($Geocoder->debug()); die();
					}
					$Geocoder->pause();
				}
			}

			return $count;
		}

		//$this->save($data, true, array('city','id','lat','lng'));
		return false;
	}

	/**
	 * Try to guess the country of the user
	 * - time sensitive (only works in a certain timeframe < 24h)
	 *
	 * @return int country or -1 on error
	 */
	public function guessByIp($ip = null) {
		$this->GeolocateLib = new GeolocateLib();
		if ($this->GeolocateLib->locate($ip)) {
			$country = $this->GeolocateLib->getResult('country_code'); # iso2
			if (!empty($country)) {
				$c = $this->fieldByConditions('id', [$this->alias . '.iso2' => $country]);
				if (!empty($c)) {
					return $c;
				}
			}
		}
		return -1;
	}

	public function afterSave($created, $options = []) {
		if ($created) {
			$this->updateCoordinates($this->id);
		}
	}

	//not in use

	public function getIdByIso($isoCode) {
		$match = ['DE' => 1, 'AT' => 2, 'CH' => 3];

		if (array_key_exists($isoCode = strtoupper($isoCode), $match)) {
			return $match[$isoCode];
		}
		return 0;
	}

	//not in use

	public function getIsoById($id, $default = '') {
		$match = [1 => 'DE', 2 => 'AT', 3 => 'CH'];

		if (array_key_exists($id, $match)) {
			return $match[$id];
		}
		return $default;
	}

	public function getDefaultCountry() {
		return $this->getIdByIso(TLD);
	}

	const STATUS_ACTIVE = 1;

	const STATUS_INACTIVE = 0;

}
