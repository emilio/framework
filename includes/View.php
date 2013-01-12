<?php
	class View {
		public $data = array();
		public $include_other_files = true;

		function __construct($param, $other_files) {
			if( strpos($param, '.') ) {
				list($controller, $action) = explode('.', $param);
			} else {
				if( file_exists(Config::get('path.controllers') . $param . '.php') ) {
					$controller = $param;
					$action = 'index';
				} else {
					$controller = 'home';
					$action = $param;
				}
			}

			$this->controller = $controller;
			$this->action = $action;
		}

		public function add_var($key, $val = null) {
			if( is_array($key) ) {
				$this->data = array_merge($this->data, $key);
			} else {
				$this->data[$key] = $val;
			}
			return $this;
		}

		public function without_other_files() {
			$this->include_other_files = false;
		}

		public function render($echo = false) {
			$template_path = Config::get('path.views');
			$controller_path = $header_file = $main_file = $footer_file = null;
			$controller = $this->controller;
			$action = $this->action;


			$controller_path = $template_path . $controller . '/';

			if( $this->include_other_files ) {
				if( ! file_exists($header_file = $controller_path . $action . '.header.php') ) {
					if( ! file_exists($header_file = $controller_path . 'header.php') ) {
						$header_file = $template_path . 'header.php';
					}
				}

				if( ! file_exists($footer_file = $controller_path . $action . '.footer.php') ) {
					if( ! file_exists($footer_file = $controller_path . 'footer.php') ) {
						$footer_file = $template_path . 'footer.php';
					}
				}

			}
			if( ! file_exists($main_file = $controller_path . $action . '.php') ) {
				if( DEVELOPEMENT_MODE || $controller === 'error') {
					throw new Exception("Vista no encontrada: " . $main_file, 1);
				} else {
					return Response::error(500);
				}
			}

			extract($this->data);

			ob_start();
				if( $header_file ) {
					include $header_file;
				}
				include $main_file;
				if( $footer_file ) {
					include $footer_file;
				}
			$view = ob_get_clean();

			if( $echo ) {
				echo $view;
				return true;
			}
			return $view;
		}
		public static function make($param, $other_files = true) {
			return new static($param, $other_files);
		}
	}