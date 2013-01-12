<?php
class Response {
	public static function json($args, $callback = null, $echo = true) {
		$response = json_encode($args);

		if( $callback ) {
			header('Content-Type: text/javascript; charset=UTF-8');
			$response = $callback . '(' . $response . ')';
		} else {
			header('Content-Type: application/json; charset=UTF-8');
		}

		if( $echo ) {
			echo $response;
		} else {
			return $response;
		}
		
		return true;
	}

	public static function error($error_code) {
		Header::status($error_code);

		if( ! defined('PAGE_CONTROLLER') ) {
			define('PAGE_CONTROLLER', 'error');
			define('PAGE_ACTION', $error_code);
		}
		return View::make('error.' . $error_code);
	}
}