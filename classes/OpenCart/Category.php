<?php

namespace MageToOc\OpenCart;

class Category extends Base {

	public $language_id = 2;

	public $url_alias;

	public $data = array(
		'category_id'     => null,
		'image'           => '',
		'parent_id'       => 0,
		'top'             => 0,
		'column'          => 0,
		'sort_order'      => 0,
		'status'          => 1,
		'date_added'      => null,
		'date_modified'   => null
	);

	public $description = array(
		'category_id' => null,
		'name' => '',
		'meta_keyword' => '',
		'meta_description' => '',
		'meta_title' => '',
		'description' => ''
	);

	private $filters = array();

	public function set($group, $key, $value) {
		$this->{$group}[$key] = $value;

		return $this;
	}

	public function get($key) {
		return $this->data[$key];
	}

	public function addFilter($filter_id) {
		if ($filter_id && !in_array($filter_id, $this->filters)) {
			$this->filters[] = $filter_id;
		}
	}

	public function setLanguageId($language_id) {
		$this->language_id = (int)$language_id;
	}

	public function setUrlAlias($url_alias) {
		$this->url_alias = $url_alias;
	}

	public function generate($name = null) {
		$this->sql = '';

		// Category
		$implode = [];

		foreach ($this->data as $key => $value) {
			$implode[] = '`' . $key . '` = ' . $this->bind($value);
		}

		if ($implode) {
			$this->sql .= "INSERT INTO oc_category SET " . implode(', ', $implode) . ";\n";
		}

		if (!empty($this->data['category_id'])) {
			// Category to store
			$implode = [];
			$implode[] = 'category_id = ' . $this->bind($this->data['category_id']);
			$implode[] = 'store_id = 0';
			$this->sql .= "INSERT INTO oc_category_to_store SET " . implode(', ', $implode) . ";\n";

			// Category description
			$implode = [];

			$this->description['category_id'] = $this->data['category_id'];
			$this->description['language_id'] = $this->language_id;

			foreach ($this->description as $key => $value) {
				$implode[] = '`' . $key . '` = ' . $this->bind($value);
			}

			if ($implode) {
				$this->sql .= "INSERT INTO oc_category_description SET " . implode(', ', $implode) . ";\n";
			}
		}

		$this->saveSql($name);

		return $this->sql;
	}
}