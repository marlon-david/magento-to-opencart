<?php
namespace MageToOc;

class MageToOc {
	public $resource;

	public $read;

	public function __construct() {
		$this->resource = \Mage::getSingleton('core/resource');

		$this->read = $this->resource->getConnection('core_read');
	}
}