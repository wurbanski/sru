<?

/**
 * wymeldowanie przez administratora Waleta uzytkownika
 */
class UFact_SruWalet_User_Del
extends UFact {

	const PREFIX = 'userDel';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$userId = (int)$this->_srv->get('req')->get->userId;
			$penalties = UFra::factory('UFbean_SruAdmin_Penalty');
			try{
				$penaltyList = $penalties->getAllActiveByUserId($userId);
			}catch(Exception $e){
				$penaltyList = array();
			}
			
			foreach($penaltyList as $penalty){
				$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
				$portData = $port->getByPenaltyUserId($penalty['id'], $userId);

				if($portData[0]['switchId'] > 0 && $portData[0]['ordinalNo'] > 0 && $portData[0]['portId'] > 0){
					$switch = UFra::factory('UFbean_SruAdmin_Switch');
					$switch->getByPK($portData[0]['switchId']);
					$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
					$hp->setPortStatus($portData[0]['ordinalNo'], UFlib_Snmp_Hp::ENABLED);
					$hp->setPortAlias($portData[0]['ordinalNo'], $portData[0]['locationAlias']);
				}
				
				try{
					UFra::factory('UFbean_SruAdmin_SwitchPort')->erasePenalty($penalty['id']);
				} catch(Exception $e) {}
			}
			
			$bean->getByPK($userId);

			$post = $this->_srv->get('req')->post->{self::PREFIX};
			
			$bean->fillFromPost(self::PREFIX);
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->modifiedAt = NOW;
			$bean->active = false;
			
			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && $bean->notifyByEmail() && !is_null($bean->email) && $bean->email != '') {
				$sender = UFra::factory('UFlib_Sender');
				$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
				$history->listByUserId($bean->id, 1);
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$title = $box->dataChangedMailTitle($bean);
				$body = $box->dataChangedMailBody($bean, $history);
				$sender->send($bean, $title, $body, self::PREFIX);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->rollback();
			if (0 == $e->getCode()) {
				$this->markErrors(self::PREFIX, array('mac'=>'regexp'));
			} else {
				throw $e;
			}
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
