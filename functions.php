<?php
// Informations generales
const VERSION = '1.0.03';
include("config.php");

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

function sql_select($sql) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = $link->query($sql);
	$results = array();
	while($r = $result->fetch_array()){
		$results[] = $r;
	}
	mysqli_close($link);
	return $results;
}

function sql_select_unique($sql) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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

function sql_count($sql) {
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = $link->query($sql);
	mysqli_close($link);
	return $result->num_rows;
}

function connect($mail, $pass) {
	global $error;
	$mail = addslashes(strtolower($mail));
	$connected = false;
	if($user = sql_select_unique("SELECT * FROM `".DB_PREF."users` WHERE mail = '$mail';")){
		$id = $user['id'];
		$hash = $user['password'];
		if(password_verify(PREF.$pass.SUFF, $hash)){
			$connected = true;
		} else {
			echo "pb";
		}
	}
	if(!$connected){
		$error = __("Identifiants inconnus.");
		$_SESSION['user'] = "";
		$_SESSION['name'] = "";
	} else {
		$_SESSION['user'] = $user['id'];
		$_SESSION['name'] = $user['mail'];
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
	if($user = sql_select_unique("SELECT * FROM `".DB_PREF."users` WHERE id = '$id';")){
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
		$links = sql_select("SELECT * FROM `".DB_PREF."links` WHERE owner = '$id' ORDER BY $order DESC;");
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
	if($qr = sql_select_unique("SELECT * FROM `".DB_PREF."links` WHERE id = '$id';")){
		$links = explode("\r\n", $qr['links']);
		$newlinks = array();
		foreach($links as $link){
			$l = explode("|", $link);
			if(!empty($l[1])){
				$l[0] = utf8_encode($l[0]);
				$newlinks[] = $l;
			}
		}
		$qr['title'] = utf8_encode($qr['title']);
		$qr['description'] = utf8_encode($qr['description']);
		$qr['links'] = $newlinks;
		return $qr;
	} else {
		return false;
	}
}

function removeLink($id){
	global $uploaddir;
	return sql_exec("DELETE FROM `".DB_PREF."links` WHERE id = '$id';");
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
	$idExists = sql_count("SELECT * FROM `".DB_PREF."links` WHERE id = '$id';");
	if($idExists == 0){
		return true;
	} else {
		return false;
	}
}

function isvideo($url){
	$vid = "";
	// Youtube
	if( (strpos($url, 'youtube') !== false) || (strpos($url, 'youtu.be') !== false) ){
		$regex = '/^https?:\/\/(?:(?:www|m)\.)?(?:youtube\.com\/watch(?:\?v=|\?.+?&v=)|youtu\.be\/)([a-z0-9_-]+)$/i';
		if ( preg_match($regex, $url, $results) ) {
			$id = $results[1];
			$vid = 'https://www.youtube.com/embed/'.$id.'?rel=0showinfo=0&theme=light';
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
?>