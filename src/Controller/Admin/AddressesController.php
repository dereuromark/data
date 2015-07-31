<?php
namespace Data\Controller\Admin;

use Cake\Core\Configure;
use Data\Controller\DataAppController;
use Cake\Event\Event;

class AddressesController extends DataAppController {

	public $paginate = array();

	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
	}

	public function index() {
		$addresses = $this->paginate();
		$this->set(compact('addresses'));
		$this->Common->loadHelper('Geo.GoogleMapV3');
	}

	public function view($id = null) {
		if (empty($id) || !($address = $this->Address->find('first', array('conditions' => array('Address.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			return $this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('address'));
		$this->Common->loadHelper('Geo.GoogleMapV3');
	}

	public function add() {
		if ($this->Common->isPosted()) {
			$this->Address->create();
			if ($this->Address->save($this->request->data)) {
				$var = $this->request->data['Address']['formatted_address'];
				$this->Common->flashMessage(__('record add {0} saved', h($var)), 'success');
				return $this->redirect(array('action' => 'index'));
			}
			$this->Common->flashMessage(__('formContainsErrors'), 'error');

		} else {
			# TODO: geolocate via IP? only for frontend
			$options = array('Country.iso2' => 'DE');
			$this->request->data['Address']['country_id'] = $this->Address->Country->field('id', $options);
		}

		$countries = $this->Address->Country->find('list');
		$countryProvinces = array();
		if (Configure::read('Address.CountryProvince')) {
			$countryProvinces = $this->Address->CountryProvince->find('list');
		}

		$this->set(compact('countries', 'countryProvinces'));
	}

	public function edit($id = null) {
		if (empty($id) || !($address = $this->Address->find('first', array('conditions' => array('Address.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			return $this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->Address->save($this->request->data)) {
				$var = $this->request->data['Address']['formatted_address'];
				$this->Common->flashMessage(__('record edit {0} saved', h($var)), 'success');
				return $this->redirect(array('action' => 'index'));
			}
			$this->Common->flashMessage(__('formContainsErrors'), 'error');

		}
		if (empty($this->request->data)) {
			$this->request->data = $address;
			$belongsTo = array('' => ' - keine Auswahl - ');
			foreach ($this->Address->belongsTo as $b => $content) {
				if ($b === 'Country') {
					continue;
				}
				$belongsTo[$b] = $b;
			}
			if (!empty($belongsTo)) {
				$this->set('models', $belongsTo);
			}
		}
		$countries = $this->Address->Country->find('list');
		$countryProvinces = array();
		if (Configure::read('Address.CountryProvince')) {
			$countryProvinces = $this->Address->CountryProvince->find('list');
		}

		$this->set(compact('countries', 'countryProvinces'));
	}

	public function delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($address = $this->Address->find('first', array('conditions' => array('Address.id' => $id), 'fields' => array('id', 'formatted_address'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			return $this->Common->autoRedirect(array('action' => 'index'));
		}
		$var = $address['Address']['formatted_address'];

		if ($this->Address->delete($id)) {
			$this->Common->flashMessage(__('record del {0} done', h($var)), 'success');
			return $this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(__('record del {0} not done exception', h($var)), 'error');
		return $this->Common->autoRedirect(array('action' => 'index'));
	}

	public function mark_as_used($id = null) {
		if (empty($id) || !($address = $this->Address->find('first', array('conditions' => array('Address.id' => $id), 'fields' => array('id', 'formatted_address'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			return $this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->Address->touch($id);
		$var = $address['Address']['formatted_address'];
		$this->Common->flashMessage(__('Address \'{0}\' marked as last used', h($var)), 'success');
		return $this->Common->autoRedirect(array('action' => 'index'));
	}

}
