<?php namespace EC;
class Event {
	public static $listeners = array();

	/**
	 * Bind an event
	 * @param string $event the event name, or the event identifier plus an event name
	 * <code>
	 *  Event::on('post.publish', function() {echo 'Foo';})
	 * </code>
	 *
	 * @param callable $callback the function to be executed when the event fires
	 * @param int $priority the priority of the executed callback
	 * @return void
	 */
	public static function on($event, $callback, $priority = 5) {
		if( strpos($event, '.') ) {
			list($name, $event) = explode('.', $event);
		} else {
			$name = 'globals';
		}

		if( ! isset(self::$listeners[$name]) ) {
			self::$listeners[$name] = array();
		}

		if( ! isset(self::$listeners[$name][$event]) ) {
			self::$listeners[$name][$event] = array();
		}
		self::$listeners[$name][$event][] = array(
			'callback' => $callback,
			'priority' => $priority
		);
	}

	/**
	 * Trigger an event, optionally passing an event object
	 * @param string $event the event name, or the event identifier plus an event name
	 * @param array $args the called function arguments
	 * @return void
	 */
	public static function trigger($event, $args = array()) {
		if( ! is_array($args) ) {
			$args = array($args);
		}
		if( strpos($event, '.') ) {
			list($name, $event) = explode('.', $event);
		} else {
			$name = 'globals';
		}


		if( ! isset(self::$listeners[$name]) || ! isset(self::$listeners[$name][$event]) ) {
			return;
		}

		usort(self::$listeners[$name][$event], function($cb1, $cb2) {
			return ($cb1['priority'] > $cb2['priority']) ? -1 : 1;
		});

		$args['event'] = $event;

		foreach (self::$listeners[$name][$event] as $callback) {
			call_user_func_array($callback['callback'], $args);
		}
	}
}