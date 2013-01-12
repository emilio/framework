<?php
/*
 * Definir la url base
 */
if( '/' === DIRECTORY_SEPARATOR ) {
	define('BASE_ABSOLUTE_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH));
} else {
	define('BASE_ABSOLUTE_URL', str_replace(DIRECTORY_SEPARATOR, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH)));
}
define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . BASE_ABSOLUTE_URL);

// Configurar el cargado automático de clases
spl_autoload_register(function($name) {
	if( file_exists($file = Config::get('path.includes') . $name . '.php') ) {
		include $file;
	} elseif (file_exists($file = Config::get('path.models') . strtolower($name) . '.php' ) ) {
		include $file;
	}
});


if( Config::get('url.pretty') ) {
	$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/' . str_replace(array(
																				BASE_ABSOLUTE_URL,
																				(isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : null)
																			), '', $_SERVER['REQUEST_URI']);
	if( is_null($path) ){
		if( ! Config::get('url.rewrite')) {
			return Redirect::to( Url::get(), 301 );
		} else {
			$path = '/';
		}
	}


	$path_array = array_filter(explode('/', $path));
	
	$controller = array_shift($path_array);

	$action = array_shift($path_array);

	$args = $path_array;

	// Forzar las urls para una barra
	if( $path[strlen($path)-1] !== '/' ) {
		Redirect::to(Url::get($controller . '@' . $action, $args));
	}

	unset($path_array);
	unset($path);
} else {
	$controller = Param::get('c');
	$action = Param::get('action');
	$args = Param::get('params');
	if( $args ) {
		$args = array_filter(explode(';', $args));
	}
}

/*
 * Comprobación home
 */
if( ! $controller ) {
	$controller = 'home';
}

if( ! $action ) {
	$action = 'index';
}

if( ! $args ) {
	$args = array();
}

// Definir la url actual
define('CURRENT_URL', Url::get($controller . '@' . $action, $args));

$controller_path = Config::get('path.controllers');
if( file_exists($controller_path . $controller . '.php') ) {
	require $controller_path . $controller . '.php';
	$class = ucfirst($controller) . '_Controller';
// Si el controlador no existe, comprobamos para ver si es el home, con una acción que ahora está en $controller
} else {
	require  $controller_path . 'home.php';
	$class = 'Home_Controller';
	if( method_exists($class, 'action_' . $controller) ) {

		if( Config::get('url.pretty') && $action !== 'index') {
			array_unshift($args, $action);
		}
		$action = $controller;
		$controller = 'home';
	} else {
		return Response::error(404);
		exit;
	}
}
unset($controller_path);
// Opcional una función global
if( method_exists($class, 'global') ) {
	call_user_func(array($class, 'global'));
}

if( method_exists($class, 'action_' . $action) ) {
	define('PAGE_CONTROLLER', $controller);
	define('PAGE_ACTION', $action);
	call_user_func_array(array($class, 'action_' . $action), $args);
} else {
	return Response::error(404);
};