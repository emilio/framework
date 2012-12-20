<?php
	return array(
		/*
		 * Configuración requerida
		 */
		'database' => array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'root',
			'dbname' => 'polls'
		),

		/*
		 * Usar la urls bonitas viene activado por defecto
		 * Rewrite es para que la aplicación genere las urls sin el index.php/
		 * En caso de que lo uses, deberás usar en el .htacces algo así:
		 <IfModule mod_rewrite.c>
		 	RewriteCond {...}
		 </IfModule>
		 */
		'url' => array(
			'pretty' => true,
			'rewrite' => false
		),

		'path' => array(
			'includes' => 'includes',
			'models' => 'models',
			'controllers' => 'controllers',
			'views' => 'views',
			'assets' => 'assets'
		),

		/*
		 * Configuración extra aquí
		 */
		'admin' => array(
			'user' => 'ecoal95',
			'password' => '$2a$08$NVekmN7OH2ZO4HQJHuoY0eJ.SFh6NKhhOcUFd8s.C2StgKqO6KdPa'
		)
	);