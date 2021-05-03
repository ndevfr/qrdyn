<?php
include("header.php");
$id = $_GET['id'];
$r = linkInfos($id);
if($r != false){
	$links = $r['links'];
	echo "<div id='page-back'><div id='page-header'><div id='page-title'>".$r['title']."</div>";
	echo "<div id='page-description'>".$r['description']."</div></div></div><div id='shadow'>";
	foreach($links as $link){
		$url = $link[1];
		$title = $link[0];
		echo "<div class='card-link'>";
		$vid = isvideo($url);
		$loc = isLockee($url);
		if (!empty($vid)) {
			echo "<div class='card-title'>".$title."</div>";
			echo "<div class='video'><iframe class='video-player' width='560' height='315' src='$vid' title='$title' frameborder='0' allowfullscreen></iframe></div>";
		} else if (!empty($loc)) {
			echo "<div class='card-title'>".$title."</div>";
			echo "<div class='lockee'><iframe src='$loc' title='$title' height='500' width='100%' frameborder='0' allowfullscreen></iframe></div>";
		} else {
			echo "<div class='card-title'>".$title."</div>";
			echo "<a class='active-btn' href='$url'>Accéder à la ressource</a>";
		}
		echo "</div>";
	}
	echo "</div>";
} else {
	$links = $r['links'];
	echo "<div id='page-back'><div id='page-header'><div id='page-title'>".__("Désolé, ce lien n'existe pas...")."</div>";
	echo "<div id='page-description'>".__("Vérifiez que vous n'avez pas fait d'erreur en le recopiant ou utilisez le QR-code associé s'il est fournit...")."</div></div></div><div id='shadow'>";
	$url = $link[1];
	$title = $link[0];
	echo "<div class='card-link'>";
	echo "<a class='active-btn' href='".SITE_URL."'>".__("Retour à la page d'accueil")."</a>";
	echo "</div>";
	echo "</div>";
}
include('footer.php');
?>
