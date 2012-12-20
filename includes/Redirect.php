<?php
class Redirect {
	public static function to($location, $status = null) {
		if( is_int($status) ) {
			switch ($status) {
				case 301:
					header("HTTP/1.1 301 Moved Permanently");
					break;
				case 302:
					header("HTTP/1.1 302 Moved Temporary");
					break;
			}
		}
		header('Location: ' . $location);
		exit;
	}
}