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

        $select->columns(array('id' => 'entity_id', 'sku' => 'productid','site' => 'website'));

        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $priceJoin = new Expression('p.entity_id = product.entity_id and p.attribute_id = 99');
        $quantityJoin = new Expression('q.entity_id = product.entity_id and q.attribute_id = 1');
        $statusJoin = new Expression('s.entity_id = product.entity_id and s.attribute_id = 273');
        $visibilityJoin = new Expression('v.entity_id = product.entity_id and v.attribute_id = 526');


        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'), Select::JOIN_LEFT);

        $select->join(array('p' => 'productattribute_decimal'), $priceJoin,array('price' => 'value'), Select::JOIN_LEFT);

        $select->join(array('q' => 'productattribute_int'), $quantityJoin,array('quantity' => 'value'), Select::JOIN_LEFT);

        $select->join(array('s' => 'productattribute_int'), $statusJoin,array('status' => 'value'), Select::JOIN_LEFT);

        $select->join(array('v' => 'productattribute_int'), $visibilityJoin,array('visibility' => 'value'), Select::JOIN_LEFT);


        $filter = new Where();

        $filter->like('product.productid', $sku.'%');
        $filter->orPredicate(new Predicate\Like('t.value','%'.$sku.'%'));
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