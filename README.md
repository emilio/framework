# Framework PHP
Un pequeño framework que creé para desarrollar con facilidad del lado del cliente.

Falta por mejorar, pero creo que su interfaz de acceso a la base de datos es genial (intentando emular ActiveRecord).

Está inspirado en [Laravel](http://laravel.com), pero escrito completamente por [mí](http://emiliocobos.net).

## Características

### Modelo Vista Controlador
Símplemente añade tus controladores a la carpeta *controllers*. El controlador por defecto es `Home_Controller` (que cargará las páginas en el inicio). La estructura del controlador sería así:

```php
class Home_Controller {
	// La página principal
	public static function action_index() {
		// Mostrará el archivo que se encuentra en views/home/index.php
		// También incluirá header.php y footer.php si existen, siempre que no se especifice false como segundo argumento
		return View::make('home.index');
	}

	// Nos permitiría mostrar un artículo con una id (por ejemplo: /blog/123)
	public static function action_blog($id = null) {
		if( $id && is_numeric($id) ){
			// Article tiene que ser una clase definida en models (leer más abajo)
			if($articulo = Article::get($id) ) {
				// Cargará la plantilla ubicada en /views/home/post-single.php
				return View::make('home.post-single')->add_var('post', $articulo); // En la vista $post será lo mismo que $articulo en esta función
			} else {
				// Artículo no encontrado => cargará la plantilla views/error/404.php
				return Response::error(404);
			}
		} else {
			// Si no es numérico => 404
			return Response::error(404);
		}
		// La página del blog (/blog/)
		return View::make('home.blog-index');
	}

	// Imaginemos un formulario de contacto: Se accedería por /contacto/
	public static function action_contacto($args = null) {
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$headers = 'Mime-Type: 1.0' . "\r\n";
			/* ... */
			mail('destinatario@dominio.com', 'Nuevo mensaje desde el contacto', $mensaje, $headers);
		}

		// Seguirá el mismo procedimiento, pero cambiando index.php por contacto.php: Plantilla fácil! 
		return View::make('home.contacto');
	}
	// Ejemplo de una redirección fácil
	public static function action_redirect() {
		Redirect::to(Param::get('url'));// Sin estado
		Redirect::to(Param::get('url'), 301);// Hacer una redirección 301
	}
	/* etc */
}
```

Las vistas deben de ser alojadas en el directorio `views`.

### Genial interacción con la Base de datos
Proveo un meto muy similar a ActiveRecord de *RoR*

#### Crea el modelo
```php
// models/article.php
class Article extends DBObject {
	public static $table = 'mis_artículos';
	// opcional si el campo de tu id no es `id`
	public static $id_field = 'mi_id';
}
```

#### Úsalo
```php
// Obtener un objeto con los datos del post con id = 1
$article = Article::get(1);

// Ejecutar otro tipo de consulta sobre él (nótese que no usamos get sino find)
/*
 * Sería lo mismo que: 
 * `Article::delete(Article::get(1));`
 * sólo que sólo hace una consulta a la base de datos en vez de dos
 */
Article::find(1)->delete();

// Ejecutar una consulta compleja:
$articulos = Article::where('author_id', '=', 3)->and_where('article_title', 'LIKE', '%ejemplo%')->limit(0, 5)->get();
// Devuelve una array con varios StdClass
```
### Urls personalizadas
Por defecto se usará `PATH_INFO` para las urls, pero se pueden hacer aún mejores usando `mod_rewrite` (o aún más complejas usando `$_GET`). Para editarlo cambia la configuración en `config.php`

El constructor de las urls tendrá esto en cuenta, así que siempre es recomendable usarlo a la hora de obtener links.
```php
Url::get(); // Home
Url::current(); // Url actual (perfecto para link[rel="canonical"])
Url::get('admin@edit', 43); // Controlador admin, action_edit, parámetros 43: /admin/edit/43
Url::get('blog', 1); // Busca el controlador Blog_Controller. Como no existe, entiende que es home@blog: /blog/1/
Url::get('blog', 1, 'preview=true'); // Añadir una query string a la url: /blog/1/?preview=true
Url::asset('js/script.js'); // Busca la url absoluta al archivo assets/js/script.js
```
### Auto carga de clases
No te preocupes por qué clases se han incluido o no en el paquete. El framework se encargará de todo por tí. Símplemente asegúrate de llamar al archivo con el mismo nombre de la clase e incluirlo en `includes/`

### Detección de errores 404
Usamos `ReflectionClass` para comprobar que el número de argumentos esperados es el correcto.
Así conseguimos detectar errores 404.

### Manejo de archivos
La versión dos incorpora un interesante manejador de archivos que te permitirá manejar los estilos que usa tu aplicación fácilmente.

Por ejemplo:

```php
// controllers/home.php
class Home_Controller {

	/*
	 * La función global se ejecutará si existe para todas las páginas que dependan de `Home_Controller`
	 */
	public static function all() {
		// En todas queremos la hoja de estilos principal
		Asset::enqueue_style('css/style.css');
		// modernizr, por ejemplo
		Asset::enqueue_script('js/modernizr.js');

		// En el footer queremos jQuery
		Asset::enqueue_script('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js','footer');
	}

	// La página de inicio
	public static function action_index() {
		return View::make('home.index')
			->add_var('title', 'Inicio'); // add_var nos permite usar $title en la página con en valor 'Inicio'
	}

	// En el contacto queremos un plugin de validación
	public static function action_contacto() {
		Asset::enqueue_script( 'js/jQuery.validation.min.js' ,'footer');
		return View::make('home.contacto')
			->add_var('title', 'Contacto');
	}
}
```

Luego en `views/home/header.php` o en su fallback (views/header.php) deberíamos de tener algo así:

```html
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<?php Asset::print_styles('head'); ?>
	<?php Asset::print_scripts('head'); ?>
</head>
<body>
```

Y en `footer.php`:
```html
<?php Asset::print_scripts('footer'); ?>
</body>
</html>
```

## Empezar
1- Edita config.php para configurar la base de datos
2- Ya!, Creado la configuración, ya puedes escribir tus propios controladores... Prueba a editar controllers/home.php para crear tu página principal.

Hecho con orgullo por [Emilio Cobos](http://emiliocobos.net)
