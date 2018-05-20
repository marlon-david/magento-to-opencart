<?php

namespace MageToOc\OpenCart;

class Address extends Base {

	public $data = array(
		'address_id'   => null,
		'customer_id'  => 0,
		'firstname'    => '',
		'lastname'     => '',
		'company'      => '',
		'address_1'    => '',
		'address_2'    => '',
		'city'         => '',
		'postcode'     => '',
		'country_id'   => 30,
		'zone_id'      => 0,
		'custom_field' => ''
	);

	public static $zones = array(
		'Acre' => 440,
		'Alagoas' => 441,
		'Amapá' => 442,
		'Amazonas' => 443,
		'Bahia' => 444,
		'Ceará' => 445,
		'Distrito Federal' => 446,
		'Espírito Santo' => 447,
		'Goiás' => 448,
		'Maranhão' => 449,
		'Mato Grosso' => 450,
		'Mato Grosso do Sul' => 451,
		'Minas Gerais' => 452,
		'Pará' => 453,
		'Paraíba' => 454,
		'Paraná' => 455,
		'Pernambuco' => 456,
		'Piauí' => 457,
		'Rio de Janeiro' => 458,
		'Rio Grande do Norte' => 459,
		'Rio Grande do Sul' => 460,
		'Rondônia' => 461,
		'Roraima' => 462,
		'Santa Catarina' => 463,
		'São Paulo' => 464
	);

	public function set($group, $key, $value) {
		$this->{$group}[$key] = $value;

		return $this;
	}

	public function get($key) {
		return $this->data[$key];
	}

	public function setCustomFields(array $customFields) {
		$this->data['custom_field'] = json_encode($customFields);
	}

}