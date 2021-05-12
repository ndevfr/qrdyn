<?php 
include("header.php");
$error = "";
if(!empty($_POST['login'])){
	if( (!empty($_POST['mail'])) && (!empty($_POST['password'])) ){
		connect($_POST['mail'], $_POST['password']);
	}
}
if((!empty($_POST['create']))&&(INSC_OPEN)){
	if( (!empty($_POST['mail'])) && (!empty($_POST['password'])) && (!empty($_POST['password2'])) ){
		if(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
			if($_POST['password'] == $_POST['password2']){
				$mail = addslashes(strtolower($_POST['mail']));
				$password = password_hash(PREF.$_POST['password'].SUFF, PASSWORD_BCRYPT);
				$sql = "INSERT INTO `".DB_PREF."users` (mail, password) VALUES ('$mail', '$password');";
				if(sql_exec($sql)){
					$error = __("Le compte a été créé.");
				} else {
					$error = __("Un compte existe déjà pour cette adresse mail.");
				}
			} else {
				$error = __("Les deux mots de passe sont différents.");
			}
		} else {
			$error = __("L'adresse mail n'est pas valide.");
		}
	} else {
		$error = __("Il faut remplir tous les champs.");
	}
}
if(!empty($_POST['logout'])){
	$_SESSION['user'] = "";
	$_SESSION['name'] = "";
}
?>
<div id="main">
<?php 
if(file_exists("install.php")){
	echo "<strong>ATTENTION ! Fichier install.php à supprimer !!";
}
if(connected()):
	?>
	<p><?php echo $_SESSION['name']; ?></p>
	<form action="" name="signout" id="signout" method="POST">
		<input type="submit" class="menu-button" name="logout" value="<?php _e("Se déconnecter"); ?>" />
	</form>
	<a href="c/" class="menu-button"><?php _e("Créer un QR-code dynamique"); ?></a>
	<a href="g/" class="menu-button"><?php _e("Créer un QR-code statique"); ?></a>
	<a href="compte/" class="menu-button"><?php _e("Gérer mon compte"); ?></a>
	<a href="scan/" class="menu-button"><?php _e("Scanner un QR-code"); ?></a>
	<?php
	foreach($plugins_home_connected as $home){
		foreach($home as $h){
			echo $h;
		}
	}
	$userLinks = userLinks();
	echo "<h2 style='display:inline;'><input type='checkbox' id='checkall' name='checkall' class='link' /><label for='checkall'>".__("QR-codes dynamiques")."</label></h2> <a class='controls' onclick='deleteselected();' title='".__("Supprimer la sélection")."'><div class='img-controls s'></div></a><span id='loading'><img src='".SITE_RSC."images/loading.png' /></span><ul id='list-links' class='list-links'>";
	foreach($userLinks as $l){
		$i = linkInfos($l);
		$cname = $i['title'];
		$cid = $i['id'];
		echo "<li class='links-list'><input type='checkbox' id='$cid' name='$fname' class='link child' /><label for='$cid'>$cname</label> ".displayControls($cid, "", 0)."</li>";
	}
	echo "</ul>";
	?>
	<script type="text/javascript">
	var	checkall = $('#checkall');
	var childs = $('.child');
	
	checkall.change(function(e){childs.prop('checked', e.target.checked) }).prop('checked', allchecked());
	
	childs.change(function(){ checkall.prop('checked', allchecked()) });
	
	function allchecked(){
		for (var i = 0; i < childs.length; i++){
			if (!childs[i].checked) return false;
		}
		return true;
	}
	
	function deleteselected(){
		if($(".child:checked").length > 0){
			if(confirm("<?php _e("Êtes-vous sûr de vouloir supprimer ces cadenas ?"); ?>")){
				$("#loading").show();
				var finish = 0;
				var total = $(".child:checked").length;
				var finish = 0;
				var total = $(".child:checked").length;
				$(".child:checked").each(function () {
					var id = $(this).attr("id");
					$.post("ajax-delete.php", {id: id}).done(function(){
						finish++;
						if(finish == total){
							window.location.reload();
						}
					});
				});
			}
		}
	}
	</script>
	<?php 
else:
	?>
	<h1><?php _e("Présentation"); ?></h1>
	<p><?php echo SITE_TITLE.__(" permet de créer des qr-codes dynamiques (avec liens multiples)."); ?></p>
	<a href="g/" class="menu-button"><?php _e("Créer un QR-code statique"); ?></a>
	<a href="scan/" class="menu-button"><?php _e("Scanner un QR-code"); ?></a>
	<?php
	foreach($plugins_home as $home){
		foreach($home as $h){
			echo $h;
		}
	}
	?>
	<div id="ConnectAccount">
	<h1><?php _e("Se connecter"); ?></h1>
	<?php if(INSC_OPEN): ?>
	<p><?php _e("Pas encore de compte ?"); ?> <a onclick="dCreateAccount();" class="hyperlink"><?php _e("Inscrivez-vous"); ?></a></p>
	<?php endif; ?>
	<form action="" name="connection" id="connection" method="POST">
	<input type="text" class="field" placeholder="<?php _e("Adresse mail"); ?>" name="mail" />
	<input type="password" class="field" placeholder="<?php _e("Mot de passe"); ?>" name="password" />
	<input type="submit" name="login" class="menu-button" value="<?php _e("Se connecter"); ?>" />
	</form>
	</div>
	<?php if(INSC_OPEN): ?>
	<div id="CreateAccount"><h1><?php _e("Créer un compte"); ?></h1>
	<p><?php _e("Déjà un compte ?"); ?> <a onclick="dConnectAccount();" class="hyperlink"><?php _e("Connectez-vous"); ?></a></p>
	<form action="" name="creation" id="creation" method="POST">
	<input type="mail" class="field" placeholder="<?php _e("Adresse mail"); ?>" name="mail" autocomplete="off" required pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.[a-zA-Z.]{2,15}" title="<?php _e("Veuillez saisir une adresse mail valide."); ?>" />
	<input type="password" class="field" placeholder="<?php _e("Mot de passe"); ?>" name="password" autocomplete="off" required pattern="(?=^.{6,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" title="<?php _e("Veuillez saisir un mot de passe de plus de six caractères contenant au moins un chiffre, une minuscule et une majuscule."); ?>" />
	<input type="password" class="field" placeholder="<?php _e("Répéter le mot de passe"); ?>" name="password2" autocomplete="off" required pattern="(?=^.{6,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" title="<?php _e("Veuillez saisir un mot de passe de plus de six caractères contenant au moins un chiffre, une minuscule et une majuscule."); ?>" />
	<label><em><?php _e("Le mot de passe doit contenir 6 caractères dont au moins un chiffre, une minuscule et une majuscule."); ?></em></label><br />
	<input type="submit" name="create" class="menu-button" value="<?php _e("Créer un compte"); ?>" />	
	</form></div>
	<?php endif; ?>
	<div id="errors"><p><?php echo $error; ?></p></div>
	<script type="text/javascript" src="<?php echo SITE_RSC; ?>js/index.min.js?v=<?php echo VERSION; ?>" defer></script>
	<?php
endif;
echo "</div>";
include("footer.php");
?>