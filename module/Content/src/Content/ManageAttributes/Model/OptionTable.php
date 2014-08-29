<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/29/14
 * Time: 11:22 AM
 */

namespace Content\ManageAttributes\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;


class OptionTable {

    protected $adapter;

    protected $_sql;

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->_sql = new Sql($this->adapter);
    }

    public function fetchOptions($optionValue, $attributeId)
    {
        $select = $this->_sql->select();
        $select->from('productattribute_option');
        $select->columns(['options'=>'value', 'dateModified'=>'lastModifiedDate','user'=>'changedby']);
        $filter = new Where();
        $filter->like('productattribute_option.value', $optionValue.'%');
        $filter->equalTo('attribute_id', $attributeId);
        $select->where($filter);
//        $select->join(['u'=>'users'], 'u.userid = productattribute_lookup.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);
        $statement = $this->_sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $options = $resultSet->toArray();

        return $options;
    }
}