<?

/**
 * sru
 */
class UFbox_Sru
extends UFbox {

	protected function _getComputerFromGetByCurrentUser() {
		$bean = UFra::factory('UFbean_Sru_Computer');
		$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);

		return $bean;
	}

	public function login() {
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function logout() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getFromSession();

		$d['user'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function userAdd() {
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		$faculties = UFra::factory('UFbean_Sru_FacultyList');
		$faculties->listAll();
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;
		$d['dormitories'] = $dorms;
		$d['faculties'] = $faculties;

		return $this->render(__FUNCTION__, $d);
	}

	public function userEdit() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getFromSession();

		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		$faculties = UFra::factory('UFbean_Sru_FacultyList');
		$faculties->listAll();

		$d['user'] = $bean;
		$d['dormitories'] = $dorms;
		$d['faculties'] = $faculties;

		return $this->render(__FUNCTION__, $d);
	}

	public function userComputers() {
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByUserId($user->id);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleUserComputer() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleUserComputerNotFound');
		}
	}

	public function userComputer() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerEdit() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerAdd() {
		$bean = UFra::factory('UFbean_Sru_Computer');

		$d['computer'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function userComputerDel() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
}
