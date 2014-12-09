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
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;


class WebAssignTable
{
    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected  $adapter;

    /**
     * @var \Zend\Db\Sql\Sql
     */
    protected $sql;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * Queries webassignment table for fields and uses data table non-server-side to display them.
     * @return array
     */
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

    /**
     * When user changes a manufacturer to a new site. This method will persist that change.
     * @param $manufacturer
     * @param $website
     * @param $userid
     * @return string
     */
    public function updateWebsiteTable($manufacturer, $website, $userid)
    {
        $update = $this->sql->update('webassignment');
        $update->set(array('website'=>$website,'changedby'=>$userid, 'dataState'=>1))->where(array('manufacturer'=>$manufacturer));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        if($website == 0 ) $site = 'aSavings';
        if($website == 1 ) $site = 'Focus';
        if($website == 2 ) $site = 'Focus / aSavings';
        return "Successfully changed Manufacturer Site'". $manufacturer . "<br />";
    }
}