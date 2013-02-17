<?php namespace EC\HTTP;
use EC\Config, EC\App;
/**
 * Url managing class
 */
class Url {
	/**
	 * Get an url based on a controller, some params and an extra query
	 * @param string $controller a controller-action like this: user@login
	 * @param string|array $params the appended parameters to the url
	 * @param string|array $extra_query the query string (built or in array format)
	 * @return string the resulting url
	 */
	public static function get($controller = null, $params = null, $extra_query = null) {
		$url = BASE_URL;

		$action = null;

		if( $params && ! is_array($params) ){
			$params = array($params);
		}

		if( $controller && strpos($controller, '@') ) {
			list($controller, $action) = explode('@', $controller);
		}

		if( Config::get('url.pretty') ) {
			if( ! Config::get('url.rewrite')) {
				$url .= 'index.php/';
			}
			if( $controller && $controller !== 'home') {
				$url .= $controller . '/';
			}
			if( $action && $action !== 'index') {
				$url .= $action . '/';
			}
			if( $params ) {
				$url .= implode('/', $params) . '/';
			}
		} else {
			if( $controller ) {
				$url .= '?c=' . $controller;
				if( $action ) {
					$url .= '&action=' . $action;
				}
				if( $params ) {
					$url .= '&params=' . implode(';', $params);
				}
			}
		}
		if( $extra_query ) {
			$url .= ((strpos($url, '?') !== false ) ? '&' : '?' ) . is_array($extra_query) ? http_build_query($extra_query) : $extra_query;
		}
		return $url;
	}

	/**
	 * Get an url to an asset
	 * @param string $path the folder/filename to the asset
	 * @see EC\Asset
	 */
	public static function asset($path = '') {
		return BASE_URL . Config::get('path.assets_orig') . '/' . $path;
	}

	/**
	 * Current base url without query string
	 * Built with Url::get(PAGE_CONTROLLER . '@' . PAGE_ACTION, $params);
	 * @return string the current url 
	 */
	public static function current() {
		return App::url();
	}
}