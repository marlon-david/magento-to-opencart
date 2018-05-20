<?php

namespace MageToOc\OpenCart;

class Product extends Base {

	public $language_id = 2;

	public $url_alias;

	public $data = array(
		'product_id' => null,
		'model' => '',
		'sku' => '',
		'upc' => '',
		'ean' => '',
		'jan' => '',
		'isbn' => '',
		'mpn' => '',
		'location' => '',
		'quantity' => 0,
		'minimum' => 1,
		'subtract' => 1,
		'stock_status_id' => 5,
		'date_available' => '',
		'manufacturer_id' => 0,
		'shipping' => 1,
		'price' => 0,
		'points' => 0,
		'weight' => 0,
		'weight_class_id' => 1,
		'length' => 0,
		'width' => 0,
		'height' => 0,
		'length_class_id' => 1,
		'status' => 1,
		'tax_class_id' => 0,
		'sort_order' => 0,
		'image' => '',
		'date_added' => null,
		'date_modified' => null
	);

	public $description = array(
		'product_id' => null,
		'name' => '',
		'meta_keyword' => '',
		'meta_description' => '',
		'meta_title' => '',
		'description' => '',
		'tag' => ''
	);

	public $images = array();

	public $categories = array();

	private $options = array();

	private $filters = array();

	public function set($group, $key, $value) {
		$this->{$group}[$key] = $value;

		return $this;
	}

	public function get($key) {
		return $this->data[$key];
	}

	public function addImage($image) {
		if (empty($this->data['image'])) {
			$this->data['image'] = 'catalog/' . ltrim($image, '/');
		} else {
			$this->images[] = 'catalog/' . ltrim($image, '/');
		}
	}

	public function setImages($images, $separator = ';') {
		$images = explode($separator, $images);

		foreach ($images as $image) {
			if (!empty($image)) {
				$this->addImage($image);
			}
		}
	}

	public function addCategory($category) {
		$this->categories[] = $category;
	}

	public function addOption(array $option) {
		$this->options[$option['option_id']] = array(
			'option_id'            => $option['option_id'],
			'type'                 => $option['type'],
			'required'             => 1,
			'product_option_value' => array(),
			'option_value'         => ''
		);
	}

	public function addOptionValue($option_id, $option_value_id, $quantity = null, $weight = 0, $weight_prefix = '+') {
		if (isset($this->options[$option_id])) {
			$this->options[$option_id]['product_option_value'][$option_value_id] = array(
				'option_value_id' => $option_value_id,
				'quantity'        => (int)$quantity,
				'subtract'        => (is_null($quantity)) ? '0' : '1',
				'price'           => '',
				'price_prefix'    => '+',
				'points'          => '',
				'points_prefix'   => '+',
				'weight'          => $weight,
				'weight_prefix'   => $weight_prefix
			);
		}
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

		// Product
		$implode = [];

		foreach ($this->data as $key => $value) {
			$implode[] = '`' . $key . '` = ' . $this->bind($value);
		}

		if ($implode) {
			$this->sql .= "INSERT INTO oc_product SET " . implode(', ', $implode) . ";\n";
		}

		if (!empty($this->data['product_id'])) {
			// Product to store
			$implode = [];
			$implode[] = 'product_id = ' . $this->bind($this->data['product_id']);
			$implode[] = 'store_id = 0';
			$this->sql .= "INSERT INTO oc_product_to_store SET " . implode(', ', $implode) . ";\n";

			// Product description
			$implode = [];

			$this->description['product_id'] = $this->data['product_id'];
			$this->description['language_id'] = $this->language_id;

			foreach ($this->description as $key => $value) {
				$implode[] = '`' . $key . '` = ' . $this->bind($value);
			}

			if ($implode) {
				$this->sql .= "INSERT INTO oc_product_description SET " . implode(', ', $implode) . ";\n";
			}

			// Product image
			if ($this->images) {
				foreach ($this->images as $image) {
					$implode = [];
					$implode[] = 'product_id = ' . $this->bind($this->data['product_id']);
					$implode[] = 'image = ' . $this->bind($image);
					$implode[] = 'sort_order = 0';

					$this->sql .= "INSERT INTO oc_product_image SET " . implode(', ', $implode) . ";\n";
				}
			}

			// Product category
			if ($this->categories) {
				foreach ($this->categories as $category_id) {
					$implode = [];
					$implode[] = 'product_id = ' . $this->bind($this->data['product_id']);
					$implode[] = 'category_id = ' . $this->bind($category_id);

					$this->sql .= "INSERT INTO oc_product_to_category SET " . implode(', ', $implode) . ";\n";
				}
			}

			// SEO URL
			if ($this->url_alias) {
				$implode = [];
				$implode[] = '`query` = ' . $this->bind('product_id=' . (int)$this->data['product_id']);
				$implode[] = '`keyword` = ' . $this->bind($this->url_alias);

				$this->sql .= "INSERT INTO oc_url_alias SET " . implode(', ', $implode) . ";\n";
			}
		}

		$this->saveSql($name);

		return $this->sql;
	}
}