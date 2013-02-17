<?php namespace EC\Database;
	class Query {
		public $where_clauses = array();

		/**
		 * The constructor
		 *
		 * @param string $table
		 * @param string $id_field
		 */
		public function __construct($table, $id_field = 'id') {
			$this->table = $table;
			$this->id_field = $id_field;
		}

		/**
		 * Adds a where clause to the query
		 *
		 * @access public
		 * @param string $where_field
		 * @param string $operator
		 * @param mixed $value
		 * @return Query the same instance
		 */
		public function and_where($where_field, $operator, $value) {
			$this->where_clauses[] = array('WHERE', $where_field, $operator, $value);
			return $this;
		}

		/**
		 * Adds a limit clause to the query
		 *
		 * @access public
		 * @param int $start (becomes the end if $end not especified)
		 * @param int $end
		 * @return Query the same instance
		 */
		public function limit($start, $end = null) {
			if ( ! $end ) {
				$end = $start;
				$start = 0;
			}
			$this->where_clauses[] = array('LIMIT', $start, $end);
			return $this;
		}

		/**
		 * Adds a limit clause to the query
		 *
		 * @access public
		 * @param string $field
		 * @param string $order
		 * @return Query the same instance
		 */
		public function order_by($field, $order = 'DESC') {
			$this->where_clauses[] = array('ORDER BY', $field, $order);
			return $this;
		}

		/**
		 * Adds a or where clause to the query
		 *
		 * @access public
		 * @param string $where_field
		 * @param string $operator
		 * @param mixed $value
		 * @return Query the same instance
		 */
		public function or_where($where_field, $operator, $value) {
			$this->where_clauses[] = array('OR', $where_field, $operator, $value);
			return $this;
		}

		/**
		 * Execute the query
		 * 
		 * @param array|string $fields the fields to get
		 * @return array the query results
		 */
		public function get($fields = '*') {
			if( is_array($fields) ) {
				$fields = $this->table . '.`' . implode('`, ' . $this->table . '`', $fields) . '`';
			}
			return DB::select($this->table, $this->where_clauses, $fields);
		}

		/**
		 * Execute a count query
		 *
		 * @param string $field the field to count
		 * @return int
		 */
		public function count($field = '*') {
			return  $this->get('COUNT(' . ($field === '*' ? $field : '`' . $field . '`') . ')');
		}

		/**
		 * Execute a min query
		 *
		 * @param string $field
		 * @return int
		 */
		public function min($field) {
			return  $this->get('MIN(`' . $field . '`)');
		}

		/**
		 * Execute a max query
		 *
		 * @param string $field
		 * @return int
		 */
		public function max($field) {
			return  $this->get('MAX(`' . $field . '`)');
		}

		/**
		 * Execute a delete query: Delete items from the db
		 *
		 * @see DB::delete()
		 */
		public function delete() {
			return DB::delete($this->table, $this->where_clauses);
		}

		/**
		 * Get the first element of the results
		 *
		 * @return stdClass|null
		 */
		public function first() {
			$results = $this->limit(1)->get();

			if( count($results) ) {
				return $results[0];
			} else {
				return null;
			}
		}

		/**
		 * Edit data
		 *
		 * @see DB::edit() 
		 */
		public function set($args) {
			return DB::edit($this->table, $this->id_field, $args, $this->where_clauses);
		}
	}