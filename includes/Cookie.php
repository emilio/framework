<?php
class Cookie {
	public static function set($name = '', $value, $days, $path = '/') {
		return setcookie($name, $value, time() + 24 * 60 * 60 * $days, $path );
	}
	public static function get($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

}