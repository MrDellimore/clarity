<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/12/14
 * Time: 12:47 PM
 */

namespace Content\ContentForm\Tables;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Where;

/**
 * I created this trait because I felt like I copying the same kind of code everywhere.
 * Class Spex
 * @package Content\ContentForm\Tables
 */

trait Spex {

    /**
     * I made this method to make it reusable. I felt like I was copying copious amounts of code constantly.
     * It queries attributes table with specific columns with or w/o a where clause.
     * @param Sql $sql
     * @param array $columns
     * @param null $where
     * @param $tableType
     * @param null $filter
     * @param array $joins
     * @return ResultSet
     */
    public function productAttribute(Sql $sql, array $columns = array(), $where = null,  $tableType, $filter = null, array $joins = array() )
    {
        $select = $sql->select();
        if(count($columns)) {
            $select->columns($columns);
        }
        if($joinTables = count($joins)) {
            for($i = 0; $i < $joinTables; $i++){
                $alias = $joins[$i][0];
                $on = $joins[$i][1];
                $cols = $joins[$i][2];
                $select->join($alias, $on, $cols);
            }
        }
        $select->from('productattribute_'. $tableType);
        if( $filter instanceof Where ) {
            $filter->notEqualTo($where['left'],$where['right']);
            $select->where($filter);
        } else {
            $select->where($where);
        }
        $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType.'.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'), Select::JOIN_LEFT);
//      This piece of code is for only selecting distinct rows. But I commented it out. Might be useful later one on certain conditions.
//        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet;

    }

    /**
     * This is another method that is just a look-up table to the productattribute_look up table. I felt like i was copying this code constantly so I made it a method in this trait.
     * @param Sql $sql
     * @param null $where
     * @return array
     */
    public function productAttributeLookup(Sql $sql, $where = null)
    {
        $select = $sql->select();
        $select->from('productattribute_lookup');
//        $select->columns(['attId'=>'attribute_id','dataType'=>'backend_type','attCode'=>'attribute_code']);
        $select->columns(['attId'=>'attribute_id','dataType'=>'backend_type','attCode'=>'attribute_code', 'frontend'=>'frontend_label', 'dateModified'=>'lastModifiedDate']);
        if(count($where)){
            $select->where($where);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->toArray();
    }

    /**
     * I don't think I'm using this method anymore but it updates a specic attribute table with certain attribute_ids and entity_ids.
     * @param Sql $sql
     * @param $tableType
     * @param array $set
     * @param array $where
     * @return ResultSet
     */
    public function productUpdateaAttributes(Sql $sql, $tableType, array $set = array(), array $where = array())
    {
        $update = $sql->update('productattribute_'.$tableType);
        $update->set($set);
        $update->where($where);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet;
    }
} 