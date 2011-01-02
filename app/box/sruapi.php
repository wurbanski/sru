<?

/**
 * sru api
 */
class UFbox_SruApi
extends UFbox {

	protected function configDhcp($type) {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType($type);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dhcpStuds() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_STUDENT);
	}

	public function dhcpOrg() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ORGANIZATION);
	}

	public function dhcpAdm() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ADMINISTRATION);
	}

	public function dhcpServ() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_SERVER);
	}

	public function dnsRev() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByIpClass((int)$this->_srv->get('req')->get->ipClass);

			$d['computers'] = $bean;

			return $this->render('configDnsRev', $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dnsDs() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType(array(
				UFbean_Sru_Computer::TYPE_STUDENT,
				UFbean_Sru_Computer::TYPE_ORGANIZATION,
				UFbean_Sru_Computer::TYPE_SERVER
			));

			$d['computers'] = $bean;

			try {
				$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
				$aliases->listAll();
				$d['aliases'] = $aliases;
			} catch (UFex_Dao_NotFound $e) {
				$d['aliases'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dnsAdm() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType(UFbean_Sru_Computer::TYPE_ADMINISTRATION);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function ethers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listEthers();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function admins() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAdmins();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function switches() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_SwitchList');
			$bean->listEnabled();

			$d['switches'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function penaltiesPast() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');	
			$bean->listPast();
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function error403() {
		UFlib_Http::notAuthorised('SRU');
		return '';
	}

	public function status200() {
		return '';
	}

	public function computersLocations() {
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listActiveStudsByDormitoryId($dorm->id);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function computersOutdated() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listOutdated();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dormitoryIps() {
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$sum = UFra::factory('UFbean_Sru_Ipv4');
			$sum->getUsedByDorm($dorm->id);
			$d['used'] = $sum;
			$sum = UFra::factory('UFbean_Sru_Ipv4');
			$sum->getSumByDorm($dorm->id);
			$d['sum'] = $sum;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function myLanstats() {
		try {
			$serv = $this->_srv->get('req')->server;
			$ip =  $serv->REMOTE_ADDR;
			$transfer = UFra::factory('UFbean_SruAdmin_Transfer');
			$transfer->listByIp($ip);
			$d['transfer'] = $transfer;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
}
