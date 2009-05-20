<?php
class StoresController extends StoreFinderAppController {

	var $name = 'Stores';
	var $uses = array('StoreFinder.Store');
	var $components = array(
		'Advindex.Advindex' => array(
			'fields' => array(
				'name' => 'Name',
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

	function admin_index() {
		$this->Store->recursive = 0;
		$this->set('stores', $this->paginate());
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Store->create();
			if ($this->Store->save($this->data)) {
				$this->Session->setFlash(__('The store have been saved'), true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The page could not be saved. Please, try again.', true), 'default', array('class' => 'errorMsg'));
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
				$this->Session->setFlash(__('The store have been saved'), true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The store content could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Store->read(null, $id);
		}
		$this->_setFormData();
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
		// No Postocde, just display the form.
		if ( !$postcode ) {
			$this->render('find');
			return;
		}
		// We have postcode, search for stores.
		$stores = $this->Store->findNearPostcode($postcode);
		$this->set(compact('stores'));
		$this->render('find_results');
	}
}
?>