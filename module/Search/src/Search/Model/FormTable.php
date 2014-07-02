<?php

namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;

class FormTable{

    protected $sku;

    protected $select;

    protected $sql;

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function executeQuery(){
        $statement = $this->sql->prepareStatementForSqlObject($this->select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet;
    }

    public function isSkuValid(ResultSet $result){
        if(!$result->valid()){
            return False;
        }
        return true;
    }

    public function selectQuery($sku){
        $this->select = $this->sql->select();
        $this->select->from('product')
            ->where(
                array(
                    'productid' => $sku
                )
            );
        return $this->select;

    }

    /**
     * @param $sku
     * @throws \Exception
     * @return int
     */
    public function validateSku($sku){
        $this->selectQuery($sku);
        $resultSet = $this->executeQuery();
        if( !$this->isSkuValid($resultSet) ){
            return false;
        }
        $skuList = array();
        $skuList = $resultSet->current();
        return $skuList['entity_id'];
    }

    public function joinTables($entityID){
        return new Expression("t.entity_id = $entityID and t.attribute_id = 96");
    }


    /**
     * @return Form
     */

    public function lookupData($entityID = null, $sku){
        $this->selectQuery($sku)
                ->join(
                array(
                    't' => 'productattribute_varchar'),
                    $this->joinTables($entityID),
                    array(
                        'title' => 'value'
                    )
            );
        $data = array();
        $data = $this->executeQuery()->current();
        return $data['title'];

    }
}