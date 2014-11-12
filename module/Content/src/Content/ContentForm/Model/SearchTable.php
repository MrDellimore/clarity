<?php


namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate;


class SearchTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
    }

    //quicksearch
    public function skulookup($sku,$l){

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('product');

        $select->columns(array('id' => 'entity_id', 'sku' => 'productid','site' => 'website','price' => 'price','status' => 'status','quantity'=>'quantity'));

        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $visibilityJoin = new Expression('v.entity_id = product.entity_id and v.attribute_id = 526');

        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'), Select::JOIN_LEFT);
        $select->join(array('v' => 'productattribute_int'), $visibilityJoin,array('visibility' => 'value'), Select::JOIN_LEFT);

        $producttable = new ProductsTable($this->adapter);
        $filter = new Where();

        if(!($producttable->validateSku($sku))){
            $filter->like('product.productid', $sku.'%');
            $filter->orPredicate(new Predicate\Like('t.value','%'.$sku.'%'));
            $select->where($filter);
        }
        else{
            $select->where(['product.productid' => $sku]);
        }



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