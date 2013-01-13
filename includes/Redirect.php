<?php
class Redirect {
	public static function to($location, $status = null) {
		if( is_int($status) ) {
			Header::status($status);
		}
		header('Location: ' . $location);
		exit;
	}
}