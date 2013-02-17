<?php namespace EC\Storage;
/**
 * Simple cookie management
 *
 * @author Emilio Cobos (http://emiliocobos.net) <ecoal95@gmail.com>
 * @version 1.0
 */
class Cookie {

	/**
	 * Set a cookie
	 * 
	 * @access public
	 * @param string $name the name of the cookie
	 * @param string $value the value of the cookie
	 * @param int $days the days of the cookie expiration
	 * @param string $path
	 * @return bool if the setting succeded
	 */
	public static function set($name, $value, $days, $path = '/') {
		return setcookie($name, $value, time() + 24 * 60 * 60 * $days, $path );
	}

	/**
	 * Delete a cookie
	 *
	 * @param string $name the name of the cookie
	 * @return bool if the setting succeded
	 */
	public static function delete($name, $path = '/') {
		if( isset($_COOKIE[$name]) ) {
			unset($_COOKIE[$name]);
		}
		return setcookie($name, '', time() - 3600, $path);
	}

	/**
	 * Get the value of a cookie
	 *
	 * @param string $name the name of the cookie
	 * @return mixed the value of the cookie or null
	 */
	public static function get($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}
}