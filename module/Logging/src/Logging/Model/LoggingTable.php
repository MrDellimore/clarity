<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/7/14
 * Time: 4:22 PM
 */

namespace Logging\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;

class LoggingTable {

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function lookupLoggingInfo($searchParams = array())
    {
        $select = $this->sql->select();
        $select->from('logger');
        $select->columns(array(
            'entityID'  =>  'entity_id',
            'oldValue'  =>  'oldvalue',
            'newValue'  =>  'newvalue',
            'dataChanged'   =>  'datechanged',
            'user'  =>  'changedby',
            'property'  =>  'property',
        ));
//        var_dump($sku);
        if( isset($searchParams['sku']) || isset($searchParams['from']) || isset($searchParams['to']) ) {
            $entityId = new Expression('p.entity_id = logger.entity_id');
            $select->join(array('p' => 'product'), $entityId ,array('entityID' => 'entity_id'));
            $filter = new Where();
            if( isset($searchParams['sku']) ) {
                $filter->like('p.productid', $searchParams['sku'] . '%');
            }
            if( isset($searchParams['from']) ) {
                $filter->between('logger.datachanged',$searchParams['from'], $searchParams['to']);
            }
            $select->where($filter);
        }
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();
//        die();
        return $result;
    }

} 