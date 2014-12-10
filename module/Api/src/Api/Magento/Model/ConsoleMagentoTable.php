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
    /**
     * Trait
     */
    use Spex;

    /**
     * @var array
     */
    protected $stockData  = [
        'qty'=>'qty',
        'is_in_stock'=> 'is_in_stock',
    ];

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * This method can possibly be combined with updateToClean for reusable code in the MagentoTable since they both do the same thing.
     * Only differnce is that the indeces are not the same due to the indeces coming from the datatable.
     * @param $changedProducts
     * @return string
     */
    public function updateToClean($changedProducts)
    {
        $result = '';
        $entityId = $changedProducts['id'];
        $sku = $changedProducts['sku'];
        array_shift($changedProducts);
        array_shift($changedProducts);
        foreach ( $changedProducts as $attribute => $attValue) {
            $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attribute]);
            $attributeId = $lookup[0]['attId'];
            $dataType = $lookup[0]['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$entityId]);
            $prdAttStatement = $this->sql->prepareStatementForSqlObject($update);
            $prdAttStatement->execute();
        }
        $result .= $entityId . ' with Product ID ' . $sku . " has been pushed to Mage Admin .\n";
        return $result;
    }

    /**
     * @Description: what this Method does is stack all attributes under it's correspoding entity id.
     * @param $products
     * @return array | $grouped
     * */
    public function groupProducts($products)
    {
        $count = 0;
        $changedAttributes = $changedValue = $changedID = $changedSkus = $grouped = [];
        foreach ( $products as $product ) {
            $changedID[$count] = $product['id'];
            $changedSkus[$count] = $product['sku'];
            array_shift($product);
            array_shift($product);
            $keys = array_keys($product);
            foreach ( $keys as $attCount => $attribute ) {
                $changedAttributes[$count] = $attribute;
                $changedValue[$count] = $product[$attribute];
            }
            $count++;
        }
        $uniqueIds = array_values(array_unique($changedID));
        $count = 0;
        foreach ($uniqueIds as $key => $uids) {
            $grouped[$key]['id'] = $uids;
            $grouped[$key]['sku'] = $changedSkus[$count];
            foreach ( $changedID as $index => $ids ) {
                if ( $uids == $ids ) {
                        $grouped[$count][$changedAttributes[$index]] = $changedValue[$index];
                    }
                }
            $count++;
            }
        return $grouped;
    }

    /**
     * @Description: This method grabs all product attributes with a dataState of 1(changed). For attribute qty it adds another array called stock_data.
     * Be mindful of this particular attribute, otherwise it will not update in Mage. Please refer to http://www.magentocommerce.com/api/soap/catalog/catalogProduct/catalog_product.update.html
     * for more details. Look at catalogInventoryStockItemUpdateEntity for further explanation.
     * It's the same as fetchChangedProducts in the MagentoTable class but that method is for grabbing changed attributes to display in the data table.
     * @param null
     * @return array | $soap
     */
    public function changedProducts()
    {
        $soap = [];
        $count = 0;
        $selectAttributes = '';
//        $select = $this->sql->select()->from('product')->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate'));
//        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
//        $select->where(array( 'dataState' => 1));

//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        var_dump($this->sql->prepareStatementForSqlObject($select));
//        die();
//        $result = $statement->execute();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $products = $resultSet->toArray();
        $lookup = $this->productAttributeLookup($this->sql);
//        foreach ($products as $product) {
            foreach( $lookup as $attribute ) {
                $dataType = $attribute['dataType'];
                $attributeId = $attribute['attId'];
                $attributeCode = $attribute['attCode'];
                if( $attributeCode != 'qty' ) {
                    $selectAttributes = $this->sql->select()->from('productattribute_'.$dataType)->columns([$attributeCode=>'value'])->where(['attribute_id'=>$attributeId, 'productattribute_'.$dataType.'.dataState'=>1]);
                    $selectAttributes->join(array('u' => 'users'),'u.userid = productattribute_'.$dataType.'.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'),Select::JOIN_LEFT);
                    $selectAttributes->join(array('p' => 'product'),'p.entity_id = productattribute_'.$dataType.'.entity_id' ,array('id' => 'entity_id', 'sku' => 'productid'),Select::JOIN_LEFT);
                }
                $statementAttributes = $this->sql->prepareStatementForSqlObject($selectAttributes);
                $resultAttributes = $statementAttributes->execute();
                $resultSetAttributes = new ResultSet;
                if ($resultAttributes instanceof ResultInterface && $resultAttributes->isQueryResult()) {
                    $resultSetAttributes->initialize($resultAttributes);
                }
                $attributes = $resultSetAttributes->toArray();
                foreach ($attributes as $atts ) {
                    $soap[$count]['id'] = $atts['id'];
                    $soap[$count]['sku'] = $atts['sku'];
//                    if( $attributeCode == 'qty' ) {
//                        $soap[$count]['stock_data'][$attributeCode] = $atts[$attributeCode];
//                    } else {
//                    if ( $attributeCode != 'qty' ) {
                    $soap[$count][$attributeCode] = $atts[$attributeCode];
                    $count++;
                }
            }
