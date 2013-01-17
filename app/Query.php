<?php
	class Query {
		public $where_clauses = array();

		public function __construct($table, $id_field = 'id') {
			$this->table = $table;
			$this->id_field = $id_field;
		}

		public function and_where($where_field, $operator, $value) {
			$this->where_clauses[] = array('WHERE', $where_field, $operator, $value);
			return $this;
		}

		public function limit($start = 0, $end = null) {
			if ( ! $end ) {
				$end = $start;
				$start = 0;
			}
			$this->where_clauses[] = array('LIMIT', $start, $end);
			return $this;
		}

		public function order_by($field, $order = 'DESC') {
			$this->where_clauses[] = array('ORDER BY', $field, $order);
			return $this;
		}

		public function or_where($where_field, $operator, $value) {
			$this->where_clauses[] = array('OR', $where_field, $operator, $value);
			return $this;
		}

		public function get($fields = '*') {
			if( is_array($fields) ) {
				$fields = $this->table . '.`' . implode('`, ' . $this->table . '`', $fields) . '`';
			}
			return DB::select($this->table, $this->where_clauses, $fields);
		}

		public function count($field = '*') {
			return  $this->get('COUNT(' . ($field === '*' ? $field : '`' . $field . '`') . ')');
		}

		public function min($field) {
			return  $this->get('MIN(`' . $field . '`)');
		}

		public function max($field) {
			return  $this->get('MAX(`' . $field . '`)');
		}

		public function delete() {
			return DB::delete($this->table, $this->where_clauses);
		}

		public function first() {
			$results = $this->limit(1)->get();

			if( count($results) ) {
				return $results[0];
			} else {
				return null;
			}
		}

		public function set($args) {
			return DB::edit($this->table, $this->id_field, $args, $this->where_clauses);
		}
	}