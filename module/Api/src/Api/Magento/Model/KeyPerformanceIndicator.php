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
     * Fetches the count to how many new products/skus have been created and that have content_reviewed (1676) with a value of 1.
     * @return int
     */
    public function fetchNewCount()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(['id'=>'entity_id']);
        $select->where(array( 'product.dataState' => '2'));
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    /**
     * Fetches the count to how many new images have been uploaded to existing products/skus in Spex and and sent to Mage.
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
     * Fetches the count to how many categories are to be created or deleted in Mage and updated in Spex.
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
     * Fetches the count to how many related products have to be created or deleted in Mage and updated Spex.
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
     * This method will have to be refactored/audited. Since Andrew moved over status, price, qty, and content_reviewed to the product table.
     * If these ever happen to change then this method has to compensate for that.
     * qty, price, and content_reviewed I don't think will because qty and price are updated from a job that Andrew made from Management Studio to Mage.
     * status I dont' know but it's doubtful it will change.
     * But there are three attribute tables that will possible change: varchar, text, and int. There is also decimal but like I said price is updated from Management Studio
     * One possible bug that I can think of is that some new products/skus may have a 1 for some of their attributes. If that api call is make for that specific attribute and the sku doesn't exist in Mage,
     * Mage will return an error and that error will be logged in the Mage History tab I created. So look out for this. You can check Mage Admin if the Sku exists if not, it will have to be created.
     * If it is created it will not longer be seen by this method.
     * @visibility public
     * @return int
     */
    public function updateCount()
    {
        $lookup = $this->productAttributeLookup( $this->sql );
        $varcharCount = $intCount = $textCount = 0;
        foreach( $lookup as $attributes ) {
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            if ( $dataType === 'varchar' ) {
                $varchar = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                $varcharStatement = $this->sql->prepareStatementForSqlObject($varchar);
                $varcharResult = $varcharStatement->execute();
                $varcharResultSet = new ResultSet;
                if ($varcharResult instanceof ResultInterface && $varcharResult->isQueryResult()) {
                    $varcharResultSet->initialize($varcharResult);
                }
                $varcharCount += $varcharResultSet->count();
            }
            if ( $dataType === 'int' ) {
                if ( $attributeId != 1 ) {
                    $int = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                    $intStatement = $this->sql->prepareStatementForSqlObject($int);
                    $intResult = $intStatement->execute();
                    $intResultSet = new ResultSet;
                    if ($intResult instanceof ResultInterface && $intResult->isQueryResult()) {
                        $intResultSet->initialize($intResult);
                    }
                    $intCount += $intResultSet->count();
                }
            }
            if ( $dataType === 'text' ) {
                $text = $this->sql->select()->from('productattribute_'.$dataType)->where(['attribute_id'=>$attributeId, 'dataState'=>1]);
                $textStatement = $this->sql->prepareStatementForSqlObject($text);
                $textResult = $textStatement->execute();
                $textResultSet = new ResultSet;
                if ($textResult instanceof ResultInterface && $textResult->isQueryResult()) {
                    $textResultSet->initialize($textResult);
                }
                $textCount += $textResultSet->count();
            }
        }
        $attributeCount = (int)$varcharCount + (int)$intCount + (int)$textCount;
        return $attributeCount;
    }

}