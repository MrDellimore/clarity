<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/29/14
 * Time: 11:17 AM
 */

namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;
//use Content\ContentForm\Tables\Spex;

class AttributesTable {

//    use Spex;

    protected $_adapter;

    protected $_sql;

    public function __construct(Adapter $adapter){
        $this->_adapter = $adapter;
        $this->_sql = new Sql($this->_adapter);
    }

    /**
     * Description: This method accesses everything from lookup table and displays it in the front end.
     * @return array
     */
    public function fetchLookupTable()
    {
        $select = $this->_sql->select();
        $select->from('productattribute_lookup');
//        $select->columns(['dataType'=>'backend_type','frontend'=>'frontend_label', 'dateModified'=>'lastModifiedDate','user'=>'changedby','input'=>'frontend_input']);
//        $select->join(['u'=>'users'], 'u.userid = productattribute_lookup.changedby',['fname'=>'firstname','lname'=>'lastname']);
        $statement = $this->_sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $attributes = $resultSet->toArray();
        echo '<pre>';
        var_dump($attributes);
        foreach($attributes as $key => $attribute){
            echo $attribute['input']. ' ' ;
        }
        die();
//        return $this->productAttributeLookup($this->_sql);
    }



    //quicksearch
    public function skulookup($sku,$l){

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('product');

        $select->columns(array('id' => 'entity_id', 'sku' => 'productid','site' => 'website'));

        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $priceJoin = new Expression('p.entity_id = product.entity_id and p.attribute_id = 99');
        $quantityJoin = new Expression('q.entity_id = product.entity_id and q.attribute_id = 1');
        $statusJoin = new Expression('s.entity_id = product.entity_id and s.attribute_id = 273');
        $visibilityJoin = new Expression('v.entity_id = product.entity_id and v.attribute_id = 526');


        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));

        $select->join(array('p' => 'productattribute_decimal'), $priceJoin,array('price' => 'value'));

        $select->join(array('q' => 'productattribute_int'), $quantityJoin,array('quantity' => 'value'));

        $select->join(array('s' => 'productattribute_int'), $statusJoin,array('status' => 'value'));

        $select->join(array('v' => 'productattribute_int'), $visibilityJoin,array('visibility' => 'value'));


        $filter = new Where();
        $filter->like('product.productid', $sku.'%');
        $select->where($filter);

        $l = (int) $l;
        $select->limit($l);




        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }

        //return $select->getSqlString();
        return $resultSet->toArray();

    }







} 