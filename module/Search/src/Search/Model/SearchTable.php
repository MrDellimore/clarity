<?php


namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;



class SearchTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
    }

//grab first 10 Skus to display in table
    public function populate(){

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('product');

        $select->columns(array('id' => 'entity_id', 'sku' => 'productid'));

        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $priceJoin = new Expression('p.entity_id = product.entity_id and p.attribute_id = 99');
        $quantityJoin = new Expression('q.entity_id = product.entity_id and q.attribute_id = 1');
        $statusJoin = new Expression('s.entity_id = product.entity_id and s.attribute_id = 273');


        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));

        $select->join(array('p' => 'productattribute_decimal'), $priceJoin,array('price' => 'value'));

        $select->join(array('q' => 'productattribute_int'), $quantityJoin,array('quantity' => 'value'));

        $select->join(array('s' => 'productattribute_int'), $statusJoin,array('status' => 'value'));

        $select->limit(500);
        /*
            ->join(array('price' => 'productattribute_decimal'),
                'price.entity_id = p.entity_id');
        */

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//echo '<pre>';
//var_dump($resultSet->toArray());
        return $resultSet->toArray();

    }

    //Use this to search new skus
        //**remember to keep light for ajax**

    public function lookup($searchTerm){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(array('f' => 'foo'))
        ->join(array('t' => 'productattribute_varchar'),
                't.entity_id = p.entity_id')
        ->join(array('price' => 'productattribute_decimal'),
                'price.entity_id = p.entity_id');

        $where = new  Where();
        $where-> like ('entity_id', $searchTerm);
        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;

    }


} 