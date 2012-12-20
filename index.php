<?php 
	error_reporting( E_ALL );
	session_start();

	// Definir los directorios
	define('BASE_PATH', dirname(__FILE__) . '/');
	define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH));



	/*
	 * Define otras constantes usadas en la aplicación aquí
	 */
	define('SITE_NAME', 'Emilio Cobos-CMC');
	// Definir la ip del visitante
	define('CURRENT_USER_IP',  ! empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] :
			( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] ));


	// Incluir el archivo que procesará la aplicación
	require BASE_PATH . 'app/config.php';
	require BASE_PATH . 'app/main.php';