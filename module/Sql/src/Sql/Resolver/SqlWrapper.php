<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/12/14
 * Time: 8:18 AM
 */

namespace Sql\src\Sql\Resolver;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;


class SqlWrapper {

    protected $adapter;

    protected $sql;

    protected $table;

    protected $columns = array();

    protected $where;

    public function __construct(Adapter $adapter, $table, $columns, $where = array()){
        $this->adapter = $adapter;
        $this->table = $table;
        $this->sql = new Sql($this->adapter);
        $this->columns = $columns;
        $this->where = $where;
    }

    public function insert(){
        $this->sql->insert($this->table);
    }

    public function select(){
        $select = $this->sql->select($this->table);
        $this->where($select, $this->where);
    }

    public function where($select, $where){
        $select->where($where);
    }

} 