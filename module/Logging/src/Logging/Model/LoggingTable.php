<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/7/14
 * Time: 4:22 PM
 */

namespace Logging\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareTrait;
use Content\ContentForm\Tables\Spex;

class LoggingTable
{
    use EventManagerAwareTrait, Spex;

    protected $adapter;

    protected $sql;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }


    /**
     * Description: this method revert any changes that had taken place in the logging history.
     * @params array();
     */
    public function undo($params = array())
    {
        $selectResult = $this->productAttributeLookup($this->sql,['attribute_code'=> $params['property'] == 'title' ? 'name' : $params['property']]);
        $attributeId = $selectResult[0]['attId'];
        $tableType  = $selectResult[0]['dataType'];
        $columnMap = array(
            'entity_id' =>  $params['eid'],
            'sku' =>  $params['sku'],
            'oldvalue'  =>  $params['new'],
            'newvalue'  =>  $params['old'],
            'datechanged'   => date('Y-m-d h:i:s'),
            'changedby' =>  $params['user'],
            'property'  =>  $params['property'],
        );

        $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $columnMap);//'fields' => $mapping,
        $this->getEventManager()->trigger('construct_sku_log', null, array('makeFields'=>$eventWritables));
         $set = array(
                'dataState'=>1,
                'lastModifiedDate'=>date('Y-m-d h:i:s'),
                'changedby'=>$params['user'],
                'value'=>$params['old'],
            );
        $where = array('attribute_id'=>$attributeId, 'entity_id'=>$params['eid']);
        $update = $this->sql->update('productattribute_' . $tableType)->set($set)->where($where);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Description: this method will return a list of all rows in logger table.
     * @return array|\Zend\Db\Adapter\Driver\ResultInterface $result array()
     */
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

    /**
     * Description: this method will use the search sku input box as a basis to return only those rows with the sku.
     * @param $searchParam array();
     * @return $response array()
     */

    public function lookupLoggingInfo($searchParams = array(), $limit)
    {
        $select = $this->sql->select();
        $select->from('logger');
        $select->columns(array(
            'id'  =>  'id',
            'entityID'  =>  'entity_id',
            'sku'   =>  'sku',
            'oldValue'  =>  'oldvalue',
            'newValue'  =>  'newvalue',
            'dataChanged'   =>  'datechanged',
//            'manufacturer'  =>  'manufacturer',
            'user'  =>  'changedby',
            'property'  =>  'property',
        ));
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

        $intTable = new Expression('i.entity_id = logger.entity_id and attribute_id = 102');
        $optionTable = new Expression('o.attribute_id = 102 and o.option_id = i.value');
        $select->join(['u'=>'users'], 'logger.changedby=u.userid',['fname'=>'firstname','lname'=>'lastname']);
        $select->join(array('i' => 'productattribute_int'), $intTable ,array('attributeId' => 'attribute_id','optionID' => 'value'), Select::JOIN_LEFT);
        $select->join(array('o' => 'productattribute_option'), $optionTable ,array('manufacturer'=>'value'), Select::JOIN_LEFT);
        $select->limit((int)$limit);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $logs = $resultSet->toArray();

        $response = array();

        foreach( $logs as $key => $fields ) {
//            $user = $logs[$key]['user'];
//            $manufacturer = $response['manufacturer'];
            $response[$key]['id'] = $fields['id'];
            $response[$key]['entityID'] = $fields['entityID'];
            $response[$key]['sku'] = $fields['sku'];
            $response[$key]['oldValue'] = $fields['oldValue'];
            $response[$key]['newValue'] = $fields['newValue'];
            $response[$key]['manufacturer'] = $fields['manufacturer'];//$manufacturer;
            $response[$key]['user'] = $fields['fname'] . ' ' . $fields['lname'];
            $response[$key]['dataChanged'] = $fields['dataChanged'];
            $response[$key]['property'] = $fields['property'];

            //Selects from options table;
//            $selectMan= $this->sql->select();
//            $selectMan->from('productattribute_option');
//            $selectMan->columns(array(
//                'manufacturer' => 'value',
//            ));
//            $selectMan->where(array('option_id'=>$manufacturer));
//            $manufacturerStatement = $this->sql->prepareStatementForSqlObject($selectMan);
//            $manufacturerResult = $manufacturerStatement->execute();
//
//            $manufacturerResultSet = new ResultSet;
//            if ($manufacturerResult instanceof ResultInterface && $manufacturerResult->isQueryResult()) {
//                $manufacturerResultSet->initialize($manufacturerResult);
//            }
//            $manufacturerResults = $manufacturerResultSet->toArray();
//            if( !count($manufacturerResults) ) $response[$key]['manufacturer'] = 'N/A';
//            foreach($manufacturerResults  as $op => $man){
//                $response[$key]['manufacturer'] = (!$manufacturerResults[$op]['manufacturer']) ? "N/A" : $manufacturerResults[$op]['manufacturer'] ;
//            }

            //Selects from users table;
//            $userListings = $this->sql->select();
//            $userListings->from('users');
//            $userListings->columns(array(
//                'firstName' => 'firstname',
//                'lastName' =>   'lastname',
//            ));
//            $userListings->where(array('userid'=>$user));
//            $userStatement = $this->sql->prepareStatementForSqlObject($userListings);
//            $userResult = $userStatement->execute();
//
//            $userResultSet = new ResultSet;
//
//            if ($userResult instanceof ResultInterface && $userResult->isQueryResult()) {
//                $userResultSet->initialize($userResult);
//            }
//            $users = $userResultSet->toArray();
//            foreach($users as $index => $person){
//                $response[$key]['user'] = $users[$index]['firstName'] . ' ' . $users[$index]['lastName'];
//            }
        }
        return $response;
    }

    public function fetchMageLogs($search, $limit)
    {
        $select = $this->sql->select();
        $select->from('mage_logs');
        $select->columns(array(
//            'id'  =>  'id',
            'sku' => 'sku',
            'resource'   =>  'resource',
            'speed'  =>  'speed',
            'pushedby'  =>  'pushedby',
            'datepushed'   =>  'datepushed',
            'status'    =>  'status',
        ));
        if( isset($search['sku']) || isset($search['from']) || isset($search['to']) ) {
            $filter = new Where();
            if( isset($search['sku']) ) {
                $filter->like('sku', $search['sku'] . '%');
            }
            if( isset($searchParams['from']) ) {
                $filter->between('mage_logs.datachanged',$searchParams['from'], $searchParams['to']);
            }
            $select->where($filter);
        }
        $select->join(['u'=>'users'], 'mage_logs.pushedby=u.userid',['fname'=>'firstname','lname'=>'lastname']);
        $select->limit((int)$limit);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $logs = $resultSet->toArray();

        $response = array();

        foreach($logs as $key => $fields) {
//            $user = $logs[$key]['user'];
//            $response[$key]['id'] = $fields['id'];
            $response[$key]['sku'] = $fields['sku'];
            $response[$key]['resource'] = $fields['resource'];
            $response[$key]['speed'] = $fields['speed']. ' secs';
            $response[$key]['fullname'] = $fields['fname'] . ' ' . $fields['lname'];
            $response[$key]['datepushed'] = date('m-j-Y',strtotime($fields['datepushed']));
            $response[$key]['status'] = $fields['status'];

        }
        return $response;
    }

} 