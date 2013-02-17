<?php namespace EC;
use ReflectionMethod,
	EC\HTTP\Url,
	EC\HTTP\Response,
	EC\HTTP\Param,
	EC\HTTP\Redirect;


class App {
	public static $controller;
	public static $action;
	public static $args;
	public static $class;
	public static $url;

	public static function devCheck() {
		if( defined('DEVELOPEMENT_MODE') && DEVELOPEMENT_MODE ) {
			error_reporting(E_ALL);
			ini_set('display_errors', 'On');
		}
	}

	public static function start() {
		self::devCheck();
		self::parse_url();
	}

	public static function parse_url() {
		if( Config::get('url.pretty') ) {
			$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
			if( $path === BASE_ABSOLUTE_URL ) {
				$path = '/';
			} else {
				$path = substr($path, strlen(BASE_ABSOLUTE_URL));
			}


			$path_array = array_filter(explode('/', $path));
			
			$controller = array_shift($path_array);

			$action = array_shift($path_array);

			$args = $path_array;

			// Forzar las urls para una barra
			if( $path[strlen($path)-1] !== '/' ) {
				Redirect::to(Url::get($controller . '@' . $action, $args, isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null));
			}
		} else {
			$controller = Param::get('c');
			$action = Param::get('action');
			$args = Param::get('params');
			if( $args ) {
				$args = array_filter(explode(';', $args));
			}
		}

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
			if( method_exists('\\' . $class, 'action_' . $controller) ) {

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

		// Set the data 
		self::$controller = $controller;
		self::$action = $action;
		self::$args = $args;
		self::$class = $class;
		self::$url = Url::get($controller . '@' . $action, $args);
	}

	public static function render() {
		if( ! method_exists( '\\' . self::$class, 'action_' . self::$action) ) {
			return Event::trigger('error.404');
		}

		$reflection = new ReflectionMethod( '\\' . self::$class, 'action_' . self::$action);
		$number_of_arguments = count(self::$args);

		// Si hay más argumentos de los esperados o menos de los requeridos, lanzamos un error 404
		if( $number_of_arguments > $reflection->getNumberOfParameters() || $number_of_arguments < $reflection->getNumberOfRequiredParameters()) {
			return Event::trigger('error.404');
		}

		// Opcional una función global
		if( method_exists(self::$class, 'all') ) {
			call_user_func(array(self::$class, 'all'));
		}

		$return = call_user_func_array(array(self::$class, 'action_' . self::$action), self::$args);

		if( $return instanceof View || $return instanceof HTTP\Response ) {
			return $return->render(true);
		} else {
			echo $return;
		}
	}

	public static function url() {
		return self::$url;
	}

	public static function action() {
		return self::$action;
	}

	public static function controller() {
		return self::$controller;
	}

	public static function args() {
		return self::$args;
	}

	public static function current_class() {
		return self::$class;
	}
}
