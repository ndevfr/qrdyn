<?php 
include("header.php");
echo '<div id="main">';
echo '<h1>'.__("Générer un QR-code statique").'</h1>';
echo "<input type='text' id='inputlink' name='inputlink' class='field' placeholder='https://...' />";
echo "<div id='qrcode'></div>";
?>
<script type="text/javascript" src="<?php echo SITE_URL; ?>js/qrmake.min.js?v=<?php echo VERSION; ?>"></script>
<?php
include('footer.php');
?>