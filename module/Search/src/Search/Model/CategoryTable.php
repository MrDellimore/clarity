<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/19/14
 * Time: 5:25 PM
 */

namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;
use Search\Entity\Category;

class CategoryTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }


    public function addCategory(Category $cat,$entityid){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $insert = $this->sql->insert('productcategory');
        $insert->columns(array('entity_id','category_id','dataState','changedby'));
        $insert->values(array(
            'entity_id' => $entityid,
            'category_id' => $cat->getCategoryid(),
            'dataState' => 2,
            'changedby' => $user
        ));

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return $entityid ." Category(s) added </br>";
    }


    public function removeCategory(Category $cat,$entityid){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();
        $message = '';

        $setArray['category_id'] = $cat->getCategoryid();
        $setArray['datastate'] = 3;
        $setArray['changedby'] = $user;

        $update = $this->sql->update('productcategory');
        $update->set($setArray);
        $update->where(array('entity_id' => $entityid ));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();

        $message .= $entityid ." Categories unset";


        return $message;
    }

}