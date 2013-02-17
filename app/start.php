<?php namespace EC;
use EC\Database\DB;

	// Obtener la url de la aplicación
	if( '/' === DS ) {
		define('BASE_ABSOLUTE_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH));
	} else {
		define('BASE_ABSOLUTE_URL', str_replace(DS, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH)));
	}

	define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . BASE_ABSOLUTE_URL);

	// Iniciar la autocarga de clases y registrar nuestro namespace
	include 'Autoloader.php';

	Autoloader::add_namespace('EC', BASE_PATH . 'app/');
	Autoloader::register();

	// Obtener la configuración inicial
	Config::init();

	// Configurar la autocarga de clases
	Autoloader::defaultPath(Config::get('path.includes'));
	Autoloader::$aliases = Config::get('aliases');

	// iniciar los eventos
	include BASE_PATH . 'events.php';
	


	// Conectamos con la base de datos
	DB::config(Config::get('database'));
	DB::connect();

	// Cargar los modelos automáticamente
	foreach (glob(Config::get('path.models') . '*.php', GLOB_NOSORT) as $file) {
		require $file;
	}

	// Iniciarlo todo
	App::start();

	// Y mostrarlo
	App::render();