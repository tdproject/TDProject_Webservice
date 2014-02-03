<?php
ini_set("soap.wsdl_cache_enabled", 0);

set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/TestProject/');
require_once 'Zend/Soap/Server.php';
require_once 'Zend/Soap/AutoDiscover.php';

require_once 'soap_methods.php';


$wsdl = 'http://localhost/TestProject/soap/soap_server.php?wsdl=1';

if(isset($_GET['wsdl']) && $_GET['wsdl'] == 1) {
	$autodiscover = new Zend_Soap_AutoDiscover();
	$autodiscover->setClass('soap_methods');
	$autodiscover->handle();
} else {
	$server = new Zend_Soap_Server($wsdl);
	$server->setClass('soap_methods');
	$server->handle();
}