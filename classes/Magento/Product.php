<?php

namespace MageToOc\Magento;

use MageToOc\MageToOc;
use MageToOc\OpenCart\Product as OCProduct;

class Product extends MageToOc {
	public function getTotal() {
		$results = $this->read->fetchAll("SELECT COUNT(entity_id) AS total FROM catalog_product_entity");

		return $results[0]['total'];
	}

	public function exportProducts($page = 1) {
		if (is_int($page)) {
			$limit = 50;
			$start = ($page-1) * $limit;
			$results = $this->read->fetchAll("SELECT entity_id FROM catalog_product_entity LIMIT " . $start . ", " . $limit);
		} else {
			$results = $this->read->fetchAll("SELECT entity_id FROM catalog_product_entity");
		}

		$model = \Mage::getModel('catalog/product');

		foreach ($results as $result) {
			$product = $model->load($result['entity_id']);

			if (!$product || !$product->getId()) {
				continue;
			}

			$data = $product->toArray();

			if ($data['entity_id'] != $result['entity_id']) {
				echo 'erro em ' . $result['entity_id'] . ', ' . $data['entity_id'] . "\n";
				continue;
			}

			$oc = new OCProduct;
			$oc->data['product_id'] = (int)$result['entity_id'];

			if ($data['sku']) {
				$oc->data['model'] = (string)$data['sku'];
			} else {
				$oc->data['model'] = $result['entity_id'];
			}

			$oc->data['sku'] = (string)$data['sku'];
			$oc->data['date_added'] = date('Y-m-d H:i:s', strtotime($data['created_at']));
			$oc->data['date_modified'] = date('Y-m-d H:i:s', strtotime($data['updated_at']));
			$oc->data['weight'] = (float)$data['weight'];
			$oc->data['price'] = (float)$data['price'];

			if (isset($data['volume_comprimento']))
				$oc->data['length'] = (float)$data['volume_comprimento'];
			if (isset($data['volume_altura']))
				$oc->data['height'] = (float)$data['volume_altura'];
			if (isset($data['volume_largura']))
				$oc->data['width'] = (float)$data['volume_largura'];

			if (!empty($data['stock_item'])) {
				$oc->data['quantity'] = intval($data['stock_item']['qty']);
				$oc->data['minimum'] = intval($data['stock_item']['min_sale_qty']);
			}

			if ($data['status'] == 1) {
				$oc->data['status'] = 1;
			} else {
				$oc->data['status'] = 0;
			}

			$oc->description['name'] = $data['name'];

			if (!empty($data['meta_title'])) {
				$oc->description['meta_title'] = (string)$data['meta_title'];
			} else {
				$oc->description['meta_title'] = (string)$data['name'];
			}

			$oc->description['meta_description'] = (string)$data['meta_description'];
			$oc->description['meta_keyword'] = (string)$data['meta_keyword'];
			$oc->description['description'] = (string)$data['description'];

			if (!empty($data['image'])) {
				$oc->addImage($data['image']);
			}

			if (!empty($data['media_gallery']['images'])) {
				foreach ($data['media_gallery']['images'] as $image) {
					$oc->addImage($image['file']);
				}
			}

			if (!empty($data['url_path'])) {
				$oc->setUrlAlias($data['url_path']);
			}

			$categories = $product->getCategoryIds();

			foreach ($categories as $category_id) {
				if ($category_id > 2) {
					$oc->addCategory($category_id);
				}
			}

			$oc->generate('oc_product_' . $page);
		}

	}
}
