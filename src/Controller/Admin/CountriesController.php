<?php
namespace Data\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Data\Controller\DataAppController;
use Cake\Event\Event;

class CountriesController extends DataAppController {

	public $paginate = ['order' => ['Country.sort' => 'DESC']];

	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

		if ($specific = Configure::read('Country.image_path')) {
			$this->imageFolder = WWW_ROOT . 'img' . DS . $specific . DS;
		} else {
			$this->imageFolder = Plugin::path('Data') . DS . 'webroot' . DS . 'img' . DS . 'country_flags' . DS;
		}

	}

	/**
	 * CountriesController::admin_update_coordinates()
	 *
	 * @param mixed $id
	 * @return void
	 */
	public function update_coordinates($id = null) {
		set_time_limit(120);
		$res = $this->Countries->updateCoordinates($id);
		if (!$res) {
			$this->Flash->error(__('coordinates not updated'));
		} else {
			$this->Flash->success(__('coordinates {0} updated', $res));
		}

		$this->autoRender = false;
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Check for missing or unused country flag icons
	 *
	 * @return void
	 */
	public function icons() {
		$icons = $this->_icons();

		$countries = $this->Countries->find('all', ['fields' => ['id', 'name', 'iso2', 'iso3']]);

		$usedIcons = [];

		# countries without icons
		$contriesWithoutIcons = [];
		foreach ($countries as $country) {
			$icon = strtoupper($country['Country']['iso2']);
			if (!in_array($icon, $icons)) {
				$contriesWithoutIcons[] = $country;
			} else {
				$key = array_keys($icons, $icon);
				$usedIcons[] = $icons[$key[0]];
			}
		}

		# icons without countries
		$iconsWithoutCountries = [];
		$iconsWithoutCountries = array_diff($icons, $usedIcons);
		//pr($iconsWithoutCountries);

		$this->set(compact('icons', 'countries', 'contriesWithoutIcons', 'iconsWithoutCountries'));
	}

	/**
	 * CountriesController::admin_import()
	 *
	 * @return void
	 */
	public function import() {
		if ($this->Common->isPosted()) {

			if (!empty($this->request->data['Form'])) {
				$count = 0;
				foreach ($this->request->data['Form'] as $key => $val) {
					$this->Countries->create();
					$data = ['iso3' => $val['iso3'], 'iso2' => $val['iso2'], 'name' => $val['name']];
					if (empty($val['confirm'])) {
						# do nothing
					} elseif ($this->Countries->save($data)) {
						$count++;
						unset($this->request->data['Form'][$key]);
					} else {
						//$this->request->data['Form'][$key]['confirm'] = 0;
						$this->request->data['Error'][$key] = $this->Countries->validationErrors;
					}

				}
				$this->Flash->success(__('record import {0} saved', $count));

			} else {

				$list = $this->request->data['Country']['import_content'];

				if (!empty($this->request->data['Country']['import_separator_custom'])) {
					$separator = $this->request->data['Country']['import_separator_custom'];
					$separator = str_replace(['{SPACE}', '{TAB}'], [Country::separators(SEPARATOR_SPACE, true), Country::separators(SEPARATOR_TAB, true)], $separator);

				} else {
					$separator = $this->request->data['Country']['import_separator'];
					$separator = Country::separators($separator, true);
				}
				# separate list into single records

				$countries = CommonComponent::parseList($list, $separator, false, false);
				if (empty($countries)) {
					$this->Countries->invalidate('import_separator', 'falscher Separator');
				} elseif (!empty($this->request->data['Country']['import_pattern'])) {
					$pattern = str_replace(['{SPACE}', '{TAB}'], [Country::separators(SEPARATOR_SPACE, true), Country::separators(SEPARATOR_TAB, true)], $this->request->data['Country']['import_pattern']);
					# select part that matches %name
					foreach ($countries as $key => $danceStep) {
						$tmp = sscanf($danceStep, $pattern); # returns array
						# write back into $countries array
						if (!empty($tmp[2])) {
							$this->request->data['Form'][$key] = ['name' => $tmp[2], 'confirm' => 1];
							if (!empty($tmp[1])) {
								$this->request->data['Form'][$key]['iso2'] = $tmp[1];
							}
							if (!empty($tmp[0])) {
								$this->request->data['Form'][$key]['iso3'] = $tmp[0];
							}
						}
						$countries[$key] = $tmp;
					}

					if (empty($this->request->data['Form'])) {
						$this->Countries->invalidate('import_pattern', 'falsches Muster');
					}
				} else {
					foreach ($countries as $key => $country) {
						$this->request->data['Form'][$key] = ['name' => $country, 'confirm' => 1];
					}
				}

			}

			$this->set(compact('countries'));

		}
	}

	/**
	 * CountriesController::admin_index()
	 *
	 * @return void
	 */
	public function index() {
		if (Plugin::loaded('Search')) {
			$this->Countries->addBehavior('Search.Searchable');
			$this->Common->loadComponent('Search.Prg');

			$this->Prg->commonProcess();
			$countries = $this->paginate($this->Countries->find('searchable', $this->Prg->parsedParams()));
		}

		$countries = $this->paginate();
		$this->set(compact('countries'));

		$this->helpers = array_merge($this->helpers, ['Geo.GoogleMapV3']);
	}

	public function view($id = null) {
		$id = (int)$id;
		if ($id <= 0) {
			$this->Flash->error(__('record invalid'));
			return $this->redirect(['action' => 'index']);
		}
		$country = $this->Countries->get($id);
		if (empty($country)) {
			$this->Flash->error(__('record not exists'));
			return $this->redirect(['action' => 'index']);
		}
		$this->set(compact('country'));
	}

	public function add() {
		if ($this->Common->isPosted()) {
			$this->Countries->create();
			if ($this->Countries->save($this->request->data)) {
				$id = $this->Countries->id;
				//$name = $this->request->data['Country']['name'];
				$this->Flash->success(__('record add {0} saved', $id));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('record add not saved'));
			}
		}
	}

	public function edit($id = null) {
		if (!$id || !($country = $this->Countries->get($id))) {
			$this->Flash->error(__('record invalid'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->Common->isPosted()) {
			if ($this->Countries->save($this->request->data)) {
				$name = $country['Country']['name'];
				$this->Flash->success(__('record edit {0} saved', h($name)));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('record edit not saved'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $country;
			if (empty($this->request->data)) { # still no record found
				$this->Flash->error(__('record not exists'));
				return $this->redirect(['action' => 'index']);
			}
		}
	}

	public function delete($id = null) {
		$id = (int)$id;
		if ($id <= 0) {
			$this->Flash->error(__('record invalid'));
			return $this->redirect(['action' => 'index']);
		}
		$res = $this->Countries->find('first', ['fields' => ['id'], 'conditions' => ['Country.id' => $id]]);
		if (empty($res)) {
			$this->Flash->error(__('record del not exists'));
			return $this->redirect(['action' => 'index']);
		}

		//$name = $res['Country']['name'];
		if ($this->Countries->delete($id)) {
			$this->Flash->success(__('record del {0} done', $id));
			return $this->redirect(['action' => 'index']);
		} else {
			$this->Flash->error(__('record del {0} not done exception', $id));
			return $this->redirect(['action' => 'index']);
		}
	}

	public function up($id = null) {
		if (empty($id) || !($navigation = $this->Countries->find('first', ['conditions' => ['Country.id' => $id]]))) {
			$this->Flash->error(__('invalid record'));
			return $this->Common->autoRedirect(['action' => 'index']);
		}
		$this->Countries->moveDown($id, 1);
		return $this->redirect(['action' => 'index']);
	}

	public function down($id = null) {
		if (empty($id) || !($navigation = $this->Countries->find('first', ['conditions' => ['Country.id' => $id]]))) {
			$this->Flash->error(__('invalid record'));
			return $this->Common->autoRedirect(['action' => 'index']);
		}
		$this->Countries->moveUp($id, 1);
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Validate
	 * - abbr
	 * - zip length/regexp ?
	 * - EU ?
	 * - lat/lng available and correct ?
	 * - iso codes
	 * - address format?
	 * - timezones and summertime
	 *
	 * resources:
	 * - http://www.iso.org/iso/list-en1-semic-3.txt
	 * - http://www.worldtimeserver.com/country.html - http://api.geonames.org/timezone?lat=47.01&lng=10.2&username=demo
	 * - http://www.geonames.org/countries/ - http://api.geonames.org/postalCodeCountryInfo?username=demo
	 * - http://www.pixelenvision.com/1708/zip-postal-code-validation-regex-php-code-for-12-countries/
	 *
	 */
	public function validate() {
		$countries = $this->Countries->find('all');

		//TODO.
		$this->set(compact('countries'));
	}

}