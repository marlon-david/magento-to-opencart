<?php

namespace MageToOc\OpenCart;

class Base
{
	protected $sql;

	protected function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	protected function bind($value) {
		if ($value === null) {
			return 'NULL';
		} elseif ($value === true) {
			return 'true';
		} elseif ($value === false) {
			return 'false';
		} elseif (is_int($value) || is_float($value)) {
			return $value;
		} else {
			return "'" . $this->escape($value) . "'";
		}
	}

	public function saveSql($name = null) {
		if (!property_exists($this, 'sql') || !$this->sql) {
			throw new \Exception('this->sql not found');
		}

		if (!$name) {
			$name = 'sql-' . date('d-m-Y-H-i-s');
		}

		$dir = __DIR__ . '/../../output/';

		if (!is_dir($dir)) {
			throw new \Exception($dir . ' not found');
		}

		$h = fopen($dir.$name.'.sql', 'a');
		fwrite($h, $this->sql);
		fclose($h);
	}
}