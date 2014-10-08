<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/6/14
 * Time: 2:13 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;
use Zend\Db\Sql\Expression;

class ConsoleMagentoTable
{
    use Spex;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function changedProducts()
    {
        $soap = [];
        $count = 0;
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $select->where(array( 'dataState' => '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
//        var_dump($this->sql->prepareStatementForSqlObject($select));
//        die();
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
//        var_dump($products);
        foreach ($products as $product) {
            $entityId = $product['id'];
            $soap[$count]['id'] = $entityId;
            $soap[$count]['sku'] = $product['sku'];
            $results = $this->productAttributeLookup($this->sql);
            foreach($results as $attribute){
                $dataType = $attribute['dataType'];
                $attributeId = $attribute['attId'];
                $attributeCode = $attribute['attCode'];
                $selectAttributes = $this->sql->select()->from('productattribute_'.$dataType)->columns([$attributeCode=>'value'])->where(['attribute_id'=>$attributeId, 'entity_id'=>$entityId, 'dataState'=>1]);
                $statementAttributes = $this->sql->prepareStatementForSqlObject($selectAttributes);
                $resultAttributes = $statementAttributes->execute();
                $resultSetAttributes = new ResultSet;
                if ($resultAttributes instanceof ResultInterface && $resultAttributes->isQueryResult()) {
                    $resultSetAttributes->initialize($resultAttributes);
                }
                $attributes = $resultSetAttributes->toArray();
                foreach ($attributes as $atts ) {
                    $soap[$count][$attributeCode] = $atts[$attributeCode];
                }
            }
//            return $result;
            $count++;
        }
//        var_dump($soap);
//        die();
        return $soap;
//        var_dump($soap);
    }

    public function fetchNewItems()
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = [];
        $count = 0;
        $select = $this->sql->select()->from('product')->columns([
            'entityId'      =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'dateCreated'   =>  'creationdate',
        ])->where(array('dataState'=>2));


        $statement = $this->sql->prepareStatementForSqlObject($select);
        var_dump($statement);
        die();
//        echo 'works';
//        die();
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        foreach($products as $index => $value){
            $entityId = $value['entityId'];
            $attributes = $this->productAttributeLookup($this->sql);
            $soapBundle[$count]['sku'] = $value['sku'];
            foreach( $attributes as $key => $fields ){
                $tableType = $fields['dataType'];
                $attributeId = (int)$fields['attId'];
                $attributeCode = $fields['attCode'];
                $attributeValues = $this->productAttribute($this->sql, [$attributeCode=>'value'],['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2],$tableType)->toArray();
                foreach($attributeValues as $keyValue => $valueOption){
                    $soapBundle[$count]['website'] = $value['website'];
                    $soapBundle[$count][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                }
            }
            $count++;

        }
//        var_dump($soapBundle);
//        die();

        return $soapBundle;
    }

} 