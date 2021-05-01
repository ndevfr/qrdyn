<?php
	include("functions.php");
	
	$path = dirname(__FILE__).'/';

	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	$url = str_replace(SITE_URL, "", "https://".$_SERVER['HTTP_HOST'].$uri_parts[0]);
		
	$regex = '#^(?|([0-9a-zA-Z]+)(*MARK:open)
				  |v/([0-9a-zA-Z]+)(*MARK:manage)
				  |m/([0-9a-zA-Z]+)(*MARK:edit)
				  |s/([0-9a-zA-Z]+)(*MARK:remove)
				  |e/([0-9a-zA-Z]+)(*MARK:export)
			)$#x';
	
	$page_home = false;
	$have_scan = false;
	$no_session = false;
	$is_link = false;
	
	switch($url){
		// NORMAL
		case "":
			$page_home = true;
			$file = "home.php";
			break;
		case "scan/":
			$no_session = true;
			$have_scan = true;
			$file = "scan.php";
			break;
		case "compte/":
			$file = "account.php";
			break;
		case "c/":
			$file = "edit.php";
			break;
		case "qr/":
			$file = "genqr.php";
			break;
		// MATCHES
		default:
			preg_match($regex, $url, $route);
			switch($route['MARK']){
				case "open":
					$_GET['id'] = $route[1];
					$no_session = true;
					$is_link = true;
					$file = "open.php";
					break;
				case "manage":
					$_GET['id'] = $route[1];
					$file = "manage.php";
					break;
				case "edit":
					$_GET['id'] = $route[1];
					$file = "edit.php";
					break;
				case "remove":
					$_GET['id'] = $route[1];
					$_GET['del'] = 1;
					$file = "manage.php";
					break;
				case "export":
					$_GET['id'] = $route[1];
					$file = "export.php";
					break;
				case "recuperation":
					$_GET['key'] = $route[1];
					$file = "recuperation.php";
					break;
				default:
					header('HTTP/1.0 404 Not Found');
					$no_session = true;
					$file = "404.php";
					break;
			}
	}
	if(!$no_session){
		include($path."session.php");
	}
	include($path.$file);
?>