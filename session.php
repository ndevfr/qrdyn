<?php
session_set_cookie_params([
	'lifetime' => 0,
	'path' => '/',
	'domain' => $_SERVER['HTTP_HOST'],
	'secure' => TRUE,
	'httponly' => TRUE
]);
session_name('qr');
session_start();

if(TOKEN_ALLOW){
	if((!empty($_GET['token']))&&(!connected())){
		$token = onlyAlphaNum($_GET['token']);
		connect_token($token);
	}
}
?>