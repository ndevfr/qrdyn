<?php
include("header.php");
$id = $_GET['id'];
$r = linkInfos($id);
$links = $r['links'];
echo "<div id='page-back'><div id='page-header'><div id='page-title'>".$r['title']."</div>";
echo "<div id='page-description'>".$r['description']."</div></div></div><div id='shadow'>";
foreach($links as $link){
	$url = $link[1];
	$title = $link[0];
	echo "<div class='card-link'>";
	$vid = isvideo($url);
	if (!empty($vid)) {
		echo "<div class='card-title'>".$title."</div>";
		echo "<div class='video'><iframe class='video-player' width='560' height='315' src='$vid' title='$title' frameborder='0' allowfullscreen></iframe></div>";
	} else {
		echo "<div class='card-title'>".$title."</div>";
		echo "<a class='active-btn' href='$url'>Accéder à la ressource</a>";
	}
	echo "</div>";
}
echo "</div>";
include('footer.php');
?>
</body>
</html>