//        }
        return $soap;
    }

    /**
     * This method grabs all skus that are new but that have content_reviewed (1676) with a value of 1.
     * It can also be combined with fetchNewItems in MagentoTable. That method has three arguments. But it can be adapted.
     * @return array
     */
    public function fetchNewItems()
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = [];
        $count = 0;
        $select = $this->sql->select()->from('product')->columns([
            'id'      =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'dateCreated'   =>  'creationdate',
        ])->where(array('dataState'=>2));
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
//        start of foreach
        foreach( $products as $index => $value ) {
            $entityId = $value['id'];
            $attributes = $this->productAttributeLookup($this->sql);
            $soapBundle[$count]['id'] = $value['id'];
            $soapBundle[$count]['sku'] = $value['sku'];
//            start of foreach
            foreach( $attributes as $fields ) {
                $tableType = $fields['dataType'];
                $attributeId = (int)$fields['attId'];
                $attributeCode = $fields['attCode'];
                $selectAttributes = $this->sql->select()->from('productattribute_'.$tableType)
                          ->columns([$attributeCode=>'value'])
//                I took off the dataState of 2 here because we don't care what the dataState is for the attributes. We justs care of the product/sku
//                has a dataState of 2 and the attribute_id 1676 has a value of 1.
                          ->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId]);//, 'dataState'=>2
                $statementAtts = $this->sql->prepareStatementForSqlObject($selectAttributes);
                $resultAtts = $statementAtts->execute();
                $resultSetAtts = new ResultSet;
                if ($resultAtts instanceof ResultInterface && $resultAtts->isQueryResult()) {
                    $resultSetAtts->initialize($resultAtts);
                }
                $attributeValues = $resultSetAtts->toArray();
//                start of foreach
                foreach( $attributeValues as $keyValue => $valueOption ) {
                    $soapBundle[$count]['websites'] = [$value['website']];
//                    $soapBundle[$count][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
//                    start of if
                    if ( array_key_exists($attributeCode,$this->stockData) ) {
                        $soapBundle[$count]['stock_data'][$attributeCode] = $valueOption[$attributeCode];
                    } else {
                        if( is_null($attributeValues[$keyValue][$attributeCode]) && $attributeCode ==  'status' ){
                            $soapBundle[$count][$attributeCode] = 0;
                        }
//                        start of if
                        if( isset($attributeValues[$keyValue][$attributeCode]) ) {
//                            if( $attributeCode == 'content_reviewed' && (int)$valueOption[$attributeCode] == 1 ) {
                                if ( $attributeCode ==  'status' ) {
                                    $soapBundle[$count][$attributeCode] = (int)$valueOption[$attributeCode];
                                } else {
                                    $soapBundle[$count][$attributeCode] = $valueOption[$attributeCode];
                                }
//                            }
                        } // end of if
                    } // end of if
                }   // end of foreach
            } // end of foreach
            $count++;
        } // end of foreach
        return $soapBundle;
    }

} 