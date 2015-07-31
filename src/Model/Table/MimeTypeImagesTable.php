<?php
namespace Data\Model\Table;

use Cake\Model\Model;
use Cake\Filesystem\File;
use Tools\Model\Table\Table;

class MimeTypeImagesTable extends Table {

	public $order = array('modified' => 'DESC');

	public $validate = array(
		'name' => array( # e.g. "exe"
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'valErrMandatoryField',
				//'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				//'required' => true
			),
		),
		'ext' => array(), # e.g. "jpg" on a file "exe.jpg"
		'active' => array('numeric')
	);

	public $hasMany = array(
		'MimeType' => array(
			'className' => 'Data.MimeType',
			'foreignKey' => 'mime_type_image_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
		)
	);

	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		if (isset($this->data['name'])) {
			$this->data['name'] = mb_strtolower($this->data['name']);
		}
		if (isset($this->data['ext'])) {
			$this->data['ext'] = mb_strtolower($this->data['ext']);
		}

		return true;
	}

	public function afterSave($created, $options = array()) {
		# clean up!
		$this->cleanUp();
		return true;
	}

	public function beforeDelete($cascade = true) {
		# retrieve infos
		$this->_del = $this->find('first', array('conditions' => array($this->alias() . '.id' => $this->id)));

		return true;
	}

	public function afterDelete(Event $event, Entity $entity, ArrayObject $options) {
		if (!empty($this->_del)) {
			# todo: ...
			$image = $this->_del['name'] . '.' . $this->_del['ext'];

			# delete image (right now: move to archive)
			if (file_exists(PATH_MIMETYPES . $image)) {
				if (!rename(PATH_MIMETYPES . $image, PATH_MIMETYPES . 'archive' . DS . $image)) {
					return false;
				}
			}

			# remove id from mime_types table

			$types = $this->MimeType->find('all', array('fields' => array('id'), 'conditions' => array('mime_type_image_id' => $this->_del['id'])));
			foreach ($types as $type) {
				$this->MimeType->id = $type[$this->MimeType->alias]['id'];
				$this->MimeType->saveField('mime_type_image_id', 0);
				//pr ($type[$this->MimeType->alias]['id'].' del success');
			}
		}

		# clean up!
		$this->cleanUp();
		return true;
	}

	public function cleanUp() {
		$handle = new File(FILES . 'mime_types.txt');
		$handle->delete();
	}

	public function findAsList() {
		$list = array();
		$images = $this->find('all', array('conditions' => array('active' => 1))); // ,'contain'=>'MimeType.id'
		foreach ($images as $image) {
			//$count = count($image['MimeType']);
			$list[$image['MimeTypeImage']['id']] = $image['MimeTypeImage']['name'] . '.' . (!empty($image['MimeTypeImage']['ext']) ? $image['MimeTypeImage']['ext'] : '?');
		}
		return $list;
	}

	public function allocate($id = null, $fileName = null, $ext = null) {
		if (empty($fileName) && empty($id) || empty($ext)) {
			return false;
		}

		if (empty($id)) {
			# new entry
			$this->create();
			$data = array('name' => $fileName, 'ext' => $ext, 'active' => 1);
			if ($this->save($data)) {
				return true;
			}
		} else {
			$this->id = $id;
			$data = array('ext' => $ext);
			if ($this->save($data)) {
				return true;
			}
		}
		return false;
	}

/** Static Enums **/

	/**
	 * Static Model::method()
	 * ALLOWED EXTENSIONS
	 */
	public function extensions($value = null) {
		$options = array(
			'gif' => __('GIF (*.gif)'),
			'png' => __('PNG (*.png)'),
			'jpg' => __('JPG (*.jpg)'),
		);

		if ($value !== null) {
			if (array_key_exists($value, $options)) {
				return $options[$value];
			}
			return '';
		}
		return $options;
	}
}
