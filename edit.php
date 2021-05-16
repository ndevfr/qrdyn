<?php 
if(connected()){
	if(!empty($_GET['id'])){
		$id = $_GET['id'];
		$modif = true;
	} else {
		$id = "";
		$modif = false;
	}
	if((($modif)&&(in_array($id, userLinks())))||(!$modif)){
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
					$postlinks .= addslashes(noQuotes(utf8_decode($_POST['inputtitlelink'][$i])))."|".addslashes(noQuotes(utf8_decode($_POST['inputurllink'][$i])))."\r\n";
				}
			}
			if($modif){
				if(!sql_update("links", array("title" => $posttitle, "description" => $postdescription, "links" => $postlinks, "owner" => $user), array("id", $id))) die(__("Erreur lors de l'enregistrement."));
			} else {
				if(!sql_insert("links", array("id" => $id, "title" => $posttitle, "description" => $postdescription,"links" => $postlinks, "owner" => $user))) die(__("Erreur lors de l'enregistrement."));
			}
			header('Location: '.SITE_URL.'v/'.$id);
		}
	}
}
include("header.php");
echo '<div id="main">';
if(connected()):
	if((($modif)&&(in_array($id, userLinks())))||(!$modif)):
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
					echo "<tr id='trlink-$i'><td><input type='text' id='inputtitlelink-$i' name='inputtitlelink[$i]' class='link-title field' placeholder='...' value=\"$l[0]\" /></td><td><input type='text' id='inputurllink-$i' name='inputurllink[$i]' class='link-url field' placeholder='https://...' value=\"$l[1]\" /></td><td style='width:36px;'><input type='button' onclick='supprlink($i)' class='suppr' value='X' /></td></tr>";
				}
				?>
			</table>
			<a id="addInput" class="active-btn">Ajouter un lien</a>
	    	<table style="width:100%"><tr><td><input type="button" name="cancel" value="Annuler" class="cancel-button" onclick="returnHome();" /></td><td><input type="submit" name="sending" value="Envoyer" class="submit-button" /></td></tr></table>
	    	</form>
	    	<div id="errors"></div>
	    	<script type="text/javascript" src="<?php echo SITE_RSC; ?>js/edit.min.js?v=<?php echo VERSION; ?>"></script>
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