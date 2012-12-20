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
			if( $controller ) {
				$url .= $controller . '/';
				if( $action ) {
					$url .= $action . '/';
				}
				if( $params ) {
					$url .= implode('/', $params) . '/';
				}
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
		return BASE_URL . 'assets/' . $path;
	}

	public function current() {
		return CURRENT_URL;
	}

}
/* CON ADMIN SEPARADO: 
<?php
class Url {
	public static function get($action = null, $id = null, $extra_query = null) {
		$url = BASE_URL;
		$admin_action = null;
		if( $action && ($admin_search = strpos($action, '@') )) {
			$admin_action = substr($action, $admin_search + 1);
			$action = 'admin';
		}

		if( $action === 'admin' ) {
			$url = ADMIN_URL;
			if( $admin_action ) {
				$url .= '?action=' . trim($admin_action);
			}
			if( $id ) {
				$url .= ((strpos($url, '?') !== false) ? '&' : '?') . 'poll=' . $id;
			}
			return $url;
		}

		if( PRETTY_URLS ) {
			if( ! REWRITE_FOR_URLS ) {
				$url .= 'index.php/';
			}
			if( $action ) {
				$url .= $action . '/';

				if( $id ) {
					$url .= $id . '/';
				}
			}
		} else {
			if( $action ) {
				$url .= '?action=' . $action;

				if( $id ) {
					$url .= '&poll=' . $id;
				}
			}
		}
		if( $extra_query ) {
			$url .= ((strpos($url, '?') !== false ) ? '&' : '?' ) . $extra_query;
		}
		return $url;
	}
}
*/