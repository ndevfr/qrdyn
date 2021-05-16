<?php
// Informations generales
const VERSION = '1.0.25';
include("config.php");

$plugins_home = array();
$plugins_home_connected = array();
$plugins_edit = array();
$plugins_manage = array();

function _e( $text ) {
	echo gettext($text);
}

function __( $text ) {
	return gettext($text);
}

function noBreakLines($str){
	return str_replace(array("\r", "\n"), "", $str);
}

function noQuotes($str){
	return str_replace(array("\""), "", $str);
}

function formName($str){
	return str_replace(array("\"", "'", " "), "", $str);
}

function onlyAlphaNum($str){
	return $chaine = preg_replace('#[^a-zA-Z0-9-]#u', "", $str);
}

function onlyOneLetter($str){
	return substr(onlyAlphaNum($str),0,1);
}

function onlyTwoChars($str){
	return substr(onlyAlphaNum($str),0,2);
}

function str2bdd($str){
	return $str;
}

function str2html($str){
	return htmlentities($str);
}

function sql_select($table, $where, $order) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$where[1] = $link->real_escape_string($where[1]);
	$sql = "SELECT * FROM `".DB_PREF.$table."` WHERE ".$where[0]." = '".$where[1]."' ".$order.";";
	$result = $link->query($sql);
	$results = array();
	while($r = $result->fetch_array()){
		$results[] = $r;
	}
	mysqli_close($link);
	return $results;
}

function sql_select_unique($table, $where) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$where[1] = $link->real_escape_string($where[1]);
	$sql = "SELECT * FROM `".DB_PREF.$table."` WHERE ".$where[0]." = '".$where[1]."';";
	$result = $link->query($sql);
	if($result->num_rows){
		$r = $result->fetch_array();
		mysqli_close($link);
		return $r;
	} else {
		mysqli_close($link);
		return false;
	}
}

function sql_exec($sql) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if($result = $link->query($sql)){
		mysqli_close($link);
		return true;
	} else {
		mysqli_close($link);
		return false;
	}
}

function sql_insert($table, $vars){
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	foreach($vars as $k => $v){
		$vars[$k] = $link->real_escape_string($vars[$k]);
	}
	echo $sql = "INSERT INTO `".DB_PREF.$table."` (".implode(", ", array_keys($vars)).") VALUES ('".implode("','", array_values($vars))."');";
	if($result = $link->query($sql)){
		mysqli_close($link);
		return true;
	} else {
		mysqli_close($link);
		return false;
	}
}

function sql_update($table, $vars, $where){
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$changes = array();
	foreach($vars as $k => $v){
		$changes[] = $k." = '".$link->real_escape_string($vars[$k])."'";
	}
	$where[1] = $link->real_escape_string($where[1]);
	$sql = "UPDATE `".DB_PREF.$table."` SET ".implode(",", array_values($changes))." WHERE ".$where[0]." = '".$where[1]."';";
	if($result = $link->query($sql)){
		mysqli_close($link);
		return true;
	} else {
		mysqli_close($link);
		return false;
	}
}

function sql_delete($table, $where){
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$changes = array();
	$where[1] = $link->real_escape_string($where[1]);
	$sql = "DELETE FROM `".DB_PREF.$table."` WHERE ".$where[0]." = '".$where[1]."';";
	if($result = $link->query($sql)){
		mysqli_close($link);
		return true;
	} else {
		mysqli_close($link);
		return false;
	}
}

function sql_count($table, $where) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$where[1] = $link->real_escape_string($where[1]);
	$sql = "SELECT * FROM `".DB_PREF.$table."` WHERE ".$where[0]." = '".$where[1]."';";
	$result = $link->query($sql);
	mysqli_close($link);
	return $result->num_rows;
}

function connect($mail, $pass) {
	global $error;
	$mail = addslashes(strtolower($mail));
	if($user = sql_select_unique("users", array("mail", $mail))){
		$id = $user['id'];
		$hash = $user['password'];
		if(password_verify(PREF.$pass.SUFF, $hash)){
			$_SESSION['user'] = $user['id'];
			$_SESSION['name'] = $user['mail'];
			return true;
		} else {
			$error = __("Identifiants inconnus.");
			$_SESSION['user'] = "";
			$_SESSION['name'] = "";
			return false;
		}
	}
}

