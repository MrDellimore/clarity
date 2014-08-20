<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/20/14
 * Time: 12:48 PM
 */

namespace Search\WebAssignment\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;


class WebAssignTable
{
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function accessWeb()
    {
        $select = $this->sql->select();
        $select->from('webassignment');
        $select->join(array('u'=>'users'), 'webassignment.changedby=u.userid',array('fname'=>'firstname','lname'=>'lastname'), Select::JOIN_LEFT);
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
        $update = $this->sql->update('webassignment');
        $update->set(array('website'=>$website,'changedby'=>$userid))->where(array('manufacturer'=>$manufacturer));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        if($website == 0 ) $site = 'aSavings';
        if($website == 1 ) $site = 'Focus';
        if($website == 2 ) $site = 'Focus / aSavings';
        return 'Successfully changed '. $manufacturer . ' to ' . $site;
    }
}