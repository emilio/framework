<?php 

Event::on('error.404', function() {
	Header::status(404);
}, 10);

Event::on('db.connect_error', function($e) {
	echo '<h1>No se pudo conectar con la base de datos</h1><pre>' . $e->getMessage() . '</pre>';
	die();
})