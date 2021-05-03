<?php 
include("header.php");
echo '<div id="main">';
if(!empty($_GET['id'])){
	$id = $_GET['id'];
	$modif = true;
} else {
	$id = "";
	$modif = false;
}
if(connected()):
	if((($modif)&&(in_array($id, userLinks())))||(!$modif)):
		$oldaction = "";
		if($modif){
			$infos = linkInfos($id);
			$title = $infos['title'];
			$description = $infos['description'];
			$links = $infos['links'];
		}
		if(!empty($_POST['sending'])){
			$user = userId();
			if(!$modif){
				$id = newId();
				while(!uniqueId($id)){
					$id = newId();
				}
			}
			$posttitle = addslashes(noBreakLines(noQuotes(utf8_decode($_POST['inputtitle']))));
			$postdescription = addslashes(noQuotes(utf8_decode($_POST['inputdescription'])));
			$postlinks = "";
			for($i=0; $i<sizeof($_POST['inputurllink']); $i++){
				if(!empty($_POST['inputurllink'][$i])){
					$postlinks .= addslashes(noQuotes(utf8_decode($_POST['inputtitlelink'][$i])))."|".$_POST['inputurllink'][$i]."\r\n";
				}
			}
			echo $postlinks;
			if($modif){
				$sql = "UPDATE `".DB_PREF."links` SET title = '$posttitle', description = '$postdescription', links = '$postlinks' WHERE id = '$id';";
			} else {
				$sql = "INSERT INTO `".DB_PREF."links` (id, title, description, links, owner) VALUES ('$id', '$posttitle', '$postdescription', '$postlinks', $user);";
			}
			if(!sql_exec($sql)) die(__("Erreur lors de l'enregistrement."));
			header('Location: '.SITE_URL.'v/'.$id);
		}
		?>
	    	<form action="" method="POST" enctype="multipart/form-data" id="creer">
	    	<div class="title"><?php _e("Titre du QR-code dynamique :"); ?></div>
			<textarea id="inputtitle" name="inputtitle" rows="3" placeholder="<?php _e("Titre..."); ?>"><?php echo $title; ?></textarea>
	    	<div class="title"><?php _e("Description du QR-code dynamique :"); ?></div>
			<textarea id="inputdescription" name="inputdescription" rows="3" placeholder="<?php _e("Description..."); ?>"><?php echo $description; ?></textarea>
	    	<div class="title"><?php _e("Liens intégrés dans le QR-code dynamique :"); ?></div>
			<table id="links">
				<thead><th>Titre</th><th>URL</th></thead>
				<?php
				for($i=0; $i<max(sizeof($links),1); $i++){
					$l = $links[$i];
					echo "<tr><td><input type='text' id='inputtitlelink-$i' name='inputtitlelink[$i]' class='link-title field' placeholder='...' value=\"$l[0]\" /></td><td><input type='text' id='inputurllink-$i' name='inputurllink[$i]' class='link-url field' placeholder='https://...' value=\"$l[1]\" /></td></tr>";
				}
				?>
			</table>
			<a id="addInput" class="active-btn">Ajouter un lien</a>
	    	<input type="submit" name="sending" value="Envoyer" class="menu-button" />
	    	</form>
	    	<div id="errors"></div>
	    	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/edit.min.js?v=<?php echo VERSION; ?>"></script>
		<?php
	else:
		echo __("QR-code dynamique inexistant ou appartenant à un autre utilisateur.").'<br /><a href="'.SITE_URL.'">'.__("Retour à la page d'accueil")."</a>";
	endif;
else:
	echo "<p>".__("Non connecté.").'</p><p><a href="'.SITE_URL.'">'.__("Retour à la page d'accueil").'</a></p>';
endif;
echo '</div>';
include("footer.php");
?>
