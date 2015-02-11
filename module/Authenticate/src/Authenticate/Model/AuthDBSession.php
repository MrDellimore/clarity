<?php
/**
 * Created by PhpStorm.
 * User: smergler
 * Date: 1/27/2015
 * Time: 11:08 AM
 */

namespace Authenticate\Model;

use Zend\Db\Sql\Exception\InvalidArgumentException;
use Zend\Db\Sql\Sql;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Session\SaveHandler\SaveHandlerInterface;

class AuthDBSession implements SaveHandlerInterface {

    protected $sql;
    protected $table;
    protected $options;
    protected $sessionName;
    protected $lifetime;
    protected $id;

    protected function _runSql($unprep) {
        $adapter = $this->sql->getAdapter();
        $selectString = $this->sql->getSqlStringForSqlObject($unprep);
        return $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    public function __construct(Sql $sql, AuthDBSessionOptions $options) {
        if (!($sql instanceof Sql)) {
            throw new InvalidArgumentException(
                'Parameter of type %s is invalid; must be MongoClient or Mongo',
                (is_object($sql) ? get_class($sql) : gettype($sql))
            );
        }

        $this->sql = $sql;
        $this->options = $options;
    }
    public function open($savePath, $name) {
        $this->sessionName = $name;
        $this->lifetime    = 30 * 60;
        return true;
    }
    public function close(){
        return true;
    }
    public function read($id) {
        $this->id = $id;
        $criteria = [
            $this->options->getIdColumn() => $id,
            $this->options->getNameColumn() => $this->sessionName
        ];
        $select = $this->sql->select()
            ->where($criteria)
            ->limit(1);

        $session = $this->_runSql($select);

        if (!($session = $session->current())) {
            return '';
        }
        if ($session[$this->options->getModifiedColumn()] +
            $session[$this->options->getLifetimeColumn()] > time()) {
            return $session[$this->options->getDataColumn()];
        }
        return '';
    }
    public function write($id, $data) {
        $saveData = [
            $this->options->getDataColumn() => $data
        ];
        return $this->saveData($id, $saveData);
    }
    protected function saveData($id, array $saveData) {
        $this->id = $id;
        $criteria = [
            $this->options->getIdColumn() => $id,
            $this->options->getNameColumn() => $this->sessionName
        ];

        $data = [
            $this->options->getModifiedColumn() => time(),
        ] + $saveData;

        $select = $this->sql->select()
            ->where($criteria);

        $rows = $this->_runSql($select);
        if ($row = $rows->current()) {
            return (bool) $this->_runSql($this->sql->update()
                ->set($data)
                ->where($criteria));
        }
        $data = [
                $this->options->getLifetimeColumn() => $this->lifetime,
                $this->options->getIdColumn() => $id,
                $this->options->getNameColumn() => $this->sessionName
        ] + $data;

        return (bool) $this->_runSql($this->sql->insert()
            ->values($data));
    }
    public function destroy($id) {
        return (bool) $this->_runSql($this->sql->delete()
            ->where([
                $this->options->getIdColumn() => $id,
                $this->options->getNameColumn() => $this->sessionName
            ]));
    }
    public function gc($maxlifetime) {
        $platform = $this->sql->getAdapter()->getPlatform();
        return (bool) $this->_runSql($this->sql->delete()
            ->where(sprintf('%s < %d',
                $platform->quoteIdentifier($this->options->getModifiedColumn()),
                (time() - $this->lifetime)
            ))
        );
    }

    /**
     * @param MvcEvent $event
     * This will save route data from the MVCEvent
     */
    public function saveRouteData(MvcEvent $event) {

        $session = new Container('login');
        $sessionData = $session->sessionDataforUser;
        if ($sessionData) {
            
        }


        // if there were no saves, we probably don't have an ID
        if (!$this->id) {
            return false;
        }
        $route = $event->getRouteMatch();
        $saveData = [
            // $this->options->getRouteColumn() => $route->getMatchedRouteName()
        ];
        return $this->saveData($this->id, $saveData);
    }
}