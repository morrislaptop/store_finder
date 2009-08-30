<?php
class StoresController extends StoreFinderAppController {

	var $name = 'Stores';
	var $components = array(
		'Advindex.Advindex' => array(
			'update_if_fields' => array(
				'name',
				'postcode'
			),
		)
	);
	var $helpers = array('Advindex.Advindex');
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

	function admin_reset()
	{
		$stores = $this->Store->find('all');
		foreach ($stores as $store) {
			$this->Store->create();
			unset($store['Store']['lat'], $store['Store']['lon']);
			$this->Store->save($store);
		}
	}

	function _setFormData() {
		$states = array('NSW', 'QLD', 'WA', 'SA', 'ACT', 'TAS', 'NT', 'VIC');
		$states = array_combine($states, $states);
		$this->set(compact('states'));
	}
}
?>