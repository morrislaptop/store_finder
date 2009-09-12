<?php
class StoreFinderAppController extends AppController
{
	function beforeFilter() {
		if ( !empty($this->params['prefix']) ) {
			$this->view = 'theme';
			$this->theme = $this->params['prefix'];
		}
		parent::beforeFilter();
	}
}
?>