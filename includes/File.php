<?php
	/*
	 * Simple file manager
	 * @author Emilio Cobos (http://emiliocobos.net) <ecoal95@gmail.com>
	 * @version 1.0
	 */
	class File {
		// the filesystem ubication of the file (eg: '/var/www/index.html')
		private $ubication;

		// handles storage for writing, appending, etc
		private $handles = array();

		/*
		 * The constructor
		 * @param string $ubication the ubication of the file
		 */
		public function __construct($ubication) {
			$this->ubication = $ubication;
			return $this;
		}

		/*
		 * Abbreviated constructor
		 * Allows you to use File::open('index.html'); instead of `new File('index.html');`
		 */
		public static function open($ubication) {
			return new static($ubication);
		}

		/*
		 * Write content into the file
		 * Note this will override the file, use append instead
		 * @param string $content the content to put in the file
		 */
		public function write($content) {
			if( isset($this->handles['write']) ) {
				$handle = $this->handles['write'];
			} else {
				$handle = $this->handles['write'] = fopen($this->ubication, 'w');
			}
			fwrite($handle, $content);
			return $this;
		}

		/*
		 * Append text to a file
		 * @param string $content the content to put in the file
		 */
		public function append($content) {
			if( isset($this->handles['append']) ) {
				$handle = $this->handles['append'];
			} else {
				$handle = $this->handles['append'] = fopen($this->ubication, 'a');
			}
			fwrite($handle, $content);
			return $this;
		}

		/*
		 * Get the file contents until a character (defaults to the whole file)
 		 * @param int $to the last char to be read
		 */
		public function read($to = 0) {
			if( isset($this->handles['read']) ) {
				$handle = $this->handles['read'];
			} else {
				$handle = $this->handles['read'] = fopen($this->ubication, 'r');
			}
			if( $to === 0 ) {
				$to = filesize($this->ubication);
			}

			return fread($handle, $to);
		}

		/*
		 * Close all the handlers
		 * Optional, but recommended after reading or modifying files
		 */
		public function close() {
			foreach ($this->handles as $handle) {
				fclose($handle);
			}
			return $this;
		}

		/*
		 * Check file existance
 		 * @param string $ubication the file to check
		 */
		public static function exists($ubication) {
			return file_exists($ubication);
		}
	}