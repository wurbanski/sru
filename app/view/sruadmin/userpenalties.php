<?php
class UFview_SruAdmin_UserPenalties
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleUserPenalties());
		$this->append('body', $box->userPenalties());
		

	}
}
