<?php
/*
 * Class for script/styles management
 */
class Asset {
	public static $enqueued = array(
		'head' => array(
			'js' => array(),
			'css' => array()
		),
		'footer' => array(
			'js' => array(),
			'css' => array()
		)
	);
	/*
	 * Dependencies not implemented yet
	 */
	public static function enqueue($src = null, $container = 'head', $dependencies = null, $is_script = true) {
		if( ! isset(static::$enqueued[$container]) ){
			static::$enqueued[$container] = array(
				'js' => array(),
				'css' => array()
			);
		}
		static::$enqueued[$container][$is_script ? 'js' : 'css'][] = (0 === strpos($src, 'http') || 0 === strpos($src, '//')) ? $src : Url::asset($src);
	}

	public static function enqueue_script($src, $container = 'head', $dependencies = null) {
		return static::enqueue($src, $container, $dependencies, true);
	}

	public static function enqueue_style($src, $container = 'head', $dependencies = null) {
		return static::enqueue($src, $container, $dependencies, false);
	}

	public static function print_scripts($container = 'head') {
		foreach (static::$enqueued[$container]['js'] as $src) {
			echo "<script src=\"$src\"></script>";
		}
	}

	public static function print_styles($container = 'head') {
		foreach (static::$enqueued[$container]['css'] as $url) {
			echo "<link rel=\"stylesheet\" href=\"$url\">";
		}
	}

	public static function print_all($container) {
		static::print_scripts($container);
		static::print_styles($container);
	}
}