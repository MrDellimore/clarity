<?php
/**
 * This class has everything about loging and registering a user and creating a session container for the user.
 * */

namespace Authenticate\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Authenticate\Entity\User;
use Zend\Db\Sql\Sql;
use Zend\Crypt\Password\Bcrypt;

class AuthTable{

    /**
     * @var object SQL
     **/
    protected $sql;

    /**
     * @param Adapter $adapter object
     * */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * Creates a session container for user login in.
     * @param $userSession
     * */
    public function storeUserSession($userSession){
        $loginSession= new Container('login');
        $userInfo = $userSession->current();
        $loginSession->sessionDataforUser = $userInfo;
    }

    /**
     * Once user meets credentials it will create a session container for them.
     * @param $userId
     * */
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

    /**
     * This method is called from the controller loginAction. When users attempts to login it will query for their username
     * and return an array back to the controller.
     * @param $username |array
     * @return array
     * */
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

    /**
     * When user registers it will call this method from controller registerAction. This method will encrypt the user's password.
     * @param array $register
     * @return array $registerUser
     */
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

    /**
     * This method is called fromthe Auth Class and it will persist the user when they try to register.
     * @param User $user object
     * @return ResultInterface object | boolean
     * */
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
        while( $current = $resultSet->current() ) {
            $resultSet->next();
            // Checks to see if user's first and last name exists. If so return back boolean not allowing them to register
            if( $current['firstname'] == $user->getFirstName() || $current['lastname'] == $user->getLastName() ) {
                return false;
            }
        }
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

        $insert->columns(array_keys($data))
               ->values($data);
        $statement = $this->sql->prepareStatementForSqlObject($insert);
        return $statement->execute();
    }

    /**
     * Get User account by UserId
     * Don't this we're using this method at all. But I left it here anyway in case it is used. Note by Will Salazar 12/9/2014 11:34AM
     * @param string $id
     * @throws \Exception
     * @return $row
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