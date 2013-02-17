<?php
class Home_Controller {
	public static function action_index() {
		return View::make('home.index')
			// $nombre en la plantilla valdrá 'Emilio'
			->add_var('nombre', 'Emilio');
	}
	public static function action_dos() {
		return Redirect::to_route('home@tres');
	}
	public static function action_tres() {
		return "Blah!";
	}
	public static function action_json() {
		return Response::json(array(
			'status' => 'Not found'
		), 404);
	}
}