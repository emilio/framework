<?php
class Config {
	protected static $config;
	public function init() {
		if( ! self::$config ) {
			self::$config = (include BASE_PATH . 'config.php');
		}
		
		// Incluir la base de datos
		include BASE_PATH . 'app/DB.php';
		include BASE_PATH . 'app/DBObject.php';
		include BASE_PATH . 'app/Query.php';


		// Conectamos con la base de datos
		DB::config('driver', self::get('database.driver'));	
		DB::config('user', self::get('database.user'));
		DB::config('password', self::get('database.password'));
		DB::config('dbname', self::get('database.dbname'));
		DB::connect();

		// Definimos algunas constantes importantes
		foreach (array( 'includes', 'models', 'controllers', 'views', 'assets')  as $path) {
			self::$config['path'][$path] = BASE_PATH . self::get('path.' . $path) . '/';
		}

	}
	public function get($key) {
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