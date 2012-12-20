<?php
	class View {
		public function make($param, $other_files = true,$echo = true) {
			$template_path = Config::get('path.views');
			$controller_path = $header_file = $main_file = $footer_file = null;
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
			$controller_path = $template_path . $controller . '/';
			if( $other_files ) {
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
				$error = 'Include no encontrado: ' . $main_file;
				return Response::error(500);
			}


			ob_start();
				include $header_file;
				include $main_file;
				include $footer_file;
			$view = ob_get_clean();
			if( $echo ) {
				echo $view;
				return true;
			}
			return $view;
		}
	}