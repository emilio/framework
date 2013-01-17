<?php
	/*
	 * Database class for data manipulation
	 * @author Emilio Cobos (http://emiliocobos.net) <ecoal95@gmail.com>
	 * @version 1.0
	 */
	class DB {
		// Conection drivers for the pdo
		// Configuration via DB::config
		private static $config = array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'dbname' => '',
			'user' => '',
			'password' => ''
		);

		// The pdo instance
		public static $db;

		// Allowed operators for where queries
		public static $allowed_operators = array('=', '!=', '>', '<', '<=', '>=', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN', 'LIKE', 'NOT LIKE'); // In allows searching for different values: Users::where('id', 'IN', '1,2,3,4');

		public static $allowed_relations = array('WHERE', 'OR', 'AND');
		public static $allowed_orders = array('DESC', 'ASC');

		/* Config the database before connection
		 * @param string $option the option to be configured
		 * @param string $value the value for the database
		 */
		public static function config($option, $value = '')
		{
			if( is_array($option) ) {
				static::$config = array_merge(static::$config, $option);
			} else {
				static::$config[$option] = $value;
			}
		}

		/*
		 * Connect to the database after configuration
		 */
		public static function connect()
		{
			$config = static::$config;
			self::$db = new PDO($config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['user'], $config['password']);
			self::$db->query("SET NAMES 'utf8'");
		}

		/*
		 * Create a row in a table
		 * @param string $table the table where the row is going to be inserted
		 * @param array $arguments the field=>value of the new row
		 */
		public static function create($table, $arguments)
		{
			$sql = 'INSERT INTO `' . $table . '` (';
			$fields = array();
			$values = array();
			foreach ($arguments as $field => $value) {
				$fields[] = $field;
				$values[':' . $field] = $value;
			}

			$sql .= '`' . implode('`, `', $fields) . '`) VALUES (';
			$sql .= ':' . implode(', :', $fields) . ')';
		
			$statement = self::$db->prepare($sql);
			try {
				$statement->execute($values);
				return self::$db->lastInsertId();
			} catch (PDOException $e) {
				return array(
					'done' => false,
					'error' => $e->getMessage()
				);
			}
		}

		/*
		 * Select rows from a table
		 * @param string $table
		 * @param mixed $where_field the field to be checked, or an array of fields ( see make_where_querie)
		 * @param string $operator an operator in self::$allowed_operators
		 * @param mixed $value the value to look for
		 */
		public static function select($table, $where_field = null, $columns = '*') {
			$fields = null;
			$has_where_query = ! is_null($where_field);
			$sql = "SELECT $columns FROM `$table`";
			$statement = null;

			if( $has_where_query ) {
				list($sql, $fields) = self::make_where_querie($where_field, $sql);
			}
			
			$statement = self::$db->prepare($sql);

			$statement->execute($fields);

			switch (substr($columns, 0, 4)) {
				case 'COUN':
				case 'MAX(':
				case 'MIN(':
					return intval($statement->fetchColumn(), 10);
				break;
				
				default:
					return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
			}
		}

		/*
		 * Permitir seleccionar de una tabla estática
		 */
		public static function from_table($table) {
			return new Query($table);
		}

		/*
		 * Make a where querie with an array of arguments. Eg:
		 * array(
		 *    array('WHERE', 'id', '=', 2),
		 *    array('AND', 'ip', '=', '173.26.0.1')
		 * )
		 */
		private static function make_where_querie($args = array(), $sql = ''){
			$count = 0;
			$prepared_fields = 0;
			$base_placeholder = 'prepared_field_';
			$values = array();
			$limit = null;
			$orderby = null;



			$relation = $operator = $field = $value = null;

			foreach ($args as $where_querie) {
				switch (count($where_querie)) {
					// Limit
					case 2:
						$limit = array(0, $where_querie[1]);
						continue 2; // Continuamos el foreach
						break;

					// Tiene operador definido
					case 4:
						$relation = strtoupper(array_shift($where_querie));
						if( ! in_array($relation, self::$allowed_relations) ) {
							throw new Exception("Error al procesar la solicitud", 1);							
						}
						if( $relation === 'WHERE' ) {
							if( $count === 0 ) {
								$count++;
							} else {
								$relation = 'AND';
							}
						}
						break;
					// LIMIT, ORDER BY, OR
					case 3:
						$first_arg = strtoupper($where_querie[0]);
						if( $first_arg === 'LIMIT' ) {
							$limit = array($where_querie[1], $where_querie[2]);
							continue 2;
						} else if ($first_arg === 'ORDER BY'){
							$orderby = array($where_querie[1], strtoupper($where_querie[2]));
							continue 2;
						}
						if( $count === 0 ) {
							$relation = 'WHERE';
							$count++;
						} else {
							$relation = 'OR';
						}
						break;
					default:
						throw new Exception("Error al procesar la solicitud", 1);							
				}

				list($field, $operator, $value) = $where_querie;
				$operator = strtoupper($operator);
				if( ! in_array($operator, self::$allowed_operators) ) {
					throw new Exception("Error al procesar la solicitud", 1);
				}
				if( $operator === 'IN' || $operator === 'NOT IN' ) {
					if( ! is_array($value) ) {
						$value = explode(',', $value);
					}

					$sql .= " $relation `$field` $operator(";
					$in_fields_count = 0;
					foreach ($value as $in_value) {
						$field_placeholder = ':' . $base_placeholder . $prepared_fields++;
						if( $in_fields_count !== 0 ) {
							$sql .= ',';
						}
						$sql.= $field_placeholder;
						$values[$field_placeholder] = $in_value;
						$prepared_fields++;
						$in_fields_count ++;
					}
					$sql .= ')';
				} elseif ($operator === 'BETWEEN' || $operator === 'NOT BETWEEN'){
					$sql .= "$relation `$field` $operator :$base_placeholder" . $prepared_fields . " AND :$base_placeholder" . ($prepared_fields + 1);
					$values[':' . $base_placeholder . $prepared_fields++] = $value[0];
					$values[':' . $base_placeholder . $prepared_fields++] = $value[1];
				} else {
					$sql .= " $relation `$field` $operator :$base_placeholder" . $prepared_fields;
					$values[':' . $base_placeholder . $prepared_fields++] = $value;
				}
			}
			if( $orderby ) {
				if( in_array($orderby[1], self::$allowed_orders) ) {
					$sql .= " ORDER BY `$orderby[0]` $orderby[1]";
				}
			}
			if( $limit ) {
				$sql .= sprintf(" LIMIT %d, %d", $limit[0], $limit[1]);
			}

			return array($sql, $values);
		}
		/*
		 * Edits columns from a table
		 * @param string $table
		 * @param string $id_field the object to be edited
		 * @param array $data the data to insert. By default values are prepared using PDO, but you can override this behavior starting the column name as 'nofilter:'
		 * Eg: for increase a count value you'll try:
		 * User::find(1)->set(array(
		 * 	'age' => 'age + 1'
		 * ));
		 * But it wont work. Insted you must use:
		 * User::find(1)->set(array(
		 * 	'nofilter:age' => '`age` + 1'
		 * ));
		 *
		 * This is better than:
		 * 
		 * $user = User::get(1);
		 * $user->age = $user->age + 1;
		 * User::save($user);
		 * 
		 * Because you've used 1 query instead of 2
		 */
		public static function edit($table, $id_field, $data, $where_clauses) {
			$sql = "UPDATE `$table` SET ";
			$params = array();

			$i = 0;

			// Usando foreach nos permite pasar un objeto y una array
			foreach($data as $key => $val) {
				if( $key !== $id_field && ! is_int($key)) {
					if( $i !== 0 ) {
						$sql .= ', ';
					} else {
						$i = 1;
					}
					
					if( 0 === strpos( $key, 'nofilter:') ) {
						$sql .= '`' . str_replace('nofilter:', '', $key) . '` = ' . $val;
					} else {
						$sql .= '`' . $key . '`=:set_' . $key;
						$params[':set_' . $key] = $val;
					}
				}
			}

			list($sql, $values) = self::make_where_querie($where_clauses, $sql);

			$statement = self::$db->prepare($sql);

			// Unir los valores de la consulta set a los de la consulta where
			$params = array_merge($params, $values);


			$statement->execute($params);

			return $statement->rowCount();
		}


		/*
		 * Delete a single row from a file
		 * @param string $table
		 * @param int $id id of the row
		 */
		public static function delete($table, $where_clauses)
		{
			$sql = "DELETE FROM `$table`";

			list($sql, $fields) = self::make_where_querie($where_clauses, $sql);

			$statement = self::$db->prepare($sql);

			return $statement->execute($fields);
		}

		/*
		 * Count the items in the DB
		 */
		public static function count($table, $field) {
			$rows = self::$db->query("SELECT COUNT($field) FROM `$table`")->fetchColumn();
			return intval($rows, 10);
		}
	}