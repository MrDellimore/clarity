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
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;

class OptionTable {

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var \Zend\Db\Sql\Sql
     */
    protected $_sql;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->_sql = new Sql($this->adapter);
    }

    /**
     * Fetches all of the options from the productattribute options table
     * @param null $optionValue
     * @param $attributeId
     * @return array $opt
     */
    public function fetchOptions($optionValue = Null, $attributeId)
    {
        $select = $this->_sql->select();
        $select->from('productattribute_option');
        $select->columns(['attId'=>'attribute_id','options'=>'value', 'dateModified'=>'lastModifiedDate','user'=>'changedby']);
        $filter = new Where();
        if(!is_null($optionValue)){
            $filter->like('productattribute_option.value', $optionValue.'%');
        }
        $filter->equalTo('attribute_id', $attributeId);
        $select->where($filter);
        $select->join(['u'=>'users'], 'u.userid = productattribute_option.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);//
        $statement = $this->_sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }

        $options = $resultSet->toArray();
//        This comment below will echo out the above query so you can better see what your query looks like in the developer tools of your browser
//        $mysql = new Mysql(new \PDO($this->adapter));
//        echo $select->getSqlString($mysql);

        $opt = [];
        foreach( $options as $key => $option ) {
            $opt[$key]['options'] = $option['options'];
            $opt[$key]['dateModified'] = $option['dateModified'];
            if( isset($option['fname'])) {
                $opt[$key]['fullname'] = $option['fname'] . ' ' . $option['lname'];
            } else {
                $opt[$key]['fullname'] = 'N/A';
            }
            $opt[$key]['attId'] = $option['attId'];
        }
        return $opt;
    }
}