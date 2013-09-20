<?php namespace EC\HTTP;
class Curl {
	public static $curl_params = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
	);
	public static function get($url, $get_fields = null, $params = array()) {
		if( $get_fields ) {
			if( is_array($get_fields) ) {
				$get_fields = http_build_query($get_fields);
			}
			$url .= (strpos($url, '?') ? '&': '?') . $get_fields;
		}


		// array_merge jode las claves, así se mantienen
		$params = self::$curl_params + $params + array(
			CURLOPT_URL => $url,
		);
		$ch = curl_init();

		curl_setopt_array($ch, $params);

		$result = curl_exec($ch);


		curl_close($ch);
		return $result;
	}

	public static function post($url, $post_fields = '', $params = array()) {
		if( is_array($post_fields) ) {
			$post_fields = http_build_query($post_fields);
		}
		$params = self::$curl_params + $params + array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post_fields,
		);
		$ch = curl_init();
		

		curl_setopt_array($ch, $params);

		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
	}
}