<?
/**
 * szablon beana komputera
 */
class UFtpl_Sru_Computer
extends UFtpl {

	protected $computerTypes = array(
		1 => 'Student',
		2 => 'Organizacja',
		3 => 'Administracja',
		4 => 'Serwer',
	);
	
	protected $errors = array(
		'host' => 'Nieprawidłowa nazwa',
		'host/duplicated' => 'Nazwa jest już zajęta',
		'host/textMin' => 'Nazwa jest za krótka',
		'host/textMax' => 'Nazwa jest zbyt długa',
		'host/regexp' => 'Zawiera niedowzolone znaki',
		'mac' => 'Nieprawidłowy format',
		'mac/duplicated' => 'MAC jest już zajęty',
		'ip' => 'Nieprawidłowy format',
		'availableMaxTo' => 'Nieprawidłowy format',
		'dormitory' => 'Wybierz akademik',
		'locationId' => 'Podaj pokój',
		'locationId/noDormitory' => 'Wybierz akademik',
		'locationId/noRoom' => 'Pokój nie istnieje',
	);
	
	public function listOwn(array $d) {
		$url = $this->url(1).'/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
		}
	}

	public function titleDetails(array $d) {
		echo 'Komputer "'.$d['host'].'"';
	}

	public function titleEdit(array $d) {
		echo 'Edycja komputera "'.$d['host'].'"';
	}

	public function detailsOwn(array $d) {
		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p><em>Rejestracja do:</em> '.date(self::TIME_YYMMDD, $d['availableTo']).'</p>';
		echo '<p><em>Miejsce:</em> '.$d['locationAlias'].' ('.$d['dormitoryName'].')</p>';
		echo '<p><em>Liczba kar:</em> '.$d['bans'].'</p>';
		$ip = explode('.', $d['ip']);
		$tag = substr(md5('haha'.$ip[2].$ip[3]), 0, 5);
		echo '<p><a href="https://sru.ds.pg.gda.pl/lanstats/?ip='.$ip[2].'.'.$ip[3].'"><img src="https://sru.ds.pg.gda.pl/lanstats/153.19.'.$ip[2].'/'.str_pad($ip[3], 3, '0', STR_PAD_LEFT).'.'.$tag.'.png" alt="Statystyki transferów" /></a></p>';
	}

	public function details(array $d) {
		$url = $this->url(0);
		echo '<h1>'.$d['host'].'</h1>';
		if (is_null($d['userId'])) {
			$user = 'BRAK';
		} else {
			$user = '<a href="'.$url.'/users/'.$d['userId'].'">'.$d['userName'].' '.$d['userSurname'].'</a>';
		}
		if ($d['typeId'] != 1) {
			echo '<p><em>Typ komputera:</em> '.$this->computerTypes[$d['typeId']].'</p>';
		}
		echo '<p><em>Właściciel:</em> '.$user.'</p>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		if (!$d['active']) {
			$max = 'BRAK <small>(było '.date(self::TIME_YYMMDD, $d['availableTo']).')</small>';
		} elseif ($d['availableTo'] != $d['availableMaxTo']) {
			$max = date(self::TIME_YYMMDD, $d['availableTo']).'<small> (max '.date(self::TIME_YYMMDD, $d['availableMaxTo']).')</small>';
		} else {
			$max = date(self::TIME_YYMMDD, $d['availableTo']);
		}
		echo '<p><em>Rejestracja do:</em> '.$max.'</p>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small></p>';
		if ($d['banned']) {
			$bans = ' <small>(<a href="'.$this->url().'/bans">są aktywne</a>)</small>';
		} elseif ($d['bans']>0) {
			$bans = ' <small>(<a href="'.$this->url().'/bans">lista</a>)</small>';
		} else {
			$bans= '';
		}
		echo '<p><em>Kary:</em> '.$d['bans'].$bans.'</p>';
		$acls = array();
		if ($d['canAdmin']) {
			$acls[] = 'admin';
		}
		if (count($acls)) {
			echo '<p><em>Uprawnienia:</em> '.implode(', ', $acls).'</p>';
		}
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$d['modifiedBy'].'</a>';;
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		if (strlen($d['comment'])) {
			echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		$form->mac('MAC');
	}

	public function formEditAdmin(array $d, $dormitories) {
		$d['availableMaxTo'] = date(self::TIME_YYMMDD, $d['availableMaxTo']);
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		$form->host('Nazwa');
		$form->mac('MAC');
		$form->ip('IP');
		$form->availableMaxTo('Rejestracja max do');
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		$form->locationId('Pokój');
		$form->canAdmin('Komputer administratora', array('type'=>$form->CHECKBOX));
		$form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function formAdd(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerAdd', $d, $this->errors);

		$form->host('Nazwa');
		$form->mac('MAC');
	}

	public function formDel(array $d) {
		$form = UFra::factory('UFlib_Form');
		$form->confirm('Tak, chcę wyrejestrować ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]', 'value'=>'1'));
	}

	public function formDelAdmin(array $d) {
		$form = UFra::factory('UFlib_Form');
		$form->confirm('Tak, wyrejestruj ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]', 'value'=>'1'));
	}

	public function listAdmin(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li><a href="'.$url.'/computers/'.$c['id'].'">'.$c['host'].'</a></li>';
		}
	}
}