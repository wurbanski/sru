<?
/**
 * widok uzytkownika
 */
class UFview_SruUser
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexSru', $this->_srv);
	}

	protected function fillDefaultData() {
		if (!isset($this->data['userMainManu'])) {
			$box = UFra::shared('UFbox_Sru');
			$this->append('userMainMenu', $box->userMainMenu());
		}
		if (!isset($this->data['userBar'])) { 
			$box = UFra::shared('UFbox_Sru');
			$this->append('userBar', $box->userBar());
		}	
	}			

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');
	}
}
