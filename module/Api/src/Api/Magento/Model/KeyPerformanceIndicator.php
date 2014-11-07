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
//        echo $resultSet->count();
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
//        echo $resultSet->count();
        return $resultSet->count();
    }

    /**
     * Counts updated attribute products.
     * @visibility protected
     * @param $tableType
     * @param $attId
     * @param $varcharCount
     * @return int $varcharCount
     */
    protected function countVarchar($tableType, $attId, $varcharCount)
    {
        $varchar = $this->sql->select()->from('productattribute_'.$tableType)->where(['attribute_id'=>$attId, 'dataState'=>1]);
        $varcharStatement = $this->sql->prepareStatementForSqlObject($varchar);
        $varcharResult = $varcharStatement->execute();
        $varcharResultSet = new ResultSet;
        if ($varcharResult instanceof ResultInterface && $varcharResult->isQueryResult()) {
            $varcharResultSet->initialize($varcharResult);
        }
        $varcharCount += $varcharResultSet->count();
//        echo $varchar->getSqlString(new \Pdo($this->adapter)) . " \n varchar \n";

        return $varcharCount;
    }

    /**
     * Counts updated attribute products.
     * @visibility protected
     * @param $tableType
     * @param $attId
     * @param $textCount
     * @return int $textCount
     */
    protected function countText($tableType, $attId, $textCount)
    {
        $text = $this->sql->select()->from('productattribute_'.$tableType)->where(['attribute_id'=>$attId, 'dataState'=>1]);
        $textStatement = $this->sql->prepareStatementForSqlObject($text);
        $textResult = $textStatement->execute();
        $textResultSet = new ResultSet;
        if ($textResult instanceof ResultInterface && $textResult->isQueryResult()) {
            $textResultSet->initialize($textResult);
        }
        $textCount += $textResultSet->count();
//        echo $text->getSqlString(new \Pdo($this->adapter)) . " \n text \n";

        return $textCount;
    }

    /**
     * Counts updated attribute products.
     * @visibility protected
     * @param $tableType
     * @param $attId
     * @param $intCount
     * @return int $intCount
     */
    protected function countInt($tableType, $attId, $intCount)
    {
        $int = $this->sql->select()->from('productattribute_'.$tableType)->where(['attribute_id'=>$attId, 'dataState'=>1]);
        $intStatement = $this->sql->prepareStatementForSqlObject($int);
        $intResult = $intStatement->execute();
        $intResultSet = new ResultSet;
        if ($intResult instanceof ResultInterface && $intResult->isQueryResult()) {
            $intResultSet->initialize($intResult);
        }
        $intCount += $intResultSet->count();
//        echo $int->getSqlString(new \Pdo($this->adapter)) . " \n int \n";

        return $intCount;
    }

    /**
     * @visibility public
     * @return int
     */
    public function updateCount()
    {
//        echo '<pre>';
        $select = $this->sql->select()
                  ->from('product')
                  ->columns([
                        'entityId'  =>  'entity_id'
                        ]);
//                  ->where([
//                        'dataState' =>  1
//                        ]);
//        $int = new Expression("i.entity_id=product.entity_id and dataState = 1");
//        $select->join(['i'=>'productattribute_int'],$int,['value'=>'value']);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $productUpdates = $resultSet->toArray();
//        $prodUpdateCount = $resultSet->count();
//        $this->setProductCount($prodUpdateCount);
        $lookup = $this->productAttributeLookup( $this->sql );
        $attributeCount = 0;
        $varcharCount = $intCount = $textCount = 0;
        foreach( $lookup as $attributes ) {
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            if ( $dataType === 'varchar' ) {
                $varcharCount = $this->countVarchar('varchar',$attributeId, $varcharCount);
//                $varchar = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
//                $varcharStatement = $this->sql->prepareStatementForSqlObject($varchar);
//                $varcharResult = $varcharStatement->execute();
//                $varcharResultSet = new ResultSet;
//                if ($varcharResult instanceof ResultInterface && $varcharResult->isQueryResult()) {
//                    $varcharResultSet->initialize($varcharResult);
//                }
//                $varcharCount += $varcharResultSet->count();
//                echo $varchar->getSqlString(new \Pdo($this->adapter)) . " \n varchar \n";

                return $varcharCount;
            }
            if ( $dataType === 'int' ) {
                if ( $attributeId != 1 ) {
                    $intCount = $this->countInt('int',$attributeId, $intCount);
//                    echo $int->getSqlString(new \Pdo($this->adapter)) . " \n int";
                }
            }
            if ( $dataType === 'text' ) {
                $textCount = $this->countText('text',$attributeId, $textCount);
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
//        echo $varcharCount . ' ' . $intCount . ' ' . $textCount . ' these are all the counts individually';
        $attributeCount = (int)$varcharCount + (int)$intCount + (int)$textCount;
//        echo $attributeCount . 'in model attribute count ';
//        $this->setProductAttributeCount($attributeCount);
        return $attributeCount;//$this->getProductAttributeCount();
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