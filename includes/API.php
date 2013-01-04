<?php
class Api {
	public static $defaults = array(
		'por_pagina' => 5,
		'page' => 1,
		'dateorder' => 'ASC',
		'published_max' => null,
		'published_min' => null,
		'barrios' => null,
		'search' => null,
		'phone' => null,
		'ref' => null,
		'show_hidden' => false
	);
	public static function get_hidden_offers() {
		$return = array();
		$user = $_SESSION['user'];

		$ocultos = Anuncios_Ocultos::where('usuario_id', '=', $user->id)->get();
		foreach ($ocultos as $oculto) {
			$return[] = $oculto->oferta_id;
		}
		return $return;
	}
	public static function get_results($options) {
		$return = array();
		$query = Oferta::query();
		$options = array_merge(self::$defaults, $options);

		extract($options);

		$dateorder = strtoupper($dateorder);

		// Orden por fechas
		if( ! ( $dateorder === 'ASC' || $dateorder === 'DESC' )) {
			return null;
		}

		$query->order_by('fecha_publicacion', $dateorder);

		// Mostrar sólo anuncios de particulares
		$query->and_where('anunciante', 'LIKE', '%particular%');

		$hidden = self::get_hidden_offers();

		if( $show_hidden ) {
			if( count($hidden) ) {
				$query->and_where('id', 'IN', $hidden);
			} else {
				$query->and_where('1', '!=', '1');
			}
		} else {
			if( count($hidden) ) {
				$query->and_where('id', 'NOT IN', $hidden);
			}
		}

		if( $ref ) {
			$query->and_where('id', '=', $ref)->limit(1);
			$return['results_count'] = min(1, $query->count());
		} else {
			// Barrios
			if( ! $show_hidden ) {
				if( $barrios ) {
					if( ! is_array($barrios) ) {
						$barrios = array_filter(explode(',', $barrios), 'is_numeric');
					}
					if( count( $barrios ) ) {
						$query->and_where('barrio_id', 'in', $barrios);
					} else {
						// Forma tonta de decir: ningún resultado
						$query->and_where('1', '!=', '1');
					}
				} else {
					// Forma tonta de decir: ningún resultado
					$query->and_where('1', '!=', '1');
				}

				// Búsqueda
				if( $search ) {
					$query->and_where('descripcion', 'like', '%' . $search . '%');
				}

				// Fechas de publicación máximas y mínimas
				if( $published_max ) {
					$query->and_where('fecha_publicacion', '<=', date('Y-m-d', strtotime($published_max)));
				}

				if( $published_min ) {
					$query->and_where('fecha_publicacion', '>=', date('Y-m-d', strtotime($published_min)));
				}
				
				// Búsqueda por telefono
				if( $phone ) {
					$query->and_where('telefono', 'like', '%' . $phone . '%');
				}
			}


			// La cuenta total de resultados ( sin límite )
			$return['results_count'] = $query->count();

			if( $por_pagina !== 'all' ) {
				// La paginación lo último para permitirnos contarlas
				$offset = $por_pagina * $page - $por_pagina;
				$query->limit($offset, $por_pagina);
			}
		}
		
		// Obtener los resultados
		$return['results'] = $query->get();
		

		$return['status'] = 200;

		// Obtener también el barrio
		foreach($return['results'] as $result) {
			$result->barrio = Barrio::get($result->barrio_id)->barrio;
		}

		return $return;
	}
}