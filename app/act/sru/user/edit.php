<?

/**
 * edycja danych uzytkownika
 */
class UFact_Sru_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	protected function checkOldPassword(&$bean, &$post) {
		if ($bean->password != $bean->generatePassword($bean->login, $post['password3'])) {
			throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password3" is not valid', 0, E_WARNING, array('password3' => 'invalid'));
		}
	}
	
	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean->getFromSession();
			$dormitoryId = $bean->dormitoryId;
			$bean->fillFromPost(self::PREFIX, array('email', 'login','password','name','surname','facultyId','studyYearId'));


			if (isset($post['password']) && $post['password'] != '' ) {
				$this->checkOldPassword($bean, $post);
			
				$map = UFra::factory('UFmap_Sru_User_Set');
				$valid = $map->valid('password');

				if (!isset($post['password2']) || $post['password'] != $post['password2']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" and "password2" do not match', 0, E_WARNING, array('password' => 'mismatch'));
				}
				if (strlen($post['password2']) < $valid['textMin']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" too short', 0, E_WARNING, array('password' => 'tooShort'));
				}
				$bean->password = $bean->generatePassword($bean->login, $post['password']);
			}
			if (isset($post['email']) && $post['email'] != $bean->email) {
				$this->checkOldPassword($bean, $post);
				$bean->email = $post['email'];
			}
			if (isset($post['facultyId']) && $post['facultyId'] == '0' && isset($post['studyYearId']) && $post['studyYearId'] != '0') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "studyYearId" differ from "N/A"', 0, E_WARNING, array('studyYearId' => 'noFaculty'));
			}
			$bean->facultyId = $post['facultyId'];
			$bean->studyYearId = $post['studyYearId'];

			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->checkWalet) {
				// sprawdzenie w bazie osiedla
				$walet = UFra::factory('UFbean_Sru_User');
				try {
					$walet->getFromWalet($bean->name, $bean->surname, $bean->locationAlias, $bean->dormitory);
				} catch (UFex_Dao_NotFound $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'User not in Walet database', 0, E_WARNING,  array('walet' => 'notFound'));
				}
			}
				
			$bean->save();
	
			try {
				$comps = UFra::factory('UFbean_Sru_ComputerList');
				$comps->listByUserId($bean->id);
				foreach ($comps as $comp) {
					try {
						$computer = UFra::factory('UFbean_Sru_Computer');
						$computer->getByHost($comp['host']);
						$ipAddr = $computer->ip;
						if ($dormitoryId != $bean->dormitory) {
							$ip = UFra::factory('UFbean_Sru_Ipv4');
							$ip->getFreeByDormitoryId(($bean->dormitory));
							$ipAddr = $ip->ip;
						}
						$computer->updateLocationByHost($comp['host'], $bean->locationId, $ipAddr);
					} catch (UFex_Dao_NotFound $e) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'No free IP', 0, E_WARNING, array('ip'=>'noFree'));
					}
				}
			} catch (UFex_Dao_NotFound $e) {
				// uzytkownik nie ma komputerow
			}

			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				$title = $box->dataChangedMailTitle($bean);
				$body = $box->dataChangedMailBody($bean);
				$headers = $box->dataChangedMailHeaders($bean);
				mail($bean->email, '=?UTF-8?B?'.base64_encode($title).'?=', $body, $headers);
			}
	
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
