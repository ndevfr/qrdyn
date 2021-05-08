<?php 
include("header.php");
echo '<div id="main">';
if(connected()): 
	$error = "";
	$user = userInfo(userId());
	$mail = $user['mail'];
	$id = intval($user['id']);
	if(!empty($_POST['editAccount'])){
		$error = __("Modification(s) effectuée(s).");
		if(!empty($_POST['password'])){
			if($_POST['password'] == $_POST['password2']){
				$password = password_hash(PREF.$_POST['password'].SUFF, PASSWORD_BCRYPT);
				sql_exec("UPDATE `".DB_PREF."users` SET password = '$password' WHERE id = $id;");
			} else {
				$error = __("Les deux mots de passe sont différents.");
			}
		}
		if( $_POST['mail'] != $mail ){
			if(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
				$mail = addslashes(strtolower($_POST['mail']));
				sql_exec("UPDATE `".DB_PREF."users` SET mail = '$mail' WHERE id = $id;");
			} else {
				$error = __("L'adresse mail n'est pas valide.");
			}
		}	
	}
	if(!empty($_POST['regenToken'])){
		$newToken = creation_token();
		sql_exec("UPDATE `".DB_PREF."users` SET token = '$newToken' WHERE id = $id;");
	}
	if(!empty($_POST['removeAccount'])){
		if(!empty($_POST['password'])){
			if(connect($_SESSION['name'], $_POST['password'])){
				removeAccount();
			} else {
				$error = __("Le mot de passe est incorrect.");
			}
		} else {
			$error = __("Le mot de passe n'a pas été saisi.");
		}
	}
	?>
	<h1><?php _e("Gérer mon compte"); ?></h1>
	<form action="" name="editingAccount" method="POST">
	<p><?php _e("Changer l'adresse mail :"); ?></p>
	<input type="mail" class="field" placeholder="<?php _e("Adresse mail"); ?>" name="mail" autocomplete="off" required pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.[a-zA-Z.]{2,15}" title="<?php _e("Veuillez saisir une adresse mail valide."); ?>" />
	<p><?php _e("Changer le mot de passe (entraine la déconnection) :"); ?></p>
	<input type="password" class="field" placeholder="<?php _e("Mot de passe"); ?>" name="password" autocomplete="off" pattern="(?=^.{6,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" title="<?php _e("Veuillez saisir un mot de passe de plus de six caractères contenant au moins un chiffre, une minuscule et une majuscule."); ?>" />
	<input type="password" class="field" placeholder="<?php _e("Répéter le mot de passe"); ?>" name="password2" autocomplete="off" pattern="(?=^.{6,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" title="<?php _e("Veuillez saisir un mot de passe de plus de six caractères contenant au moins un chiffre, une minuscule et une majuscule."); ?>" />
	<label><em><?php _e("Le mot de passe doit contenir 6 caractères dont au moins un chiffre, une minuscule et une majuscule."); ?></em></label><br />
	<input type="submit" name="editAccount" class="menu-button" value="<?php _e("Modifier le compte"); ?>" />
	</form>
	<h1><?php _e("Générer un nouveau token de connexion :"); ?></h1>
	<p><?php _e("Token actuel :"); ?></p>
	<?php if(TOKEN_ALLOW): ?>
	<textarea id="token" onclick="this.select();document.execCommand('copy');"><?php echo $user['token']; ?></textarea>
	<form action="" name="generatingToken" method="POST">
	<input type="submit" name="regenToken" class="menu-button" value="<?php _e("Générer un nouveau token"); ?>" />
	</form>
	<?php endif; ?>
	<h1><?php _e("Supprimer mon compte"); ?></h1>
	<form action="" name="removingAccount" method="POST">
	<p><?php _e("Mot de passe du compte :"); ?></p>
	<input type="password" class="field" placeholder="<?php _e("Mot de passe"); ?>" name="password" autocomplete="off" required /><br />
	<input type="submit" name="removeAccount" class="menu-button" value="<?php _e("Supprimer le compte"); ?>" />
	</form>
	<div id="errors"><p><?php echo $error; ?></p></div>
	<?php
else:
	echo "<p>".__("Non connecté.").'</p><p><a href="'.$HOME.'">'.__("Retour à la page d'accueil")."</a></p>";
endif;
echo "</div>";
include("footer.php");
?>
