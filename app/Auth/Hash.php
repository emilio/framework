<?php namespace EC\Auth;
if ( ! function_exists('openssl_random_pseudo_bytes')) {
	trigger_error('El hash requiere la extensión openssl')	;
}

class Hash {

	/**
	 * Hash a password using Bcrypt
	 */
	public static function make($value, $rounds = 8)
	{
		$work = str_pad($rounds, 2, '0', STR_PAD_LEFT);

		$salt = openssl_random_pseudo_bytes(16);

		$salt = substr(strtr(base64_encode($salt), '+', '.'), 0 , 22);

		return crypt($value, '$2a$'.$work.'$'.$salt);
	}

	/**
	 * Check the password.
	 */
	public static function check($value, $hash)
	{
		return crypt($value, $hash) === $hash;
	}

}