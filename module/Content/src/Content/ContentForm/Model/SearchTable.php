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
    public function skulookup(array $options = array()){
        $sku = '';
        $limit = 1;
        $status = '';
        $mfc = '';
        $website = '';
        $quantity_min = 0;
        $quantity_max = 999999999;

        $options += [
            'sku' => '',
            'limit' => 10,
            'status' => '',
            'mfc' => '',
            'website' => '',
            'quantity_min' => 0,
            'quantity_max' => 99999999999
        ];
        extract($options, EXTR_IF_EXISTS);

        if ($quantity_min === "") {
            $quantity_min = 0;
        }
        if ($quantity_max === "") {
            $quantity_max = 999999999;
        }

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('product');

        $select->columns(array('id' => 'entity_id', 'sku' => 'productid',
            'site' => 'website','price' => 'price','status' => 'status','quantity'=>'quantity'));

        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,
            array('title' => 'value'), Select::JOIN_LEFT
        );

        $visibilityJoin = new Expression('v.entity_id = product.entity_id and v.attribute_id = 526');
        $select->join(array('v' => 'productattribute_int'), $visibilityJoin,
            array('visibility' => 'value'), Select::JOIN_LEFT
        );

        $producttable = new ProductsTable($this->adapter);

        $where = $select->where;
        if (!($producttable->validateSku($sku))) {
            $where->NEST
                ->like('product.productid', "{$sku}%")
                ->OR->like('t.value', "%{$sku}%")
                ->UNNEST;
        }
        else {
			$where->equalTo('product.productid', $sku);
        }

        $where->and->NEST
            ->lessThanOrEqualTo('product.quantity', $quantity_max)
            ->and->greaterThanOrEqualTo('product.quantity', $quantity_min)
            ->UNNEST;

        if ($website && is_numeric($website)) {
            $where->and->equalTo('product.website', $website);
        }

        // status
        if ($status !== '') {
            $where->and->equalTo('product.status', $status);
        }
        // mfc
        if ($mfc) {
            $mfcJoin = new Expression('mfc.entity_id = product.entity_id and mfc.attribute_id = 102');
            $select->join(array('mfc' => 'productattribute_int'), $mfcJoin,
                array('mfc_id' => 'value'), Select::JOIN_LEFT
            );

            $where->and->equalTo('mfc.value', $mfc);
        }

        $select->limit((int)$limit);

        $statement = $sql->getSqlStringForSqlObject($select);
        // var_dump($statement); exit();
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