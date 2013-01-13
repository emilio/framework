<?php 
	session_start();

	// Si es `true` se mostrarán errores, y si es `false` no
	define('DEVELOPEMENT_MODE', false);

	// Definir los directorios
	if( ! defined('DIRECTORY_SEPARATOR') ) {
		define('DIRECTORY_SEPARATOR', '/');
	}
	define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);


	/*
	 * Define otras constantes usadas en la aplicación aquí
	 */


	// Incluir el archivo que procesará la aplicación
	require BASE_PATH . 'app/config.php';
	require BASE_PATH . 'app/main.php';