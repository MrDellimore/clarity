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
use Zend\EventManager\EventManagerAwareTrait;
use Content\ContentForm\Tables\Spex;

class LoggingTable
{
    /**
     * Traits
     */
    use EventManagerAwareTrait, Spex;

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var Sql object
     */
    protected $sql;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }


    /**
     * Description: this method reverts any changes that had taken place in the logging history.
     * @param array $params
     * @return ResultInterface $result
     */
    public function undo($params = array())
    {
        $where = array(
             'attribute_code'=> (strtolower($params['property']) == 'title') ? 'name' : strtolower(str_replace(' ', '_',$params['property']))
        );
        $selectResult = $this->productAttributeLookup($this->sql,$where);

        $attributeId = $selectResult[0]['attId'];
        $tableType  = $selectResult[0]['dataType'];
        $columnMap = array(
            'entity_id'     =>  $params['eid'],
            'sku'           =>  $params['sku'],
            'oldvalue'      =>  $params['new'],
            'newvalue'      =>  $params['old'],
            'datechanged'   =>  date('Y-m-d h:i:s'),
            'changedby'     =>  $params['user'],
            'property'      =>  $params['property'],
        );

//        Refer to Module.php in Content module.
        $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $columnMap);
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
     * Description: this method will fetch logger table and dump it in the data table
     * It will also use the sku if provide to search the log table by sku.
     * @param array $searchParams
     * @param $limit
     * @param $moreOld | Null
     * @param $old | Null
     * @return array $response
     */
    public function lookupLoggingInfo($searchParams = array(), $limit, $moreOld = Null, $old = Null)
    {
        $select = $this->sql->select()->from('logger')->columns([
                                                                'id'            =>  'id',
                                                                'entityID'      =>  'entity_id',
                                                                'sku'           =>  'sku',
                                                                'oldValue'      =>  'oldvalue',
                                                                'newValue'      =>  'newvalue',
                                                                'dataChanged'   =>  'datechanged',
                                                                'user'          =>  'changedby',
                                                                'property'      =>  'property',
                                                            ]);
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
        $select->limit((int)$limit);
        $select->join(['u'=>'users'] , 'logger.changedby=u.userid',['firstname'=>'firstname','lastname'=>'lastname']);
        $select->join(array('i' => 'productattribute_int'), $intTable ,array('attributeId' => 'attribute_id','optionID' => 'value'), Select::JOIN_LEFT);
        $select->join(array('o' => 'productattribute_option'), $optionTable ,array('manufacturer'=>'value'), Select::JOIN_LEFT);
        $select->order('logger.datechanged DESC');
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $logs = $resultSet->toArray();

        $response = array();

        foreach($logs as $key => $history){
            $manufacturer = $history['manufacturer'];
            $oldValue = $history['oldValue'];
            $newValue = $history['newValue'];

//            Was asked to shorten fields that are too large.
//            Might use ellipses later on if requested
//            Used strip_tags instead on lines 184 and 187

//            if( (is_string($oldValue) && strlen($oldValue) > 50) || ( is_string($newValue) && strlen($newValue) > 50 ) && !is_null($old) ) {
//                $shortOldValue = utf8_encode(substr(strip_tags($oldValue),0,50)) . " <a href='#' class='more_old' id='more_old_sku" . $key . "'>...</a>";
//                $shortNewValue = utf8_encode(substr(strip_tags($newValue),0,50)) . " <a href='#' class='more_new' id='more_new_sku" . $key . "'>...</a>";;
//
//            } else {
//                $shortOldValue = $oldValue;
//                $shortNewValue = $newValue;
//            }

            $response[$key]['id']               = $history['id'];
            $response[$key]['sku']              = $history['sku'];
            $response[$key]['entityID']         = $history['entityID'];
            $response[$key]['oldValue']         = strip_tags($oldValue);
            $response[$key]['newValue']         = strip_tags($newValue);
            $response[$key]['manufacturer']     = $manufacturer;
            $response[$key]['dataChanged']      = date('m-d-Y',strtotime($history['dataChanged']));
            $response[$key]['property']         = ucfirst($history['property']);
            $response[$key]['user']             = $history['firstname']. ' ' . $history['lastname'];
        }
        return $response;
    }

    /**
     * Description: this method will fetch mage_logs table and dump it in the data table
     * It will also use the sku if provide to search the log table by sku.
     * @param $search
     * @param $limit
     * @return array $response
     */
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
        $select->order('mage_logs.datepushed DESC');
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $logs = $resultSet->toArray();

        $response = array();

        foreach($logs as $key => $fields) {
            $response[$key]['sku'] = $fields['sku'];
            $response[$key]['resource'] = $fields['resource'];
            $response[$key]['speed'] = $fields['speed']. ' secs';
            $response[$key]['fullname'] = $fields['fname'] . ' ' . $fields['lname'];
            $response[$key]['datepushed'] = date('m-j-Y H:i:s',strtotime($fields['datepushed']));
            $response[$key]['status'] = $fields['status'];
        }
        return $response;
    }

} 