<?php
namespace EC\HTTP;
/*
 * Redirect to a location or route
 */
class Redirect {
	/**
	 * Redirect to a raw url
	 * @param string $location the url the user will follow
	 * @param string $status the HTTP status
	 * @return void
	 */
	public static function to($location, $status = 302) {
		Header::location($location, $status);
		exit;
	}

	/*
	 * Redirects to a route url
	 * @param int $status the HTTP response code if the last param is an integer
	 */
	public static function to_route() {
		$args = func_get_args();
		$status = 302;
		if( is_int(end($args)) ) {
			$status = array_pop($args);
		}
		$url = call_user_func_array(array('EC\\HTTP\\Url', 'get'), $args);
		return Redirect::to($url, $status);
	}
}