<?php namespace EC\Storage;
use EC\Config;
class Log {
	/**
	 * Append to a log file a message with the date included
	 * @param string $filename the log file
	 * @param string $message the message to log
	 * @return bool
	 */
	public static function write_log($file, $message) {
		return @file_put_contents(Config::get('path.logs') . $file, '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	/**
	 * Log into the 'errors.log' file
	 * @param string $message
	 * @see Log::write_log()
	 * @return bool
	 */
	public static function error($message) {
		return self::write_log('errors.log', $message);
	}

	/**
	 * Log into the 'warnings.log' file
	 * @param string $message
	 * @see Log::write_log()
	 * @return bool
	 */
	public static function warn($message) {
		self::write_log('warnings.log', $message);
	}

	/**
	 * Log into the 'info.log' file
	 * @param string $message
	 * @see Log::write_log()
	 * @return bool
	 */
	public static function info($message) {
		self::write_log('info.log', $message);
	}

	/**
	 * Log into the 'main.log' file
	 * @param string $message
	 * @see Log::write_log()
	 * @return bool
	 */
	public static function log($message) {
		self::write_log('main.log', $message);
	}

	/**
	 * Allow to add a custom log
	 * @param string $key the log identifier
	 * @param string $message
	 * @see Log::write_log()
	 * @return bool
	 */
	public static function custom($key, $message) {
		return self::write_log($key . '.log', $message);
	}
}