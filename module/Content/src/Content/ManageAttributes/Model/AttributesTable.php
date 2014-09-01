<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/29/14
 * Time: 11:17 AM
 */

namespace Content\ManageAttributes\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;

class AttributesTable {

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
    public function fetchAttributes($attributeCode = null)
    {
        $select = $this->_sql->select();
        $select->from('productattribute_lookup');
        $select->columns(['attId'=>'attribute_id','dataType'=>'backend_type','frontend'=>'frontend_label', 'input'=>'frontend_input', 'dateModified'=>'lastModifiedDate','user'=>'changedby']);
        $filter = new Where();
        $filter->like('productattribute_lookup.frontend_label', $attributeCode.'%');
        $select->where($filter);

        $select->join(['u'=>'users'], 'u.userid = productattribute_lookup.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);
        $statement = $this->_sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $attributes = $resultSet->toArray();
        $atts = [];
        foreach( $attributes as $key => $attribute ) {
            $atts[$key]['attId'] = $attribute['attId'];
            $atts[$key]['dataType'] = $attribute['dataType'];
            $atts[$key]['frontend'] = $attribute['frontend'];
            $atts[$key]['input'] = $attribute['input'];
            $atts[$key]['dateModified'] = $attribute['dateModified'];
            if( isset($attribute['fname'])) {
                $atts[$key]['fullname'] = $attribute['fname']. ' ' . $attribute['lname'];
            } else {
                $atts[$key]['fullname'] = 'N/A';
            }
        }
        return $atts;
    }
}