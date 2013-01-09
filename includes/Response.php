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
	public static function error($error_code = 404, $echo = true) {
		$response = null;
		switch ($error_code) {
			case 404:
				header('Status: HTTP 1.1 404 Not Found');
				break;
			case 500:
				header('Status: HTTP 1.1 500 Internal Server Error');
				break;
		}
		if( ! defined('PAGE_CONTROLLER') ) {
			define('PAGE_CONTROLLER', 'error');
			define('PAGE_ACTION', $error_code);
		}
		return View::make('error.' . $error_code, $echo);
	}
}