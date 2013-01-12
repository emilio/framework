<?php
class Home_Controller {
	public static function action_index() {
		return View::make('home.index')
			// $nombre en la plantilla valdrá 'Emilio'
			->add_var('nombre', 'Emilio');
	}
}