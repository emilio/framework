<?php namespace EC;
use EC\Database\DB, EC\Storage\Cache;
class Config {
	protected static $config;
	public static function init() {
		if( ! self::$config ) {
			self::$config = (require BASE_PATH . 'config.php');
		}
		
		// Hallamos las rutas absolutas
		foreach (array( 'cache','includes', 'models', 'controllers', 'views', 'assets')  as $path) {
			self::$config['path'][$path . '_orig'] = self::$config['path'][$path];
			self::$config['path'][$path] = BASE_PATH . self::$config['path'][$path] . '/';
		}

		// Configuramos la caché
		Cache::configure(array(
			'cache_path' => self::get('path.cache'),
			'expires' => self::get('cache.expires')
		));
	}
	public static function get($key) {
		if( false !== strpos($key, '.') ) {
			list( $first, $second ) = explode('.', $key);
			return self::$config[$first][$second];
		}

		return self::$config[$key];
	}
}