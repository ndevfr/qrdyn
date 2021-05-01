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
?>