function connected() {
	if(!empty($_SESSION['user'])) {
		return true;
	} else {
		$_SESSION['user'] = "";
		$_SESSION['name'] = "";
		return false;
	}
}

function userId(){
	return (int)$_SESSION['user'];
}

function userInfo($id) {
	if($user = sql_select_unique("users", array("id", $id))){
		return $user;
	} else {
		return false;
	}		
}

function userLinks($id = false, $order = "time") {
	if($id == false){
		if(!empty($_SESSION['user'])) {
			$id = $_SESSION['user'];
		}
	}
	if($id != false) {
		$links = sql_select("links", array("owner", $id), "ORDER BY ".$order." DESC;");
		$linksId = array();
		foreach($links as $l){
			$linksId[] = $l['id'];
		}
		return $linksId;
	} else {
		return false;
	}
}

function linkInfos($id = false){
	if($qr = sql_select_unique("links", array("id", $id))){
		$links = explode("\r\n", $qr['links']);
		$newlinks = array();
		foreach($links as $link){
			$l = explode("|", $link);
			if(!empty($l[1])){
				$l[0] = str2html(utf8_encode($l[0]));
				$l[1] = str2html($l[1]);
				$newlinks[] = $l;
			}
		}
		$qr['title'] = str2html(utf8_encode($qr['title']));
		$qr['description'] = str2html(utf8_encode($qr['description']));
		$qr['links'] = $newlinks;
		return $qr;
	} else {
		return false;
	}
}

function removeLink($id){
	global $uploaddir;
	return sql_delete("links", array("id", $id));
}

function removeAccount($id = false){
	global $error;
	if($id == false){
		if(!empty($_SESSION['user'])) {
			$id = $_SESSION['user'];
		}
	}
	if($id != false){
		$links = userLinks($id);
		$ok = true;
		foreach($links as $lid){
			if(!removeLink($lid)){
				$ok = false;
			}
		}
		if($ok){
			if(!sql_delete("users", array("id", $id))){
				$ok = false;
			}
		}
		if($ok){
			$error = __("Compte supprimé.");
			if($id == $_SESSION['user']){
				$_SESSION = array();
			}
		} else {
			$error = __("Problème lors de la suppression.");
		}
	} else {
		$error = __("Problème lors de la suppression.");
		return false;
	}
}

function newId(){
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';
	for($i=0; $i<6; $i++){
		$string .= $chars[rand(0, strlen($chars)-1)];
	}
	return $string;
}

function uniqueId($id){
	$idExists = sql_count("links", array("id", $id));
	if($idExists == 0){
		return true;
	} else {
		return false;
	}
}

function isLockee($url){
	$vid = "";
	if( strpos($url, 'lockee.fr/o/') !== false ){
		$vid = $url;
	}
	return $vid;
}

function isvideo($url){
	$vid = "";
	// Youtube
	if( (strpos($url, 'youtube') !== false) || (strpos($url, 'youtu.be') !== false) ){
		$regex = '/^https?:\/\/(?:(?:www|m)\.)?(?:youtube\.com\/watch(?:\?v=|\?.+?&v=)|youtu\.be\/)([a-z0-9_-]+)$/i';
		if ( preg_match($regex, $url, $results) ) {
			$id = $results[1];
			$vid = 'https://www.youtube.com/embed/'.$id.'?rel=0&showinfo=0&theme=light';
		}
	// Vimeo
	} else if( strpos($url, 'vimeo.com') !== false ){
		$regex = '/^https?:\/\/(?:www\.)?vimeo\.com.+?(\d+).*$/i';
		if ( preg_match($regex, $url, $results) ) {
			$id = $results[1];
			$vid = 'https://player.vimeo.com/video/'.$id;
		}
	// Dailymotion
	} else if( (strpos($url, 'dai.ly') !== false) || (strpos($url, 'dailymotion') !== false) ){
		$regex = '/^https?:\/\/(?:www\.)?(?:dai\.ly\/|dailymotion\.com\/(?:.+?video=|(?:video|hub)\/))([a-z0-9]+)$/i';
		if ( preg_match($regex, $url, $results) ) {
			$id = $results[1];
			$vid = 'https://www.dailymotion.com/embed/video/'.$id.'?queue-autoplay-next=false&queue-enable=false&sharing-enable=false&ui-theme=false';
		}
	// Mediacad Ac-Nantes
	} else if( strpos($url, 'mediacad.ac-nantes.fr') !== false ) {
		$regex = '/^https?:\/\/mediacad.ac-nantes.fr\/m\/([0-9]+)$/i';
		if ( preg_match($regex, $url, $results) ) {
			$id = $results[1];
			$vid = 'https://mediacad.ac-nantes.fr/m/'.$id.'/d/i';
		}
	}
	return $vid;
}

