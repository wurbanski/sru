<?
/**
 * uzytkownik
 */
class UFdao_Sru_User
extends UFdao {

	public function getByLoginPassword($login, $password) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);
		$query->where($mapping->password, $password);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByLogin($login) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);

		return $this->doSelectFirst($query);
	}

	public function getByRegistryNo($registryNo) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->registryNo, $registryNo);

		return $this->doSelectFirst($query);
	}

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->auth);
	}

	public function search($params) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->order($mapping->dormitoryId, $query->ASC);
			$query->order($mapping->room, $query->ASC);
			$query->order($mapping->surnameSearch, $query->ASC);
			$query->order($mapping->nameSearch, $query->ASC);
			$query->order($mapping->active, $query->DESC);
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'surname':
					case 'name':
					case 'login':
					case 'email':
					case 'room':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->where($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					case 'dormitory':
					case 'registryNo':
					default:
						$query->where($var, $val);
				}
			}

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function listByRoom($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->order($mapping->surname);

		return $this->doSelect($query);
	}

	public function listByEmailActive($email, $active=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->email, $email);
		if (is_bool($active)) {
			$query->where($mapping->active, $active);
		}

		return $this->doSelect($query);
	}

	public function getByEmailActive($email, $active=null) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->email, $email);
		if (is_bool($active)) {
			$query->where($mapping->active, $active);
		}

		return $this->doSelectFirst($query);
	}

	public function listAllActive() {
		$mapping = $this->mapping('stats');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);

		return $this->doSelect($query);
	}

	public function listActiveByDorm($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $id);
		$query->where($mapping->active, true);

		return $this->doSelect($query);
	}

	public function listAllForMigration() {
		$mapping = $this->mapping('migration');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->dormitoryId);

		return $this->doSelect($query);
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych/dodanych użytkowników.
	 *
	 */
	public function listLastModified($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		if (isset($id)) {
			$query->where($mapping->modifiedById, $id);
		}
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}
}
