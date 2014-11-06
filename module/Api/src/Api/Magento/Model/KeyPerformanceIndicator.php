<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 9/25/14
 * Time: 9:26 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Db\Sql\Expression;


class KeyPerformanceIndicator {

    /**
     * @trait
     */
    use Spex;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var Sql Object;
     */
    protected $sql;

    /**
     * @var integer;
     */
    protected $productCount;

    /**
     * @var integer;
     */
    protected $attributeCount;

    /**
     * @access public
     * @params Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * @return int
     */
    public function fetchNewCount()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->where(array( 'product.dataState' => '2'));
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        echo $select->getSqlString(new \Pdo($this->adapter));
        return $resultSet->count();
    }

    /**
     * @return int
     */
    public function fetchImageCount()
    {
        $select = $this->sql->select()->from('productattribute_images')->where(['dataState'=>2]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    /**
     * @return int
     */
    public function fetchCategoryCount()
    {
        $filter = new Where;
        $filter->in('productcategory.dataState',[2,3]);
        $select = $this->sql->select()->from('productcategory')->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    /**
     * @return int
     */
    public function fetchLinkedCount()
    {
        $filter = new Where;
        $filter->in('productlink.dataState',[2,3]);
        $select = $this->sql->select()->from('productlink')->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    /**
     * @visibility public
     * @return int
     */
    public function updateCount()
    {
//        echo '<pre>';
//        $select = $this->sql->select()
//                  ->from('product')
//                  ->columns([
//                        'entityId'  =>  'entity_id'
//                        ])
//                  ->where([
//                        'dataState' =>  1
//                        ]);
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
////        $productUpdates = $resultSet->toArray();
//        $prodUpdateCount = $resultSet->count();
//        $this->setProductCount($prodUpdateCount);
        $lookup = $this->productAttributeLookup( $this->sql );
        $attributeCount = 0;
        $varcharCount = $intCount = $textCount = 0;
        foreach( $lookup as $attributes ) {
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            if ( $dataType == 'varchar' ) {
                $varchar = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                $statement = $this->sql->prepareStatementForSqlObject($varchar);
                $result = $statement->execute();
                $resultSet = new ResultSet;
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet->initialize($result);
                }
                $varcharCount += $resultSet->count();
            }
            if ( $dataType == 'int' ) {
                if ( $attributeId != 1 ) {
                    $int = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                    $statement = $this->sql->prepareStatementForSqlObject($int);
                    $result = $statement->execute();
                    $resultSet = new ResultSet;
                    if ($result instanceof ResultInterface && $result->isQueryResult()) {
                        $resultSet->initialize($result);
                    }
                    $intCount += $resultSet->count();
                }
            }
            if ( $dataType == 'text' ) {
                $text = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                $statement = $this->sql->prepareStatementForSqlObject($text);
                $result = $statement->execute();
                $resultSet = new ResultSet;
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet->initialize($result);
                }
                $textCount += $resultSet->count();
            }

//            $int = $this->sql->select()->from('productattribute_int')->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
//            $statement = $this->sql->prepareStatementForSqlObject($varchar);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
//            $varcharCount = $resultSet->count();
//            $select = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
//            $statement = $this->sql->prepareStatementForSqlObject($select);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
//            $attributeCount += $resultSet->count();
//            $attributeCount += $this->productAttribute($this->sql, [], ['attribute_id'=>$attributeId, 'dataState'=>1], $dataType)->count();
        }
        $attributeCount = $varcharCount + $intCount + $textCount;
//        echo $varcharCount . ' ' . $intCount . ' ' . $textCount;
        $this->setProductAttributeCount($attributeCount);
        return $this->getProductAttributeCount();
    }

    public function setProductCount($prodCount)
    {
        $this->productCount = $prodCount;
    }

    public function setProductAttributeCount($attributeCount)
    {
        $this->attributeCount = $attributeCount;
    }

    public function getProductCount()
    {
        return $this->productCount;
    }

    public function getProductAttributeCount()
    {
        return $this->attributeCount;
    }

}