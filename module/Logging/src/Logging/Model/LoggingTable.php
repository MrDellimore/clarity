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

    public function listUser()
    {
        $select = $this->sql->select();
        $select->from('users');
        $select->columns(array(
            'userId'    =>  'userid',
            'firstName'  =>  'firstname',
            'lastName'  =>  'lastname',
        ));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();
        return $result;

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
            'manufacturer'  =>  'manufacturer',
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
        $logs = $resultSet->toArray();

        $response = array();

        foreach($logs as $key => $value){
            $user = $logs[$key]['user'];
            $manufacturer = $logs[$key]['manufacturer'];
//            $entityId = $logs[$key]['entityID'];

            $response[$key]['entityID'] = $logs[$key]['entityID'];
            $response[$key]['oldValue'] = $logs[$key]['oldValue'];
            $response[$key]['newValue'] = $logs[$key]['newValue'];
            $response[$key]['manufacturer'] = $logs[$key]['manufacturer'];
            $response[$key]['dataChanged'] = $logs[$key]['dataChanged'];
            $response[$key]['property'] = $logs[$key]['property'];

            //Selects from options table;
            $selectMan= $this->sql->select();
            $selectMan->from('productattribute_option');
            $selectMan->columns(array(
                'manufacturer' => 'value',
            ));
            $selectMan->where(array('option_id'=>$manufacturer));
            $manufacturerStatement = $this->sql->prepareStatementForSqlObject($selectMan);
            $manufacturerResult = $manufacturerStatement->execute();

            $manufacturerResultSet = new ResultSet;
            if ($manufacturerResult instanceof ResultInterface && $manufacturerResult->isQueryResult()) {
                $manufacturerResultSet->initialize($manufacturerResult);
            }
            $manufacturerResults = $manufacturerResultSet->toArray();
            if( !count($manufacturerResults) ) $response[$key]['manufacturer'] = 'N/A';
            foreach($manufacturerResults  as $op => $man){
                $response[$key]['manufacturer'] = (!$manufacturerResults[$op]['manufacturer']) ? "N/A" : $manufacturerResults[$op]['manufacturer'] ;
            }

            //Selects from users table;
            $userListings = $this->sql->select();
            $userListings->from('users');
            $userListings->columns(array(
                'firstName' => 'firstname',
                'lastName' =>   'lastname',
            ));
            $userListings->where(array('userid'=>$user));
            $userStatement = $this->sql->prepareStatementForSqlObject($userListings);
            $userResult = $userStatement->execute();

            $userResultSet = new ResultSet;

            if ($userResult instanceof ResultInterface && $userResult->isQueryResult()) {
                $userResultSet->initialize($userResult);
            }
            $users = $userResultSet->toArray();
            foreach($users as $index => $person){
                $response[$key]['user'] = $users[$index]['firstName'] . ' ' . $users[$index]['lastName'];
            }
        }
        return $response;
    }

} 