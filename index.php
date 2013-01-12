<?php 
	session_start();

	// Si es `true` se mostrarán errores, y si es `false` no
	define('DEVELOPEMENT_MODE', true);

	// Definir los directorios
	if( ! defined('DIRECTORY_SEPARATOR') ) {
		define('DIRECTORY_SEPARATOR', '/');
	}
	define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);


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