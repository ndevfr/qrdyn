<!DOCTYPE html>
<!-- <?php echo SITE_TITLE; ?> version <?php echo VERSION; ?> -->
<!-- Par Nicolas Desmarets, professeur de mathématiques -->
<!-- Copyright (c) 2021 Desmaths.fr -->
<html lang="fr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="icon" type="image/png" href="<?php echo SITE_RSC; ?>favicon.png">
	<meta charset="utf-8" />
	<title><?php echo SITE_TITLE; ?></title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo SITE_RSC; ?>css/styles.css?v=<?php echo VERSION; ?>" />
<link rel="stylesheet" href="<?php echo SITE_RSC; ?>css/custom.css?v=<?php echo VERSION; ?>" />
	<style type="text/css">
	a, a:hover, a:visited{
		color:<?php echo SITE_COLOR; ?>;
	}
	#header, #page-header, .menu-button, .submit-button, .active-btn{
		background:<?php echo SITE_COLOR; ?>;
	}
	#page-back{
		background:<?php echo SITE_COLOR; ?>33;
	}
	.img-controls{
		background-image:url("<?php echo SITE_RSC; ?>images/controls.svg");
	}
	.qrscan {
		background-image: url("<?php echo SITE_RSC; ?>images/camera.png");
	}
	</style>
	<script src="<?php echo SITE_RSC; ?>js/jquery.min.js?v=<?php echo VERSION; ?>"></script>
	<script src="<?php echo SITE_RSC; ?>js/jquery-qrcode.min.js?v=<?php echo VERSION; ?>"></script>
	<?php if($have_scan): ?>
	<script src="<?php echo SITE_RSC; ?>js/qrscan.min.js?v=<?php echo VERSION; ?>"></script>
	<?php endif; ?>
	<script>var homeURL = "<?php echo SITE_URL; ?>";</script>
</head>
<body>
<div id='header'><img src="<?php echo SITE_LOGO; ?>" id="logo" /> <span id='site-title'><a href="<?php echo SITE_URL; ?>"><?php echo SITE_TITLE; ?></a><?php if($is_link){echo "/".$_GET['id'];} ?></span></div>