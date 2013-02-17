<?php 
	session_start();

	// Si es `true` se mostrarán errores, y si es `false` no
	define('DEVELOPEMENT_MODE', false);

	// Abreviar DIRECTORY_SEPARATOR
	define('DS', DIRECTORY_SEPARATOR);

	// Definir los directorios
	define('BASE_PATH', dirname(__FILE__) . DS);


	/*
	 * Define otras constantes usadas en la aplicación aquí
	 */


	// Incluir el archivo que procesará la aplicación
	include BASE_PATH . 'app/start.php';