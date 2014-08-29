<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/28/14
 * Time: 7:10 PM
 */

include 'config/autoload/local.php';
include 'init_autoloader.php';
use Zend\Soap\Client;
$attributeSetId = '';
$soapHandle = new Client(SOAP_URL_STAGE);
$session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
$fetchAttributeList = [$session, 'product_attribute_set.list'];
$attributeSets = $soapHandle->call('call', $fetchAttributeList);
foreach($attributeSets as $setId => $name){
    if($name['name'] == 'Default'){
        $attributeSetId = $name['set_id'];
    }
}
$productSku = '0123456789ABCD-config';
$newProductData = [
    'name'              => 'test product # 1',
    // websites - Array of website ids to which you want to assign a new product
    'websites'          => [3], // array(1,2,3,...)
    'short_description' => 'short description',
    'description'       => 'description',
    'price'             => 12.05,
    'status'            => 2,
    'visibility'        => 1,
    'manufacturer'      =>  1,
    'color'      =>  1,
];
$packet = [$session, 'config_product.create', ['configurable',$attributeSetId, $productSku,$newProductData ]];

$entityId = $soapHandle->call('call',$packet);
var_dump($entityId);