<?php
// Configurar el cargado automático de clases
spl_autoload_register(function($name) {
	if( file_exists($file = Config::get('path.includes') . $name . '.php') ) {
		include $file;
	} elseif (file_exists($file = Config::get('path.models') . strtolower($name) . '.php' ) ) {
		include $file;
	}
});



if( Config::get('url.pretty') ) {
	$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
	if( is_null($path) ){
		if( ! Config::get('url.rewrite')) {
			Redirect::to( Url::get(), 301 );
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
// Definir la url actual
define('CURRENT_URL', Url::get($controller . '@' . $action, $args));
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
		header("HTTP/1.1 404 Not Found");
		View::make('error.404');
		exit;
	}
}
unset($controller_path);



if( method_exists($class, 'action_' . $action) ) {
	define('PAGE_CONTROLLER', $controller);
	define('PAGE_ACTION', $action);
	
	call_user_func_array(array($class, 'action_' . $action), $args);
} else {
	header("HTTP/1.1 404 Not Found");
	View::make('error.404');
};