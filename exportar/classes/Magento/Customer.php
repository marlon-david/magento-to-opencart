<?php

namespace MageToOc\Magento;

use MageToOc\MageToOc;
use MageToOc\OpenCart\Address as OCAddress;
use MageToOc\OpenCart\Customer as OCCustomer;

class Customer extends MageToOc {

	const CUSTOM_FIELD_ID_CPF = 1;
	const CUSTOM_FIELD_ID_NASCIMENTO = 2;
	const CUSTOM_FIELD_ID_NUMERO = 3;
	const CUSTOM_FIELD_ID_COMPLEMENTO = 4;
	const CUSTOM_FIELD_ID_RG = null;

	public $dataNascimentoFormat = 'd/m/Y';

	public function getTotal() {
		$results = $this->read->fetchAll("SELECT COUNT(entity_id) AS total FROM customer_entity");

		return $results[0]['total'];
	}

	public function exportCustomers($page = 1) {
		if (is_int($page)) {
			$limit = 50;
			$start = ($page-1) * $limit;
			$results = $this->read->fetchAll("SELECT entity_id FROM customer_entity LIMIT " . $start . ", " . $limit);
		} else {
			$results = $this->read->fetchAll("SELECT entity_id FROM customer_entity");
		}

		$model = \Mage::getModel('customer/customer');
		$modelAddr = \Mage::getModel('customer/address');
		$zones = array();

		foreach ($results as $result) {
			/**
			 * @var Mage_Customer_Model_Customer
			 */
			$customer = $model->load($result['entity_id']);

			if (!$customer || !$customer->getId()) {
				continue;
			}

			$data = $customer->toArray();

			if ($data['entity_id'] != $result['entity_id']) {
				echo 'error in ' . $result['entity_id'] . ', ' . $data['entity_id'] . "\n";
				continue;
			}

			$oc = new OCCustomer;

			$oc->data['customer_id'] = (int)$data['entity_id'];
			$oc->data['address_id'] = (int)$data['default_shipping'];
			$oc->data['status'] = ($data['is_active'] == 1) ? 1 : 0;
			$oc->data['email'] = (string)$data['email'];
			$oc->data['firstname'] = (string)$data['firstname'];
			$oc->data['lastname'] = (string)$data['lastname'];

			$custom = array();

			if (isset($data['taxvat']) && self::CUSTOM_FIELD_ID_CPF) {
				$custom[self::CUSTOM_FIELD_ID_CPF] = $data['taxvat'];
			}

			if (isset($data['dob']) && self::CUSTOM_FIELD_ID_NASCIMENTO) {
				$custom[self::CUSTOM_FIELD_ID_NASCIMENTO] = date($this->dataNascimentoFormat, strtotime($data['dob']));
			}

			if (isset($data['rg_inscricao']) && self::CUSTOM_FIELD_ID_RG) {
				$custom[self::CUSTOM_FIELD_ID_RG] = $data['rg_inscricao'];
			}

			$oc->setCustomFields($custom);

			// no password
			$oc->data['password'] = 'import';

			$oc->data['date_added'] = date('Y-m-d H:i:s', strtotime($data['created_at']));

			$ids = array();

			if ($data['default_shipping']) {
				$ids[] = $data['default_shipping'];
			}

			if ($data['default_billing'] && !in_array($data['default_billing'], $ids)) {
				$ids[] = $data['default_billing'];
			}

			foreach ($ids as $id) {
				$address = $modelAddr->load($id);

				$data = $address->toArray();

				if (!isset($zones[$data['region_id']])) {
					if (isset(OCAddress::$zones[$data['region']])) {
						$zones[$data['region_id']] = OCAddress::$zones[$data['region']];
					} else {
						$zones[$data['region_id']] = 0;
					}
				}

				$ocAddr = new OCAddress();
				$ocAddr->data['address_id'] = (int)$data['entity_id'];
				$ocAddr->data['customer_id'] = (int)$customer->getId();
				$ocAddr->data['zone_id'] = $zones[$data['region_id']];
				$ocAddr->data['postcode'] = (string)$data['postcode'];
				$ocAddr->data['firstname'] = (string)$data['firstname'];
				$ocAddr->data['lastname'] = (string)$data['lastname'];
				$ocAddr->data['address_1'] = (string)$address->getStreet1();
				$ocAddr->data['address_2'] = (string)$address->getStreet4();
				$ocAddr->data['city'] = (string)$data['city'];
				$ocAddr->data['country_id'] = 30;

				$custom = array();

				if (self::CUSTOM_FIELD_ID_NUMERO) {
					$custom[CUSTOM_FIELD_ID_NUMERO] = $address->getStreet2();
				}

				if (self::CUSTOM_FIELD_ID_COMPLEMENTO) {
					$custom[CUSTOM_FIELD_ID_COMPLEMENTO] = $address->getStreet3();
				}

				$ocAddr->setCustomFields($custom);

				if (empty($oc->data['telephone'])) {
					$oc->data['telephone'] = $data['telephone'];
				}

				if (empty($oc->data['fax'])) {
					$oc->data['fax'] = $data['fax'];
				}

				$oc->addAddress($ocAddr);
			}

			$oc->generate('oc_customer_' . $page);
		}

	}
}
