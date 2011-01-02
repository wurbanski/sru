<?
/**
 * template admina Waleta
 */
class UFtpl_SruWalet_Admin
extends UFtpl_Common {

	public static $adminTypes = array(
		UFacl_SruWalet_Admin::DORM 	=> 'Pracownik OS',
		UFacl_SruWalet_Admin::OFFICE 	=> 'Starszy Pracownik OS',
		UFacl_SruWalet_Admin::HEAD 	=> 'Kierownik OS',
	);
	
	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login jest zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/mismatch' => 'Hasła różnią się',
		'name' => 'Podaj nazwę',
		'name/regexp' => 'Nazwa zawiera niedozwolone znaki',
		'name/textMax' => 'Nazwa jest za długa',
		'email' => 'Adres email jest nieprawidłowy ',
		'dormitoryId' => 'Wybierz akademik',
		'typeId' => 'Wybierz uprawnienia',
	);	
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$this->_escape($d['name']).'</p>';
	}
	
	public function listAdmin(array $d, array $dorms) {
		$url = $this->url(0).'/admins/';
		$baseUrl = $this->url(0);

		echo '<table id="adminsT" style="width: 100%;"><thead><tr>';
		echo '<th>Administrator</th>';
		echo '<th>Ostatnie logowanie</th>';
		echo '<th>DS-y pod opieką</th>';
		echo '</tr></thead><tbody>';

		foreach ($d as $c) {
			echo '<tr><td style="border-top: 1px solid;"><a href="'.$url.$c['id'].'">';
			switch($c['typeId']) {
				case UFacl_SruWalet_Admin::HEAD:
						echo '<strong>'.$this->_escape($c['name']).'</strong></a>';
						break;
				case UFacl_SruWalet_Admin::OFFICE:
						echo '<i>'.$this->_escape($c['name']).'</i></a>';
						break;
				case UFacl_SruWalet_Admin::DORM:
						echo $this->_escape($c['name']).'</a>';
						break;
			}
			echo '</td><td style="border-top: 1px solid;">'.($c['lastLoginAt'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $c['lastLoginAt'])).'</td>';
			if($c['typeId'] == UFacl_SruWalet_Admin::HEAD){
				echo '<td style="border-top: 1px solid;">wszystkie</td></tr>';
			}else if(is_null($dorms[$c['id']])){
				echo '<td style="border-top: 1px solid;">żaden</td></tr>';
			}else{
				echo '<td style="border-top: 1px solid;">';
				foreach($dorms[$c['id']] as $dorm){
					echo '<a href="'.$baseUrl.'/dormitories/'.$dorm['dormitoryAlias'].'">'.strtoupper($dorm['dormitoryAlias']).'</a> ';
				}
				echo '</td></tr>';
			}
		}
		echo '</tbody></table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#adminsT").tablesorter(); 
    } 
);
</script>
<?
	}

	public function listAdminSimple(array $d) {
		$url = $this->url(0).'/admins/';
		
		if(!count($d))
			return;

		echo '<ul>';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).'</a></li>';
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']);
	}

	public function details(array $d, $dormList) {
		$url = $this->url(0);
		if (array_key_exists($d['typeId'], $this::$adminTypes)) {
			$type = $this::$adminTypes[$d['typeId']];
		} else {
			$type = UFtpl_SruAdmin_Admin::$adminTypes[$d['typeId']];
		}
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$type.' &bull; ostatnie logowanie: '.date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt']).')</small></h2>';

		if ($d['typeId'] != UFacl_SruWalet_Admin::HEAD) { 
			echo '<p><em>Domy studenckie:</em>';
			if (is_null($dormList)) {
				echo ' brak przypisanych DS</p>';
			} else {
				echo '</p><ul>';
				foreach ($dormList as $dorm) {
					echo '<li><a href="'.$url.'/dormitories/'.$dorm['dormitoryAlias'].'">'.$dorm['dormitoryName'].'</a></li>';
				}
				echo '</ul>';
			}
		}

		echo '<p><em>Login:</em> '.$d['login'].'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Telefon:</em> '.$d['phone'].'</p>';
		echo '<p><em>Gadu-Gadu:</em> '.$d['gg'].'</p>';
		echo '<p><em>Jabber:</em> '.$d['jid'].'</p>';
		echo '<p><em>Adres:</em> '.$d['address'].'</p>';
	}	

	public function titleAdd(array $d) {
		echo 'Dodanie nowego administratora';
	}		
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 11;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->login('Login', array('class'=>'required'));
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		echo $form->name('Imię i nazwisko', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Imię i nazwisko administratora lub inne oznaczenie." /><br/>', 'class'=>'required')); 
		echo $form->typeId('Uprawnienia', array( 
			'type' => $form->SELECT, 
			'labels' => $form->_labelize($this::$adminTypes), 
			'after'=> ' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Kierownik OS ma uprawnienia do wszystkich części Waleta, zaś Pracownik OS jedynie do wybranych Domów Studenckich. Starszy pracownik OS może także dostęp do obsadzenia każdego DSu." /><br/>', 
		));
		echo $form->_end();

		echo '<div id="dorms">' . $form->_fieldset('Domy studenckie');
		$post = $this->_srv->get('req')->post;
		foreach ($dormitories as $dorm) {
			$permission = 0;
			try {
				$permission = $post->adminAdd['dorm'][$dorm['id']];
			} catch (UFex_Core_DataNotFound $e) {
			}
			echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminAdd[dorm]['.$dorm['id'].']', 'id'=>'adminAdd[dorm]['.$dorm['id'].']', 'value'=>$permission));
		}
		echo $form->_end() . '</div>';
		
?><script type="text/javascript">
(function (){
	form = document.getElementById('adminAdd_typeId');
	function changeVisibility() { 
		var div = document.getElementById("dorms"); 
		if (form.value == <? echo UFacl_SruWalet_Admin::HEAD; ?>) { 
			div.style.display = "none"; 
			div.style.visibility = "hidden"; 
		} else { 
			div.style.display = "block"; 
			div.style.visibility = "visible"; 
		}
	}
	form.onchange = changeVisibility;
})()
</script><?

		echo $form->_fieldset();
		echo $form->email('E-mail', array('class'=>'required'));
		echo $form->phone('Telefon');
		echo $form->gg('GG', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Numer w komunikatorze GG." /><br/>'));
		echo $form->jid('Jabber', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Adres w komunikatorze sieci Jabber." /><br/>'));
		echo $form->address('Adres', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Lokalizacja lub miejsce przebywania administratora." /><br/>'));

?>
<script>
$("#main img[title]").tooltip({ position: "center right"});
</script>
<?
	}

	public function formEdit(array $d, $dormitories, $dormList, $advanced=false) {
		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->name('Imię i nazwisko', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Imię i nazwisko administratora lub inne oznaczenie." /><br/>', 'class'=>'required'));
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		if($advanced) {
			echo $form->typeId('Uprawnienia', array( 
				'type' => $form->SELECT, 
				'labels' => $form->_labelize($this::$adminTypes), 
				'after'=> ' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Kierownik OS ma uprawnienia do wszystkich części Waleta, zaś Pracownik OS jedynie do wybranych Domów Studenckich. Starszy pracownik OS może także dostęp do obsadzenia każdego DSu." /><br/>', 
			));
			echo $form->active('Aktywny <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Tylko aktywni administratorzy mogą zalogować się do Waleta." />', array('type'=>$form->CHECKBOX) );
		}

		echo $form->_end();

		if($advanced) {
			$post = $this->_srv->get('req')->post;
			echo '<div id="dorms">' . $form->_fieldset('Domy studenckie');
			foreach ($dormitories as $dorm) {
				$permission = 0;
				try {
					$permission = $post->adminEdit['dorm'][$dorm['id']];
				} catch (UFex_Core_DataNotFound $e) {
					if (!is_null($dormList)) {
						foreach ($dormList as $perm) {
							if ($perm['dormitory'] == $dorm['id']) {
								$permission = 1;
								break;
							}
						}
					}
				}
				echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dorm]['.$dorm['id'].']', 'id'=>'adminEdit[dorm]['.$dorm['id'].']', 'value'=>$permission));
			}
			echo $form->_end() . '</div>';
?><script type="text/javascript">
(function (){
	form = document.getElementById('adminEdit_typeId');
	function changeVisibility() { 
		var div = document.getElementById("dorms"); 
		if (form.value == <? echo UFacl_SruWalet_Admin::HEAD; ?>) { 
			div.style.display = "none"; 
			div.style.visibility = "hidden"; 
		} else { 
			div.style.display = "block"; 
			div.style.visibility = "visible"; 
		}
	}
	form.onchange = changeVisibility;
	function selectType(typeId){ 
		form.selectedIndex = (typeId - 11);
	}
	selectType(<? echo $d['typeId']; ?>);
 	changeVisibility();
})()
</script><?
		}
		
		echo $form->_fieldset();
		echo $form->email('E-mail', array('class'=>'required'));
		echo $form->phone('Telefon');
		echo $form->gg('GG', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Numer w komunikatorze GG." /><br/>'));
		echo $form->jid('Jabber', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Adres w komunikatorze sieci Jabber." /><br/>'));
		echo $form->address('Adres', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" alt="?" title="Lokalizacja lub miejsce przebywania administratora." /><br/>'));

?>
<script>
$("#main img[title]").tooltip({ position: "center right"});
</script>
<?
	}

	public function waletBar(array $d, $ip, $time) {
		echo '<a href="'.$this->url(0).'/admins/'.$d['id'].'">'.$this->_escape($d['name']).'</a> ';
		if (!is_null($time) && $time != 0 ) {
			echo 'Ostatnie&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $time).' ' ;
		}
		if (!is_null($ip)) {
			echo '('.$ip.') ';
		}
	}
}
