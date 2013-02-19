<?php namespace EC;

/**
 * Autoloader instance, PSR-0 compatible
 */
class Autoloader {

	public static $default_namespace = array(
		'path' => './',
		'separator' => '/',
		'extension' => '.php'
	);
	public static $aliases = array();
	public static $namespaces = array();

	/**
	 * Register a namespace
	 * @param string $namespace
	 * @param string $path the path (trailslashed)
	 * @param string $separator
	 * @param string $extension
	 * @return void
	 */
	public static function add_namespace($namespace, $path, $separator = DS, $extension = '.php') {
		self::$namespaces[$namespace] = array(
			'path' => $path,
			'separator' => $separator,
			'extension' => $extension
		);
	}

	/**
	 * Add an alias to a class
	 * @param string $alias the alias the class will have
	 * @param string $class the class it will represent
	 */
	public static function add_alias($alias, $class) {
		self::$aliases[$alias] = $class;
	}

	/**
	 * Set the path for non registered includes
	 */
	public static function defaultPath($path) {
		self::$default_namespace['path'] = $path;
	}

	/**
	 * Register the autoload function
	 * @return void
	 */
	public static function register() {
		spl_autoload_register(array(get_class(), 'autoload'));
	}

	/**
	 * Autoload function
	 */
	public static function autoload($class) {
		if( isset(self::$aliases[$class]) ) {
			return class_alias(self::$aliases[$class], $class);
		}
		if( $namespace_pos = strpos($class, '\\') ) {
			// Get the namespace
			$namespace = substr($class, 0, $namespace_pos);
			$class = ltrim(substr($class, $namespace_pos), '\\');
			if( ! isset(self::$namespaces[$namespace]) ) {
				self::$namespaces[$namespace] = array(
					'path' => self::$default_namespace['path'] . $namespace . '/',
					'separator' => self::$default_namespace['separator'],
					'extension' => self::$default_namespace['extension']
				);
			}
			$namespace = self::$namespaces[$namespace];
		} else {
			$namespace = self::$default_namespace;
		}
		require $namespace['path'] . str_replace('\\', $namespace['separator'], $class) . $namespace['extension'];
	}
}