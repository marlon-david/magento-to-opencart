<?php

namespace MageToOc\Magento;

use MageToOc\MageToOc;
use MageToOc\OpenCart\Category as OCCategory;

class Category extends MageToOc {
	public function getTotal() {
		$results = $this->read->fetchAll("SELECT COUNT(entity_id) AS total FROM catalog_category_entity");

		return $results[0]['total'];
	}

	public function exportCategories($page = 1) {
		if (is_int($page)) {
			$limit = 50;
			$start = ($page-1) * $limit;
			$results = $this->read->fetchAll("SELECT entity_id FROM catalog_category_entity LIMIT " . $start . ", " . $limit);
		} else {
			$results = $this->read->fetchAll("SELECT entity_id FROM catalog_category_entity");
		}

		$model = \Mage::getModel('catalog/category');

		foreach ($results as $result) {
			if ($result['entity_id'] == 1) {
				continue;
			}

			$category = $model->load($result['entity_id']);

			if (!$category || !$category->getId()) {
				continue;
			}

			$data = $category->toArray();

			if ($data['entity_id'] != $result['entity_id']) {
				echo 'error in ' . $result['entity_id'] . ', ' . $data['entity_id'] . "\n";
				continue;
			}

			$oc = new OCCategory;

			if ($data['entity_id'] < 3) {
				$oc->data['status'] = 0;
			}

			if ($data['parent_id'] == 1) {
				$oc->data['status'] = 0;
				$oc->data['parent_id'] = 0;
			} elseif ($data['parent_id'] == 2) {
				$oc->data['status'] = (int)$data['is_active'];
				$oc->data['parent_id'] = 0;
				$oc->data['top'] = (int)$data['include_in_menu'];
			} else {
				$oc->data['status'] = (int)$data['is_active'];
				$oc->data['parent_id'] = (int)$data['parent_id'];
				$oc->data['top'] = 0;
			}

			$oc->data['category_id'] = (int)$data['entity_id'];
			$oc->data['sort_order'] = (int)$data['position'];
			$oc->data['date_added'] = date('Y-m-d H:i:s', strtotime($data['created_at']));
			$oc->data['date_modified'] = date('Y-m-d H:i:s', strtotime($data['updated_at']));

			$oc->description['name'] = $data['name'];

			if (!empty($data['meta_title'])) {
				$oc->description['meta_title'] = (string)$data['meta_title'];
			} else {
				$oc->description['meta_title'] = (string)$data['name'];
			}

			$oc->description['meta_description'] = (string)$data['meta_description'];
			$oc->description['meta_keyword'] = (string)$data['meta_keyword'];
			$oc->description['description'] = (string)$data['description'];

			if (!empty($data['url_path'])) {
				$oc->setUrlAlias($data['url_path']);
			}

			$oc->generate('oc_category_' . $page);
		}
	}
}
