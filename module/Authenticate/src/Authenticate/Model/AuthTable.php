<?php
namespace Authenticate\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Authenticate\Entity\User;
use Zend\Db\Sql\Sql;
use Zend\Crypt\Password\Bcrypt;
//use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as dbTable;

class AuthTable{
    
    protected $tableGateway;

    protected $sql;

    public function __construct(Adapter $adapter){
//        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);

    }

    public function storeUserSession($userSession){
        $loginSession= new Container('login');
        $userInfo = $userSession->current();
        $loginSession->sessionDataforUser = $userInfo;
//        var_dump($loginSession->sessionDataforUser);
//        die();

    }

    public function storeUser($userId)
    {
        $this->userId = $userId;
        $columns = array('userid','firstname', 'lastname', 'email', 'username', 'password', 'role', 'datecreated');
        $select = $this->sql->select('users');
        $select->columns($columns)
            ->where(array('userid'   =>  $userId));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $this->storeUserSession($resultSet);
    }

    public function selectUser($username)
    {
        $select = $this->sql->select()->from('users')->where(['username'=>$username]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->toArray();
    }

    public function encryptPassword($register)
    {
        $encrypt = new Bcrypt(['cost'=>12]);
        $hash = $encrypt->create($register['password']);
        $registerUser = [
            'firstname' =>  $register['firstname'],
            'lastname'  =>  $register['lastname'],
            'email'     =>  $register['email'],
            'role'      =>  $register['role'],
            'username'  =>  $register['username'],
            'password'  =>  $hash,
        ];
        return $registerUser;
    }


    public function saveUser(User $user){
        $select = $this->sql->select('users');
        $columns = array('firstname', 'lastname', 'email', 'username', 'password', 'role', 'datecreated');
        $select->columns($columns);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $resultSet->rewind();
        while($current = $resultSet->current()){
//            echo "<pre>";
//                var_dump($current);
            $resultSet->next();
            if($current['firstname'] == $user->getFirstName() || $current['lastname'] == $user->getLastName() ){
                return false;
            }
        }

//        for($i = 0; $i < $resultSet->count(); $i++){
//        die();

        $insert = $this->sql->insert('users');
        $data = array(
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'email'  => $user->getEmail(),
            'username'  => $user->getUsername(),
            'password'  => $user->getPassword(),
            'role'  => $user->getRole(),
            'datecreated'   => date('Y-m-d H:i:s')
        );

//        $columns = array(  'firstname' , 'lastname' ,  'email' , 'username'  , 'password'  , 'role' );
//        var_dump($data);
//        var_dump($columns);
//        var_dump(array_keys($data));
//        var_dump(array_values($data));
        $insert->columns(array_keys($data))
               ->values($data);
//        var_dump($insert->getSqlString());
        $statement = $this->sql->prepareStatementForSqlObject($insert);
        return $statement->execute();
//        var_dump($statement);
//        die();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $table = new TableGateway('users', $this->adapter);
//        var_dump($table->executeInsert($insert));
//        $fields = implode(',',array_keys($data));
//        $values = implode(',',array_values($data));
//        echo $fields;
//die();
//        foreach($data as $fields => $fieldValues){
//
//        }

//        $insertQuery = "INSERT INTO users ($fields) VALUES($values)";

//        $id = (int)$user->getId();
//        if($id == 0){
//            return $this->tableGateway->insert($data);
//            return $dbAdapter->query($insertQuery);die();
//        }
        
//        else{
//            if ($this->getUser($id)) {
//                $this->tableGateway->update($data, array('id' => $id));
//            }
//
//            else {
//                throw new \Exception('User ID does not exist');
//            }
//        }
    }

    /**
     * Get User account by UserId
     * @param string $id
     * @throws \Exception
     * @return Row
     */    
    public function checkUser($id){
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

}