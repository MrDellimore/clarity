<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/17/14
 * Time: 9:58 AM
 */
//use Zend\Soap\Client;

include 'config/autoload/local.php';
include 'init_autoloader.php';
//use Zend\Soap\Client;
//$soapHandle = new Client(SOAP_URL_STAGE);
//$soapHandle = new Client(SOAP_URL);
//$session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
$soapHandle = new SoapClient(SOAP_URL);
$session = $soapHandle->login(SOAP_USER, SOAP_USER_PASS);

//$entityId = $soapHandle->call('call',$packet);
//var_dump($newProductData);
//var_dump($entityId);
$result = $soapHandle->call($session, 'catalog_product_link.remove', array('type' => 'related', 'product' => '248', 'linkedProduct' => '676'));

$result = $soapHandle->call($session, 'catalog_product_link.remove', array('type' => 'related', 'product' => '391', 'linkedProduct' => '168'));
//$result1 = $soapHandle->call($session, 'catalog_product_link.remove', array('type' => 'related', 'product' => '391', 'linkedProduct' => '7868'));
echo "\n";
var_dump ($result, $result1);