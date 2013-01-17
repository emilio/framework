<?php
	/*
	 * Database object abstract class for retrieving easily data from a DB
	 * Usage:
	 *   - In another file (let's call it models.php):
	 *      class User extends DBObject {
	 *        public static $table = 'users';
	 *      }
	 *   - Anywhere:
	 *       $user = User::get(2); // get the user with id = 2
	 *
	 * @author Emilio Cobos (http://emiliocobos.net) <ecoal95@gmail.com>
	 * @version 1.0
	 */
	class DBObject {
		/*
		 * Required var in an extended class
		 * The table name
		 */
		public static $table = '';

		/*
		 * The id field, default
		 */
		public static $id_field = 'id';

		/*
		 * Get an query by id
		 * Abbreviaton of ::where('id', '=', $id);
		 * @param int $id the id to retrieve
		 * @return Query
		 */
		public static function find($id = 0)
		{
			return self::where(static::$id_field, '=', $id);
		}

		/*
		 * Get an objet by its id, or get all objects
		 * Abbreviaton of ::find($id)->first(); or ::all();
		 * @param int $id the id to retrieve
		 * @return StdClass
		 */
		public static function get($id = 0)
		{
			if( $id === 0 ) {
				return self::all();
			}

			return self::find($id)->first();
		}

		/*
		 * Get the total number of rows
		 * @return int
		 */
		public static function count($field = '*') {
			return DB::count(static::$table, $field);
		}

		/*
		 * Get the max value of something
		 * @return int
		 */
		public static function max($field) {
			return self::query()->max($field);
		}

		/*
		 * Get the min value of something
		 * @return int
		 */
		public static function min($field) {
			return self::query()->min($field);
		}

		/*
		 * Perform a query without where clauses
		 * Useful for limits and orders:
		 * eg: last 5 created users
		 * $users = User::query()->order_by('created_at', 'DESC')->limit(5)->get();
		 * @return int
		 */
		public static function query() {
			return new Query(static::$table, static::$id_field);
		}
		
		/*
		 * Select with a where query
		 * @param string $field the field to retrieve
		 * @param string $operator the comparison operator allowed in DB::$allowed_operators;
		 * @param mixed $value value to look for
		 * @return array
		 */
		public static function where($field = null, $operator = null, $value = null)
		{
			return self::query()->and_where($field, $operator, $value);
		}

		/*
		 * Select all data in that table
		 * @return array
		 */
		public static function all() {
			return DB::select(static::$table);
		}

		/*
		 * Create a row with the fields named in $args
		 * @param array|StdClass $args the fields and values to insert the database
		 */
		public static function create($args)
		{
			return DB::create(static::$table, $args);
		}

		private static function get_id($obj) {
			if( is_object($obj) ) {
				$obj = $obj->{static::$id_field};
			} else if ( is_array($obj) ) {
				$obj = $obj[static::$id_field];
			}
			return (int) $obj;
		}

		private function remove_id_from_obj($obj) {
			if( is_object($obj) ) {
				if( isset($obj->{static::$id_field}) ) {
					unset($obj->{static::$id_field});
				}
			} elseif( is_array($obj) ) {
				if( isset($obj[static::$id_field]) ) {
					unset($obj[static::$id_field]);
				}
			}
			return $obj;
		}
		/*
		 * Save a database object
		 * @param StdClass $obj object retrieved from the database before
		 */
		public static function save($obj) {
			$id = self::get_id($obj);
			
			return DB::edit(static::$table, static::$id_field, self::remove_id_from_obj($obj), array(
				array('WHERE', static::$id_field, '=', $id)
			));
		}
		/*
		 * Delete a database object
		 * @param mixed $obj object retrieved from the database before or id to delete
		 */
		public static function delete($obj) {
			return DB::delete(static::$table, array(
				array('WHERE', static::$id_field, '=', self::get_id($obj))
			));
		}
	}