function displayControls($cid, $exclude = "", $txt = 0){
	$controls = "";
	if($txt == 1){
		$ca = "&nbsp;".__("Aller");
		$cv = "&nbsp;".__("Voir");
		$cm = "&nbsp;".__("Modifier");
		$cs = "&nbsp;".__("Supprimer");
		$ce = "&nbsp;".__("Exporter");
	} else {
		$ca = "";
		$cv = "";
		$cm = "";
		$cs = "";
		$ce = "";
	}
	$controls .= "<a class='controls' href='".SITE_URL."$cid' target='_blank' title='".__("Aller")."'><div class='img-controls o'></div><div class='l'>$ca</div></a>";
	if($exclude != "v"){
		$controls .= "<a class='controls' href='".SITE_URL."v/$cid' title='".__("Voir")."'><div class='img-controls v'></div><div class='l'>$cv</div></a>";
	}
	if($exclude != "m"){
		$controls .= "<a class='controls' href='".SITE_URL."m/$cid' title='".__("Modifier")."'><div class='img-controls m'></div><div class='l'>$cm</div></a>";
	}
	/* if($exclude != "e"){
		$controls .= "<a class='controls' href='".SITE_URL."e/$cid' title='".__("Exporter")."'><div class='img-controls e'></div><div class='l'>$ce</div></a>";
	}*/
	if($exclude != "s"){
		$controls .= "<a class='controls' href='".SITE_URL."s/$cid' title='".__("Supprimer")."'><div class='img-controls s'></div><div class='l'>$cs</div></a>";
	}
	return $controls;
}

function tobase62($number){
	$tobase = 62;
	$map = implode('',array_merge(range(0,9),range('a','z'),range('A','Z')));
	$map_base = substr($map,0,$tobase);
	$tobase = strlen($map_base);
	$result = '';
	while ($number >= $tobase) {
		$result = $map_base[$number%$tobase].$result;
		$number /= $tobase;
	}
	return $map_base[$number].$result;		
}


function creation_token(){
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$hash = $_SESSION['user'].time();
	$token = tobase62($hash);
	while(strlen($token)<100){
		$token .= $chars[rand(0, strlen($chars)-1)];
	}
	return $token;
}

function connect_token($token) {
	if(!empty($token)){
		if($user = sql_select_unique("users", array("token", $token))){
			$_SESSION['user'] = $user['id'];
			$_SESSION['name'] = $user['mail'];
			return true;
		} else {
			$error = __("Token introuvable.");
			$_SESSION['user'] = "";
			$_SESSION['name'] = "";
			return false;
		}
	} else {
		return false;
	}
}

function add_to_home($html, $connected = true, $order = 0){
	global $plugins_home_connected, $plugins_home;
	$plugins_home_connected[$order][] = $html;
	if(!$connected){
		$plugins_home[$order][] = $html;
	}
}

function add_to_edit($html, $order = 0){
	global $plugins_edit;
	$plugins_edit[$order][] = $html;
	if(!$connected){
		$plugins_edit[$order][] = $html;
	}
}

function add_to_manage($html, $order = 0){
	global $plugins_manage;
	$plugins_manage[$order][] = $html;
	if(!$connected){
		$plugins_manage[$order][] = $html;
	}
}

//add_to_home("<a href='test/' class='menu-button'>Test menu 5</a>", false, 5);
?>