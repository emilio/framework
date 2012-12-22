<?php
class Url {
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
			$url .= ((strpos($url, '?') !== false ) ? '&' : '?' ) . $extra_query;
		}
		return $url;
	}
	public function asset($path = '') {
		return BASE_URL . Config::get('path.assets_orig') . '/' . $path;
	}

	public function current() {
		return CURRENT_URL;
	}
}