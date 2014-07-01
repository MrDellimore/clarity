<?php


namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;



class FormTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
    }

    /**
     * @param $sku
     * @return boolean
     */
    public function validateSku($sku){
        if( isset($sku) ){
//            throw new \Exception('Form id does not exist');
        }
    }

    /**
     * @return Form
     */

    public function lookupData(){

    }
}