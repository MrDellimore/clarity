<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/19/14
 * Time: 5:25 PM
 */

namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;
use Content\ContentForm\Entity\Category;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;

class CategoryTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /*
     * todo make select statement to check if category assignment is there
     *
     */
    public function checkCategory(Category $cat,$entityid){
        $select = $this->sql->select();
        $select->from('productcategory');
        $select->columns(array('category_id'));
        $select->where(array('entity_id'=> $entityid,'category_id' => $cat->getId()));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }

        return $resultSet->valid();
    }

    public function addCategory(Category $cat,$entityid){
        if($this->checkCategory($cat, $entityid)){
            $this->updateCategory($cat,$entityid);
        }

        else{
            $loginSession= new Container('login');
            $userData = $loginSession->sessionDataforUser;
            $user = $userData['userid'];

            $insert = $this->sql->insert('productcategory');
            $insert->columns(array('entity_id','category_id','dataState','changedby'));
            $insert->values(array(
                'entity_id' => $entityid,
                'category_id' => $cat->getId(),
                'dataState' => 2,
                'changedby' => $user
            ));

            $statement = $this->sql->prepareStatementForSqlObject($insert);

            $statement->execute();
        }


        return $entityid ." Category(s) added </br>";
    }

    public function updateCategory(Category $cat,$entityid){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();
        $message = '';

        //$setArray['category_id'] = $cat->getId();
        $setArray['datastate'] = 2;
        $setArray['changedby'] = $user;

        $update = $this->sql->update('productcategory');
        $update->set($setArray);
        $update->where(array('entity_id' => $entityid, 'category_id' => $cat->getId() ));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();

        $message .= $entityid ." Categories updated";

        return $message;
    }


    public function removeCategory(Category $cat,$entityid){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();
        $message = '';

        //$setArray['category_id'] = $cat->getId();
        $setArray['datastate'] = 3;
        $setArray['changedby'] = $user;

        $update = $this->sql->update('productcategory');
        $update->set($setArray);
        $update->where(array('entity_id' => $entityid, 'category_id' => $cat->getId() ));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();

        $message .= $entityid ." Categories unset";

        return $message;
    }



}