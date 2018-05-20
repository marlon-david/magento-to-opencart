<?php

namespace MageToOc\OpenCart;

class Customer extends Base {

	public $language_id = 2;

	public $data = array(
		'customer_id'       => null,
		'customer_group_id' => 1,
		'store_id'          => 0,
		'language_id'       => 2,
		'firstname'         => '',
		'lastname'          => '',
		'email'             => '',
		'telephone'         => '',
		'fax'               => '',
		'password'          => '',
		'salt'              => '',
		'cart'              => null,
		'wishlist'          => null,
		'newsletter'        => 0,
		'address_id'        => 0,
		'custom_field'      => '',
		'ip'                => '',
		'status'            => 1,
		'approved'          => 1,
		'safe'              => 1,
		'token'             => '',
		'code'              => '',
		'date_added'        => null
	);

	private $addresses = array();

	public function set($group, $key, $value) {
		$this->{$group}[$key] = $value;

		return $this;
	}

	public function get($key) {
		return $this->data[$key];
	}

	public function addAddress(Address $filter_id) {
		if ($filter_id && !in_array($filter_id, $this->addresses)) {
			$this->addresses[] = $filter_id;
		}
	}

	public function setLanguageId($language_id) {
		$this->language_id = (int)$language_id;
	}

	public function setCustomFields(array $customFields) {
		$this->data['custom_field'] = json_encode($customFields);
	}

	public function generate($name = null) {
		$this->sql = '';

		// Customer
		$implode = [];

		foreach ($this->data as $key => $value) {
			$implode[] = '`' . $key . '` = ' . $this->bind($value);
		}

		if ($implode) {
			$this->sql .= "INSERT INTO oc_customer SET " . implode(', ', $implode) . ";\n";
		}

		if (!empty($this->data['customer_id'])) {
			// Customer address
			foreach ($this->addresses as $address) {
				$implode = [];

				$address->data['customer_id'] = $this->data['customer_id'];

				foreach ($address->data as $key => $value) {
					$implode[] = '`' . $key . '` = ' . $this->bind($value);
				}

				if ($implode) {
					$this->sql .= "INSERT INTO oc_address SET " . implode(', ', $implode) . ";\n";
				}
			}
		}

		$this->saveSql($name);

		return $this->sql;
	}
}