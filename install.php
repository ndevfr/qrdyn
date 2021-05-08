<!DOCTYPE html>
<html lang="fr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="icon" type="image/png" href="favicon.png">
	<meta charset="utf-8" />
	<title>Installation</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/styles.css" />
	<style type="text/css">
	a, a:hover, a:visited{
		color:#d92341;
	}
	#header, #page-header, .menu-button, .active-btn{
		background:#d92341;
	}
	#page-back{
		background:#d9234133;
	}
	</style>
</head>
<body>
<?php 
include("functions.php");
$result = "";
if(!empty($_POST['install'])){
	$file = fopen("config.php", "w+");
	fwrite($file,"<?php\r\n");
	fwrite($file,"// Variables de connexion a la BDD\r\n");
	fwrite($file,"const DB_HOST = '".$_POST['bdd_host']."';\r\n");
	fwrite($file,"const DB_NAME = '".$_POST['bdd_name']."';\r\n");
	fwrite($file,"const DB_USER = '".$_POST['bdd_user']."';\r\n");
	fwrite($file,"const DB_PASS = '".$_POST['bdd_pass']."';\r\n");
	fwrite($file,"const DB_PREF = '".$_POST['bdd_pref']."';\r\n");
	fwrite($file,"// Personnalisation de l'interface\r\n");
	fwrite($file,"const SITE_TITLE = '".$_POST['site_title']."';\r\n");
	if(substr($_POST['site_url'], -1) != "/"){
		$_POST['site_url'] = $_POST['site_url']."/";
	}
	fwrite($file,"const SITE_URL = '".$_POST['site_url']."';\r\n");
	fwrite($file,"const SITE_LOGO = '".$_POST['site_logo']."';\r\n");
	fwrite($file,"const SITE_COLOR = '".$_POST['site_color']."';\r\n");
	fwrite($file,"// Salt pour les mots de passe\r\n");
	fwrite($file,"const PREF = '".$_POST['site_pref']."';\r\n");
	fwrite($file,"const SUFF = '".$_POST['site_suff']."';\r\n");
	fwrite($file,"// Connexion avec token possible\r\n");
	fwrite($file,"const TOKEN_ALLOW = ".$_POST['site_token'].";\r\n");
	fwrite($file,"// Inscriptions possibles\r\n");
	fwrite($file,"const INSC_OPEN = ".$_POST['site_insc'].";\r\n");
	fclose($file);
	if(file_exists("config.php")){
		$result .= "Fichier de configuration config.php créé.<br/>";
	} else {
		$result .= "Erreur lors de la création du fichier de configuration config.php.<br />";
	}
	$link = mysqli_connect($_POST['bdd_host'], $_POST['bdd_user'], $_POST['bdd_pass'], $_POST['bdd_name']);
	$sql = "CREATE TABLE `".$_POST['bdd_pref']."links` (`id` VARCHAR(255) NOT NULL, `title` VARCHAR(255) NOT NULL, `description` VARCHAR(255) NOT NULL, `links` MEDIUMTEXT NOT NULL, `owner` INT(11) NOT NULL, `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) DEFAULT CHARSET=utf16;";
	if($link->query($sql) == true){
		$result .= "Table de données ".$_POST['bdd_pref']."links créée.<br />";
	} else {
		$result .= "Erreur lors de la création de la table de données ".$_POST['bdd_pref']."links.<br />";
	}
	$sql = "CREATE TABLE `".$_POST['bdd_pref']."users` (`id` INT(11) NOT NULL AUTO_INCREMENT, `mail` VARCHAR(255) NOT NULL, `password` VARCHAR(255) NOT NULL, `token`  VARCHAR(255) NOT NUL, PRIMARY KEY (`id`)) DEFAULT CHARSET=utf16;";
	if($link->query($sql) == true){
		$result .= "Table de données ".$_POST['bdd_pref']."users créée.<br />";
	} else {
		$result .= "Erreur lors de la création de la table de données ".$_POST['bdd_pref']."users.<br />";
	}
	$password = password_hash($_POST['site_pref'].$_POST['account_pass'].$_POST['site_suff'], PASSWORD_BCRYPT);
	$sql = "INSERT INTO `".$_POST['bdd_pref']."users` (`mail`, `password`) VALUES
	('".$_POST['account_mail']."', '.$password.')";
	if($link->query($sql) == true){
		$result .= "Utilisateur ".$_POST['account_mail']." créé.";
	} else {
		$result .= "Erreur lors de la création de l'utilisateur ".$_POST['account_mail'].".<br />";
	}
	mysqli_close($link);
	$result .= "N'ouvliez pas de supprimer ce fichier install.php du serveur une fois l'installation effectuée.";
}
echo '<div id="main">';
echo '<strong>'.$result.'</strong>';
echo '<form action="" method="POST"><h1>'.__("Installation").'</h1>';
echo '<h2>'.__("Base de données").'</h2>';
echo __("Serveur")." : <input type='text' id='bdd_host' name='bdd_host' class='field' placeholder='localhost' value='".$_POST['bdd_host']."' /><br />";
echo __("Identifiant").": <input type='text' id='bdd_user' name='bdd_user' class='field' placeholder='utilisateur' value='".$_POST['bdd_user']."' /><br />";
echo __("Mot de passe")." : <input type='text' id='bdd_pass' name='bdd_pass' class='field' placeholder='motdepasse' value='".$_POST['bdd_pass']."' /><br />";
echo __("Nom de la base de données")." : <input type='text' id='bdd_name' name='bdd_name' placeholder='nomdelabase' class='field' value='".$_POST['bdd_name']."' />";
echo __("Prefixe pour les tables")." : <input type='text' id='bdd_pref' name='bdd_pref' class='field' placeholder='pref_' value='".$_POST['bdd_pref']."' />";
echo '<h2>'.__("Compte utilisateur").'</h2>';
echo __("Mail")." : <input type='text' id='account_mail' name='account_mail' class='field' placeholder='mail@qr.fr' value='".$_POST['account_mail']."' /><br />";
echo __("Mot de passe")." : <input type='text' id='account_pass' name='account_pass' class='field' placeholder='motdepasse' value='".$_POST['account_pass']."' />";
echo '<h2>'.__("Personnalisation").'</h2>';
echo __("Nom du site")." : <input type='text' id='site_title' name='site_title' class='field' placeholder='qr.desmaths.fr' value='".$_POST['site_title']."' /><br />";
echo __("URL du site")." : <input type='text' id='site_url' name='site_url' class='field' placeholder='https://qr.desmaths.fr/' value='".$_POST['site_url']."' /><br />";
echo __("Logo du site")." : <input type='text' id='site_logo' name='site_logo' class='field' placeholder='https://qr.desmaths.fr/logo.png' value='".$_POST['site_logo']."' /><br />";
echo __("Couleur principale (format hexadecimal)")." : <input type='text' id='site_color' name='site_color' class='field' placeholder='#d92341' value='".$_POST['site_color']."' /><br />";
echo __("Inscriptions possibles")." : <select id='site_insc' name='site_insc' class='field'><option value='false'>Non</option><option value='true'>Oui</option></select>";
echo __("Connexion avec token possible")." : <select id='site_token' name='site_token' class='field'><option value='false'>Non</option><option value='true'>Oui</option></select>";
echo __("Prefixe SALT pour les mots de passe")." : <input type='text' id='site_pref' name='site_pref' class='field' placeholder='prefix4pass' value='".$_POST['site_pref']."' /><br />";
echo __("Suffixe SALT pour les mots de passe")." : <input type='text' id='site_suff' name='site_suff' class='field' placeholder='suffix4pass' value='".$_POST['site_suff']."' /><br />";
echo "<input type='submit' name='install' class='menu-button' value='".__("Installer")."' /></form>";

?>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/qrmake.min.js?v=<?php echo VERSION; ?>"></script>
<?php
include('footer.php');
?>
