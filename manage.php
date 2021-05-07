<?php 
include("header.php");
echo '<div id="main">';
if(connected()): 
	$id = addslashes($_GET['id']);
	if(!empty($_GET['del'])){
		$del = intval($_GET['del']);
	} else {
		$del = 0;
	}
	if(in_array($id, userLinks())):
		if(!empty($_POST['sending'])){
			if(removeLink($id)){
				header('Location: '.SITE_URL);
			}
		}
	
		$linkInfos = linkInfos($id);
		$thelink = SITE_URL."$id";
		if($del):
			?>
			<form action="" method="POST" name="remove" id="remove">
			<div id="question"><?php _e("Supprimer ce QR-code dynamique ?"); ?></div>
			<table class="pad" id="suppression">
				<tr>
					<td><div class="cancel" onclick="cancelRemove();"><?php _e("non"); ?></div></td>
					<td><div class="confirm" onclick="confirmRemove();"><?php _e("oui"); ?></div></td>
			</table>
			<input type="hidden" name="sending" value="1" />
			</form>
			<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/remove.min.js?v=<?php echo VERSION; ?>"></script>
			<?php
			echo "<h1>".$linkInfos['title']."</h1>";
			echo displayControls($id, "s", 1);
		else:
			echo "<h1>".$linkInfos['title']."</h1>";
			echo displayControls($id, "v", 1);
		endif;
		echo "<h2>".$linkInfos['description']."</h2>";
		echo "<div class='link'><a href='$thelink' target='_blank'>$thelink</a></div>";
		echo "<div id='qrcode'></div>";
		echo "<h2>".__("Liens intégrés")."</h2>";
		echo "<ul>";
		foreach($linkInfos['links'] as $link){
			$url = $link[1];
			$title = $link[0];
			echo "<li>$title : <a href='$url' target='_blank'>$url</a></li>";
		}
		echo "</ul>";
	else:
		echo __("QR-code dynamique inexistant ou appartenant à un autre utilisateur.").'<br /><a href="'.SITE_URL.'">'.__("Retour à la page d'accueil")."</a>";
	endif;
else:
	echo "<p>".__("Non connecté.").'</p><p><a href="'.SITE_URL.'">'.__("Retour à la page d'accueil")."</a></p>";
endif;
echo "</div>";
?>
<script>
jQuery("#qrcode").qrcode({
	render:"image",
	width: 512,
	height: 512,
	ecLevel: 'L',
	minVersion: 1,
	maxVersion: 10,
	radius: 0.5,
	quiet: 2,
	text: "<?php echo $thelink; ?>"
});
</script>
<?php
include("footer.php");
?>
