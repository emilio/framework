<?php 

Event::on('error.404', function() {
	Header::status(404);
}, 10);