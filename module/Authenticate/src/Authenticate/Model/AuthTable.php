<?php
namespace Authenticate\Model;

use Authenticate\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;

class AuthTable{
    
    protected $tableGateway;
    protected $sql;

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);

    }
    public function storeUserSession($userSession){
        $sessionContainer = new Container('login');
        $authTimeout = 60 *30 ;
        $sessionContainer->setExpirationSeconds($authTimeout);
        $userInfo = $userSession->current();
        $sessionContainer->sessionDataforUser = $userInfo;
    }
    public function storeUser($userId)
    {
        $this->userId = $userId;
        $columns = array('userid','firstname', 'lastname', 'email', 'username', 'password', 'role', 'datecreated');
        $select = $this->sql->select('users');
        $select->columns($columns)
            ->where(array('userid' => $userId));
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
            $resultSet->next();
            if($current['firstname'] == $user->getFirstName() || $current['lastname'] == $user->getLastName() ){
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
    public function updateRouteForUser($id, RouteMatch $routeMatch) {
        $controller = strtolower($routeMatch->getParam('controller'));
        if (
            strstr($controller, 'ajax') != null ||
            strstr($controller, 'api') != null ||
            strstr($controller, 'logging') != null ||
            $routeMatch->getParam('sku') == 'favicon.ico' ||
            false
        ) {
            return;
        }

        $paramjson = json_encode($routeMatch->getParams());
        $update = $this->sql->update('users')
            ->set([
                'lastRoute' => $paramjson,
                'accessed' => time() + (30*60)
            ])
            ->where([
                'userid' => $id
		    ]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        return (bool)$statement->execute();
    }
}