<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/20/14
 * Time: 12:48 PM
 */

namespace Content\WebAssignment\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;



class WebAssignTable
{
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function accessWeb($searchTerm)
    {
        $select = $this->sql->select();
        $select->from('webassignment');
        $concatName = new Expression("concat(u.firstname, ' ',u.lastname)");

        $select->columns(array('Manufacturer'=>'manufacturer','Site'=>'website','Date Assigned'=>'lastModifiedDate','Edit'=>'id'));
        $select->join(array('u'=>'users'), 'webassignment.changedby=u.userid',array('Changed by'=>$concatName), Select::JOIN_LEFT);
        $filter = new Where();
        $filter->like('manufacturer', $searchTerm.'%');
        $select->where($filter);
        $select->limit(10);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->toArray();
    }

    public function updateWebsiteTable($manufacturer, $website, $userid)
    {

        //What is the purpose of this first select statement?
        //validation?  if so this can be done client side instead of a new DB call
        /*
        $select = $this->sql->select();
        $select->from('webassignment')->columns(array('website'=>'website'))->where(array('manufacturer'=>$manufacturer));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $webResults = $resultSet->toArray();
        $site = $webResults[0]['website'];
        if($site == $website){
            return '';
        }
        */

        $update = $this->sql->update('webassignment');
        $update->set(array('website'=>$website,'changedby'=>$userid, 'dataState'=>1))->where(array('manufacturer'=>$manufacturer));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        if($website == 0 ) $site = 'aSavings';
        if($website == 1 ) $site = 'Focus';
        if($website == 2 ) $site = 'Focus / aSavings';
        return "Successfully changed Manufacturer '". $manufacturer . "' to Website: '" . $site . "'";
    }
}