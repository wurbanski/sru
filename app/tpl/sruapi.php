<?
/**
 * szablon api sru
 */
class UFtpl_SruApi
extends UFtpl_Common {

	public function dhcp(array $d) {
		$d['computers']->write('configDhcp');
	}

	public function dnsRev(array $d) {
		if (!is_null($d['computers'])) {
			$d['computers']->write('configDnsRev');
		}
		if (!is_null($d['switches'])) {
			$d['switches']->write('configDnsRev');
		}
	}

	public function dns(array $d) {
		if (!is_null($d['computers'])) {
			$d['computers']->write('configDns', $d['aliases']);
		}
		if (!is_null($d['switches'])) {
			$d['switches']->write('configDns');
		}
	}

	public function ethers(array $d) {
		$d['computers']->write('configEthers');
	}
	
	public function skosEthers(array $d) {
		$d['computers']->write('configSkosEthers');
	}

	public function admins(array $d) {
		$d['computers']->write('configAdmins');
	}

	public function tourists(array $d) {
		$d['computers']->write('configAdmins');
	}

	public function switches(array $d) {
		$d['switches']->write('apiList');
	}

	public function findMac(array $d) {
		$d['switchPort']->write('apiInfo');
	}

	public function switchesStructure(array $d) {
		$d['switchPorts']->write('apiStructure', $d['dormitory']);
	}
	
	public function switchesModelIps(array $d) {
		$d['switches']->write('apiList');
	}
	
	public function switchesModels(array $d) {
		$d['switches']->write('apiModelList');
	}

	public function error404() {
		header('HTTP/1.0 404 Not Found');
	}

	public function penaltiesPast(array $d) {
		$d['penalties']->write('apiPast');
	}

	public function computersLocations(array $d) {
		$d['computers']->write('apiComputersLocations');
	}

	public function computersOutdated(array $d) {
		$d['computers']->write('apiComputersOutdated');
	}
	
	public function computersNotSeen(array $d) {
		$d['computers']->write('apiComputersNotSeen');
	}
	
	public function computersServers(array $d) {
		$d['computers']->write('apiComputersServers');
	}
	
	public function usersToDeactivate(array $d) {
		$d['users']->write('apiUsersToDeactivate');
	}
	
	public function usersToRemove(array $d) {
		$d['users']->write('apiUsersToDeactivate');
	}
	
	public function adminsOutdated(array $d) {
		$d['admins']->write('apiAdminsOutdated');
	}

	public function dormitoryIps(array $d) {
		$d['ips']->write('apiDormitoryIps');
	}
	
	public function dormitoryFreeIps(array $d) {
		$d['sum']->write('apiDormitoryFreeIps', $d['used']);
	}
	
	public function firewallExceptions(array $d) {
		$d['admins']->write('apiFirewallExceptions', $d['exadmins']);
	}

	public function apiPenaltiesTimelineMailTitle(array $d) {
		echo date(self::TIME_YYMMDD).': Podsumowanie nałożonych/modyfikowanych kar';
	}
	
	public function apiPenaltiesTimelineMailBody(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$host = $conf->sruUrl;

		if (is_null($d['added'])) {
			echo 'Nie nałożono żadnej kary ani ostrzeżenia.'."\n";
		} else {
			echo 'Nałożonych kar i ostrzeżeń: '.count($d['added'])."\n";
			foreach ($d['added'] as $added) {
				echo date(self::TIME_YYMMDD_HHMM, $added['createdAt']).': '.$added['userName'].' "'.$added['userLogin'].'" '.$added['userSurname'].' ('.strtoupper($added['userDormAlias']).') za: '.($added['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING ? '*': '').$added['templateTitle'].' przez: '.$added['creatorName'].' '.$host.'/admin/penalties/'.$added['id']."\n";
			}
		}
		echo "\n";
		if (is_null($d['modified'])) {
			echo 'Nie zmodyfikowano żadnej kary.'."\n";
		} else {
			echo 'Zmodyfikowanych kar: '.count($d['modified'])."\n";
			foreach ($d['modified'] as $modified) {
				echo date(self::TIME_YYMMDD_HHMM, $modified['modifiedat']).': '.$modified['name'].' "'.$modified['login'].'" '.$modified['surname'].' ('.strtoupper($modified['userdormalias']).') za: '.$modified['template'].' przez: '.$modified['modifiername'].' '.$host.'/admin/penalties/'.$modified['id']."\n";
			}
		}
		
		echo "\n";
		if (is_null($d['ending'])) {
			echo 'Żadna kara ani ostrzeżenie nie kończy się.'."\n";
		} else {
			echo 'Kończące się kary i ostrzeżenia: '.count($d['ending'])."\n";
			foreach ($d['ending'] as $ending) {
				echo date(self::TIME_YYMMDD_HHMM, $ending['endAt']).': '.$ending['userName'].' "'.$ending['userLogin'].'" '.$ending['userSurname'].' ('.strtoupper($ending['userDormAlias']).') za: '.$ending['templateTitle'].' przez: '.$ending['creatorName'].' '.$host.'/admin/penalties/'.$ending['id']."\n";
			}
		}
	}

	public function dutyHours(array $d) {
		$d['hours']->write('apiAllDutyHours', $d['dormAdmins'], $d['dormitories']);
	}

	public function dutyHoursUpcoming(array $d) {
		$d['hours']->write('apiUpcomingDutyHours', $d['days'], $d['dormitories']);
	}
	
	public function hostDeactivatedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostChangedMailTitleEnglish');
		} else {
			echo $d['host']->write('hostChangedMailTitlePolish');
		}
	}
	
	public function hostDeactivatedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostAdminChangedMailBodyEnglish', $d['action']);
		} else {
			echo $d['host']->write('hostAdminChangedMailBodyPolish', $d['action']);
		}
	}
}
