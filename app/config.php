<?php
class Config {
	protected static $config;
	public static function init() {
		if( ! self::$config ) {
			self::$config = (include BASE_PATH . 'config.php');
		}
		
		// Incluir la base de datos
		include BASE_PATH . 'app/DB.php';
		include BASE_PATH . 'app/DBObject.php';
		include BASE_PATH . 'app/Query.php';


		// Conectamos con la base de datos
		DB::config(self::get('database'));
		DB::connect();

		// Definimos algunas constantes importantes
		foreach (array( 'cache','includes', 'models', 'controllers', 'views', 'assets')  as $path) {
			self::$config['path'][$path . '_orig'] = self::$config['path'][$path];
			self::$config['path'][$path] = BASE_PATH . self::get('path.' . $path) . '/';
		}

		// Cargar los modelos automáticamente
		foreach (glob(self::get('path.models') . '*.php', GLOB_NOSORT) as $file) {
			include $file;
		}
		
		// Configurar el cargado automático de clases
		spl_autoload_register(function($name) {
			require (Config::get('path.includes') . $name . '.php');
		});

		Cache::configure(array(
			'cache_path' => self::get('path.cache'),
			'expires' => self::get('cache.expires')
		));

	}
	public static function get($key) {
		if( ! self::$config ) {
			self::init();
		}

		if( false !== strpos($key, '.') ) {
			list( $first, $second ) = explode('.', $key);
			return self::$config[$first][$second];
		}

		return self::$config[$key];
	}
}