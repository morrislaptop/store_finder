<?php
class StoresController extends StoreFinderAppController {

	var $name = 'Stores';
	var $components = array(
		'Advindex.Advindex' => array(
			'fields' => array(
				'name' => 'Name',
				'password' => 'Password',
				'address' => 'Address',
				'suburb' => 'City',
				'state' => 'State',
				'postcode' => 'Zip/Postal',
				'phone' => 'Clinic Phone Number',
				'website' => 'Website'
			),
			'update_if_fields' => array(
				'name'
			),
		)
	);
	var $helpers = array('Advindex.Advindex');
	var $layout = 'app';
	var $paginate = array(
		'contain' => array()
	);

	/**
	* @var Store
	*/
	var $Store;

	function admin_index() {
		$this->Store->recursive = 0;
		$this->set('stores', $this->paginate());
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Store->create();
			if ($this->Store->save($this->data)) {
				$this->Session->setFlash(__('The store have been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The page could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		$this->_setFormData();
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Store Content', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Store->save($this->data)) {
				$this->Session->setFlash(__('The store have been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The store content could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$this->Store->contain();
			$this->data = $this->Store->read(null, $id);
		}
		$this->_setFormData();
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Store', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Store->del($id)) {
			$this->Session->setFlash(__('Store deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	function _setFormData() {
		$states = array('NSW', 'QLD', 'WA', 'SA', 'ACT', 'TAS', 'NT', 'VIC');
		$states = array_combine($states, $states);
		$this->set(compact('states'));
	}

	function find($postcode = null) {
		// Catch data.
		if ( !empty($this->data['Store']['postcode']) ) {
			$this->redirect(array('action' => 'find', $this->data['Store']['postcode']));
		}

		// We have postcode, search for stores.
		$searched = false;
		if ( $postcode ) {
			$stores = $this->Store->findNearPostcode($postcode);
			$searched = true;
		}
		else {
			$stores = $this->Store->find('all');
		}
		$this->set(compact('stores', 'searched'));
	}

	function set_passwords()
	{
		$stores = $this->Store->find('all');
		foreach ($stores as $s)
		{
			$matches = array();
			preg_match_all('/\W(.)/', $s['Store']['name'], $matches);
			array_unshift($matches[1], substr($s['Store']['name'], 0, 1));
			$pass = implode('', $matches[1]) . $s['Store']['postcode'];
			$this->Store->id = $s['Store']['id'];
			$this->Store->saveField('password', $pass);
		}
	}
}
?>