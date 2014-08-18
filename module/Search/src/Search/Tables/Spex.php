<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/12/14
 * Time: 12:47 PM
 */

namespace Search\Tables;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\ExpressionInterface;
use Zend\Db\Sql\Where;


trait Spex {

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
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet;

    }

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