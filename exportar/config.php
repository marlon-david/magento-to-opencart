<?php

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd() . '/..');

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
	include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';

if (!file_exists($mageFilename)) {
	echo $mageFilename." was not found";
	exit;
}

if (file_exists(MAGENTO_ROOT . '/app/bootstrap.php')) {
	require MAGENTO_ROOT . '/app/bootstrap.php';
}
require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
	Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

$app = Mage::app($mageRunCode, $mageRunType);

/* Fim do inicializador do Magento */

error_reporting(E_ALL);

/* autoload */
spl_autoload_register(function($class) {
	if (substr($class, 0, 9) == 'MageToOc\\') {
		$file = __DIR__ . '/classes/' . str_replace('\\', '/', substr($class, 9)) . '.php';

		if (file_exists($file)) {
			require $file;
		}
	}
});