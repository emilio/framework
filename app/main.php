<?php


if( defined('DEVELOPEMENT_MODE') && DEVELOPEMENT_MODE ) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

/*
 * Definir la url base
 */
if( '/' === DIRECTORY_SEPARATOR ) {
	define('BASE_ABSOLUTE_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH));
} else {
	define('BASE_ABSOLUTE_URL', str_replace(DIRECTORY_SEPARATOR, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH)));
}
define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . BASE_ABSOLUTE_URL);

if( Config::get('url.pretty') ) {
	if( isset($_SERVER['PATH_INFO']) ) {
		$path = $_SERVER['PATH_INFO'];
	} else {
		$path = $_SERVER['REQUEST_URI'];
		if( isset($_SERVER['QUERY_STRING']) ) {
			$path = str_replace('?' . $_SERVER['QUERY_STRING'], '', $path);
		}

		if( $path === BASE_ABSOLUTE_URL ) {
			$path = '/';
		} else {
			$path = substr($path, strlen(BASE_ABSOLUTE_URL));
		}
	}
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
		if( $action !== 'index' ) {
			$args = array($controller, $action);
		} else {
			$args = array($controller);
		}
		$controller = 'home';
		$action = 'index';
	}
}
unset($controller_path);

if( method_exists($class, 'action_' . $action) ) {
	$reflection = new ReflectionMethod($class, 'action_' . $action);
	$number_of_arguments = count($args);

	// Si hay más argumentos de los esperados o menos de los requeridos, lanzamos un error 404
	if( $number_of_arguments > $reflection->getNumberOfParameters() || $number_of_arguments < $reflection->getNumberOfRequiredParameters()) {
		return Response::error(404)->render(true);
	}

	// Si no, lanzamos la aplicación
	define('PAGE_CONTROLLER', $controller);
	define('PAGE_ACTION', $action);

	// Opcional una función global
	if( method_exists($class, 'all') ) {
		call_user_func(array($class, 'all'));
	}

	$return = call_user_func_array(array($class, 'action_' . $action), $args);

	if( $return instanceof View ) {
		return $return->render(true);
	}

	echo $return;
} else {
	return Response::error(404)->render(true);
};