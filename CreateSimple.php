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
//$soapHandle = new Client(SOAP_URL_STAGE);
$soapHandle = new Client(SOAP_URL);
$session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
$fetchAttributeList = [$session, 'product_attribute_set.list'];
$attributeSets = $soapHandle->call('call', $fetchAttributeList);
foreach($attributeSets as $setId => $name){
    if($name['name'] == 'Default'){
        $attributeSetId = $name['set_id'];
    }
}
$productSku = ['sku','ATIINSTANTLABK1','ATIP2786IMPOSSIBLEK1','ATIP2785IMPOSSIBLEK2','ATIINSTANTLABK2','ATIP2786IMPOSSIBLEK2','ATIP3107IMPOSSIBLEK1','ATIP2785IMPOSSIBLEK3','ATIINSTANTLABK3','51-0816',
                'COMP-6SAFTX4','HS-B250XT','URC-LOG880','3041-EFESTX2','1086-EFESTX2','3245-EFESTX2','3042-EFEST','4066-EFESTX2','3164-EFESTX2','4084-EFESTX2','AEFEIMR18350P10K2','3164-EFEST','3245-EFEST',
                '1086-EFEST','3041-EFEST','4066-EFEST','4084-EFEST','3891-EFEST','AEFEIMR18350P10K1','WP812B','KODAAENERG12','TV434','ALEXLSD16GCTBK1','1163-MACK','COMP-4SAFTX2','COMP-4SAFTX4','COMP-4SAFT',
                'COMP-4SAFTX5','COMP-4SAFTX10','COMP-4SAFTX25','ASLICBHK1','FILS49','SUR6277','09064','TH800667','AVORD5012K1','AVORVNQ1026K1','AWES2331K1','1365-659','SH0BZ'];





$newProductData = [
//    'scope'             =>  'global',
    'name'              => 'test product # 1',
    // websites - Array of website ids to which you want to assign a new product
    'websites'          => [3], // array(1,2,3,...)
    'short_description' => 'short description',
    'description'       => 'description',
//    'price'             => 12.05,
    'status'            => 2,
//    'visibility'        => 1,
//    'manufacturer'      =>  1,
//    'color'             =>  1,
];
//$packet = [$session, 'dumb_create.createrandom', ['configurable',$attributeSetId, $productSku]];
$packet = [$session, 'catalog_product.create', ['simple',$attributeSetId, $productSku, $newProductData]];

$entityId = $soapHandle->call('call',$packet);
var_dump($newProductData);
var_dump($entityId);