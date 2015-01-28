<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 12/11/14
 * Time: 1:29 PM
 */


namespace Authenticate\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Sql;


class ActiveTable{

    protected $sql;

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function fetchStash($sku){
        $insert = $this->sql->select('activeusers');
        $insert->columns(array('userid','currentpage','productid'));
        $insert->where(array('productid' => $sku));

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }

    public function stashActiveUser($userid,$resource,$sku){
        $insert = $this->sql->insert('activeusers');
        $insert->columns(array('userid','currentpage','productid'));
        $insert->values(array(
            'userid' => $userid,
            'currentpage' => $resource,
            'productid' => $sku));

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }

    public function unstashActiveUser($userid,$sku){
        $delete = $this->sql->delete('activeusers');
        $delete->where(array('userid' =>$userid,'productid' => $sku));

        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $statement->execute();
    }
}