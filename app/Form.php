<?php namespace EC;
use EC\HTTP\Param;

/**
 * Clase para manejar formularios
 * @author Emilio Cobos
 * @package EC
 */
class Form {
	/**
	 * Opciones por defecto para mostrar el formulario
	 * Variables permitidas: :help_text, :label_text, :id, :required_attr
	 */
	public static $default_parse_options = array(
		'before_input' => '<p class="field-:id">',
		'after_input' => '</p>',
		'required_attr' => 'data-required',
		'label_template' => '<label for=":id" :required_attr>:label_text</label>',
		'help_template' => '<span class="help-text">:help_text</span>'
	);

	/**
	 * Mensajes de error por defecto
	 * Variables permitidas: :label_text, :id
	 */
	public static $default_error_messages = array(
		'required' => 'El campo :id es obligatorio',
		'email' => 'Introduce un email válido',
		'url' => 'Introduce una url válida',
		'number' => 'El campo :id tiene que ser numérico',
		'pattern' => 'El campo :id tiene que tener el formato adecuado.',
		'verification' => 'El campo :id tiene que ser igual a su verificación.',
		'time' => 'El campo :id tiene que ser un tiempo correcto.',
		'datetime' => 'El campo :id tiene que ser una fecha correcta como 1990-12-1',

	);
	/**
	 * Crear un formulario a partir de una array con campos
	 * @param array $form array asociativa tal que así:
	 * <code>
	 *   $form = array(
	 *     'title' => array(
	 *        'tag' => 'input', // opcional (input es por defecto)
	 *        'attrs' => array(
	 *           'type' => 'text',
	 *           'placeholder' => 'Hey!',
	 *           'data-id' => 'cualquier-cosa',
	 *           'required' => true,
	 *        ),
	 *     ),
	 *   );
	 * </code>
	 * @param array $data_src the data for filling the form if avaliable (ej: $_POST)
	 * @param array $options unas opciones para modificar la salida (ver $default_parse_options)
	 */
	public static function parse($form, $data_src = null, $options = array()) {
		$data_src = (array) $data_src;
		$options = array_merge(self::$default_parse_options, $options);

		$searches = array(
			':id',
			':label_text',
			':help_text',
			':required_attr'
		);
		$output = '';
		foreach ($form as $id => $input) {
			/*
			 * atributos iniciales
			 */
			if( ! isset($input['tag']) ) {
				$input['tag'] = 'input';
			}
			if( ! isset($input['attrs']) ) {
				$input['attrs'] = array();
			}
			$input['attrs']['id'] = $input['attrs']['name'] = $id;
			if( $input['tag'] === 'input' ) {
				if( ! isset($input['attrs']['type']) ) {
					$input['attrs']['type'] = 'text';
				}

				if( ! isset($input['attrs']['value']) && ! in_array($input['attrs']['type'], array('password', 'checkbox', 'radio')) ) {
					$input['attrs']['value'] = @$data_src[$id];
				}
			}


			$replaces = array(
				$id,
				isset($input['label_text']) ? $input['label_text'] : '',
				isset($input['help_text']) ? $input['help_text'] : '',
				isset($input['attrs']['required']) ? $options['required_attr'] : ''
			);
			$label = '';
			$label_before = false;


			$output .= str_replace($searches, $replaces, $options['before_input']); 
			if( isset($input['label_text']) ) {
				$label = ' ' . str_replace($searches, $replaces, $options['label_template']);
			}

			if( isset($input['help_text']) ) {
				$label .= ' ' . str_replace($searches, $replaces, $options['help_template']);
			}

			if( isset($input['label_text']) && ($input['tag'] !== 'input' || ($input['attrs']['type'] !== 'checkbox' || $input['attrs']['type'] !== 'radio')) ) {
				$label_before = true;
				$output .= $label;
			}

			$output .= ' <' . $input['tag'];

			foreach ($input['attrs'] as $attr => $value) {
				$output .= ' ' . $attr . '="' . ($value === true ? '' : htmlspecialchars($value) ) . '"';
			}
			$output .= '>';

			if( isset($input['options']) ) {
				$current_val = @$data_src[$id];
				foreach ($input['options'] as $val => $text) {
					$output .= '<option value="' . htmlspecialchars($val) . '"' . ($current_val === $val ? ' selected' : '') . '>' . htmlspecialchars($text) . '</option>';
				}
			} elseif( $input['tag'] === 'textarea' ) {
				$output .= @$data_src[$id];
			}

			if( $input['tag'] !== 'input' ) {
				$output .= '</' . $input['tag'] . '>';
			}

			if( ! $label_before && isset($input['label_text']) ) {
				$output .= $label;
			}

			$output .= $options['after_input']; 
		}
		return $output;
	}

	/**
	 * Validar un formulario dados la estructura y los datos iniciales
	 * @see Form::parse()
	 * @param array $form ver arriba
	 * @param array $data los datos incluidos (ej: $_POST)
	 * @param array $error_messages una array conteniendo los mensajes de error por tipo
	 */
	public static function validate($form, $data, $error_messages = array()) {
		$return = array();
		$errors = array();
		$error_messages = array_merge(self::$default_error_messages, $error_messages);
		$searches = array(
			':id',
			':label_text',
		);
		foreach ($form as $id => $input) {
			$value = isset($data[$id]) ? $data[$id] : null;
			$is_empty = $value === null || $value === '';
			/**
			 * Atributos iniciales
			 */
			if( ! isset($input['tag']) ) {
				$input['tag'] = 'input';
			}
			if( ! isset($input['attrs']) ) {
				$input['attrs'] = array();
			}
			$input['attrs']['id'] = $input['attrs']['name'] = $id;
			if( $input['tag'] === 'input' ) {
				if( ! isset($input['attrs']['type']) ) {
					$input['attrs']['type'] = 'text';
				}
			}
			
			if( isset($input['attrs']['required']) && $is_empty ) {
				$errors[$id] = 'required';
				continue;
			}

			if( $input['tag'] === 'input' && ! $is_empty) {
				switch ($input['attrs']['type']) {
					case 'url':
						if( ! filter_var($value, FILTER_VALIDATE_URL) ) {
							$errors[$id] = 'url';
							continue 2;
						}
						break;
					case 'email':
						if( ! filter_var($value, FILTER_VALIDATE_EMAIL) ) {
							$errors[$id] = 'email';
							continue 2;
						}
						break;
					case 'number':
						if( ! is_numeric($value) ) {
							$errors[$id] = 'number';
							continue 2;
						}
						break;
					case 'time':
						if( ! preg_match('/^[0-9]{2}:[0-9]{2}(:[0-9]{2})?$/', $value) ) {
							$errors[$id] = 'time';
							continue 2;
						}
						break;
					case 'datetime':
						if(date('Y-m-d H:i:s', strtotime($value)) !== $value) {
							$errors[$id] = 'datetime';
							continue 2;
						}
						break;
					case 'tel': // Todo
					case 'text':
					default:
						break;
				}
			}

			if( isset($input['attrs']['pattern']) && ! $is_empty ) {
				if( ! preg_match('/^' . $input['attrs']['pattern'] . '$/', $value) ) {
					$errors[$id] = 'pattern';
					break;
				}
			}
		}
		foreach ($errors as $id => $type) {
			$replaces = array(
				$id,
				isset($data[$id]['label_text']) ? $data[$id]['label_text'] : '',
			);
			$return[] = str_replace($searches, $replaces, $error_messages[$type]);
		}
		return $return;
	}
}