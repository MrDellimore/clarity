<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 10/1/14
 * Time: 3:09 PM
 */



namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;
use Content\ContentForm\Entity\Accessories;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;

class AccessoryTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }


    public function addAccessory(Accessories $accessory){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $insert = $this->sql->insert('productlink');
        $insert->columns(array('link_id','entity_id','linked_entity_id','link_type_id','dataState','changedby'));
        $insert->values(array(
            'link_id' => $accessory->getId(),
            'entity_id' => $accessory->getEntityid(),
            'linked_entity_id' => $accessory->getLinkedSku(),
            'link_type_id' => 1,
            'dataState' => 2,
            'changedby' => $user
        ));
        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }

    public function updateAccessory(Accessories $accessory){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();

        $setArray['position'] = $accessory->getPosition();
        $setArray['datastate'] = 1;
        $setArray['changedby'] = $user;

        $update = $this->sql->update('productlink');
        $update->set($setArray);
        $update->where(array('link_id' => $accessory->getId() ));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }


    public function removeAccessory(Accessories $accessory){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();

        $setArray['datastate'] = 3;
        $setArray['changedby'] = $user;

        $update = $this->sql->update('productlink');
        $update->set($setArray);
        $update->where(array('link_id' => $accessory->getId() ));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

}