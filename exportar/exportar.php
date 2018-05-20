<?php

require 'config.php';

$dir = './output/';

if (isset($_GET['page'])) {
	$page = (int)$_GET['page'];
} else {
	$page = 1;
}

if (isset($_GET['acao'])) {
	$acao = $_GET['acao'];
} else {
	$acao = '';
}

if ($acao == 'produtos') {
	$produtos = new \MageToOc\Magento\Product();

	if (file_exists($dir . 'oc_product_' . $page . '.sql')) {
		unlink($dir . 'oc_product_' . $page . '.sql');
	}

	$produtos->exportProducts($page);
	echo 'Produtos exportados<br>';
}

if ($acao == 'categorias') {
	if (file_exists($dir . 'oc_category_' . $page . '.sql')) {
		unlink($dir . 'oc_category_' . $page . '.sql');
	}

	$cats = new \MageToOc\Magento\Category();
	$cats->exportCategories($page);
	echo 'Categorias exportadas<br>';
}

if ($acao == 'clientes') {
	if (file_exists($dir . 'oc_customer_' . $page . '.sql')) {
		unlink($dir . 'oc_customer_' . $page . '.sql');
	}

	$cats = new \MageToOc\Magento\Customer();
	$cats->exportCustomers($page);
	echo 'Clientes exportados<br>';
}