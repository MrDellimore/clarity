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

    public function lookupLoggingInfo($searchParams = array(), $moreOld = Null, $old = Null)
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

            /*Might use ellipses later on if requested*/
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
//            $response[$key]['oldValue'] = $shortOldValue;
            $response[$key]['oldValue']         = strip_tags($oldValue);
//            $response[$key]['newValue'] = $history['newValue'];
//            $response[$key]['newValue'] = $shortNewValue;
            $response[$key]['newValue']         = strip_tags($newValue);
            $response[$key]['manufacturer']     = $manufacturer;


            $response[$key]['dataChanged']      = date('m-d-Y',strtotime($history['dataChanged']));
            $response[$key]['property']         = ucfirst($history['property']);
            $response[$key]['user']             = $history['firstname']. ' ' . $history['lastname'];

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
        }
        return $response;
    }

} 