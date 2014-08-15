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
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareTrait;
use Search\Tables\Spex;

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
        $columns = array(
            'attId' =>  'attribute_id',
            'dataType'  =>  'backend_type',
        );
        $where = array(
             'attribute_code'=> $params['property'] == 'title' ? 'name' : $params['property']
        );
        $selectResult = $this->productAttribute($this->sql,$columns, $where, 'lookup');

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
        $this->getEventManager()->trigger('constructLog', null, array('makeFields'=>$eventWritables));
         $set = array(
                'dataState'=>1,
                'lastModifiedDate'=>date('Y-m-d h:i:s'),
                'changedby'=>$params['user'],
                'value'=>$params['old'],
            );
        $where = array('attribute_id'=>$attributeId, 'entity_id'=>$params['eid']);
        $this->productUpdateaAttributes($this->sql, $tableType, $set, $where);
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

    public function lookupLoggingInfo($searchParams = array())
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
//        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
//        $select->join(array('t' => 'productattribute_varchar'), $titleJoin ,array('title' => 'value'));

//        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $intTable = new Expression('i.entity_id = logger.entity_id and attribute_id = 102');
        $optionTable = new Expression('o.attribute_id = 102 and o.option_id = i.value');

        $select->join(array('i' => 'productattribute_int'), $intTable ,array('attributeId' => 'attribute_id','optionID' => 'value'));
        $select->join(array('o' => 'productattribute_option'), $optionTable ,array('manufacturer'=>'value'));
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


            $response[$key]['id'] = $logs[$key]['id'];
            $response[$key]['sku'] = $logs[$key]['sku'];
            $response[$key]['entityID'] = $logs[$key]['entityID'];
            $response[$key]['oldValue'] = $logs[$key]['oldValue'];
            $response[$key]['newValue'] = $logs[$key]['newValue'];
            $response[$key]['manufacturer'] = $manufacturer;

//            $response[$key]['manufacturer'] = $logs[$key]['manufacturer'];
            $response[$key]['dataChanged'] = $logs[$key]['dataChanged'];
            $response[$key]['property'] = $logs[$key]['property'];

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