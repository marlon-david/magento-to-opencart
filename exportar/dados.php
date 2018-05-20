<?php

require 'config.php';


use MageToOc\MageToOc;

$json = array();

$produtos = new \MageToOc\Magento\Product;
$json['produtos'] = ceil($produtos->getTotal() / 50);

$categorias = new \MageToOc\Magento\Category;
$json['categorias'] = ceil($categorias->getTotal() / 50);

$clientes = new \MageToOc\Magento\Customer;
$json['clientes'] = ceil($clientes->getTotal() / 50);

header('Content-type: application/json');
echo json_encode($json);