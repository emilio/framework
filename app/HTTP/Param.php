<?php namespace EC\HTTP;
/**
 * Http parameters helper class
 */
class Param {
	/**
	 * Get a GET request parameter
	 * @param string $key
	 * @return string the corresponding value in $_GET or null
	 */
	public static function get($key) {
		return isset($_GET[$key]) ? $_GET[$key] : null;
	}
	/**
	 * Get a POST request parameter
	 * @param string $key
	 * @return mixed the corresponding value in $_POST or null
	 */
	public static function post($key) {
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}

	/**
	 * Get a POST request parameter
	 * @param string $key
	 * @return mixed the corresponding value in $_REQUEST or null
	 */
	public static function request($key) {
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
	}
}