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

    public function updateCount()
    {
//        echo '<pre>';
        $select = $this->sql->select()
                  ->from('product')
                  ->columns([
                        'entityId'  =>  'entity_id'
                        ])
                  ->where([
                        'dataState' =>  1
                        ]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $productUpdates = $resultSet->toArray();
        $prodUpdateCount = $resultSet->count();
        $this->setProductCount($prodUpdateCount);
        $lookup = $this->productAttributeLookup( $this->sql );
        $attributeCount = 0;
        foreach( $lookup as $index => $attributes ) {
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            $attributeCount += $this->productAttribute($this->sql, [], ['attribute_id'=>$attributeId, 'dataState'=>1], $dataType)->count();
        }
        $this->setProductAttributeCount($attributeCount);
        return $this->getProductCount() + $this->getProductAttributeCount();
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