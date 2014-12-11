<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 12/11/14
 * Time: 11:40 AM
 */

namespace Marketing\Deals\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Marketing\Deals\Entity\Deals;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate;
use Zend\EventManager\EventManagerAwareTrait;

class DealsTable {

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * Inserts deal into the database.
     * @param Deals $deals
     * @return string
     */
    public function persist(Deals $deals)
    {

        $insert = $this->sql->insert('deals')
                  ->columns(['sku','special_price','inventory','start_date','end_date','max_qty','us_standard'])
                  ->values([
                    'sku'               =>  $deals->getSku(),
                    'special_price'     =>  $deals->getSpecialPrice(),
                    'inventory'         =>  $deals->getInventory(),
                    'start_date'        =>  date('Y-m-d g:i:s',strtotime($deals->getStartDate())),
                    'end_date'          =>   date('Y-m-d g:i:s',strtotime($deals->getEndDate())),
                    'max_qty'           =>  $deals->getMaxQty(),
                    'us_standard'       =>  $deals->getUsStandard(),
                        ]);
        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();
        $result = $deals->getSku() . ' was successfully saved!';
        return $result;
    }

    /**
     * Selects from the DB and dumps it in data table
     * @param null $sku
     * @param null $limit
     * @return array
     */
    public function searchDeals( $sku = Null, $limit = Null )
    {
        $d = [];
        $select = $this->sql->select()
            ->from('deals')
            ->columns(['sku','special_price','inventory','start_date','end_date','max_qty','us_standard']);
        $filter = new Where;
        if ( $sku ) {
           $filter->like('deals.sku', $sku.'%');
        }
        $select->where($filter);
        if( $limit ) {
            $select->limit((int)$limit);
        }
        $select->order('deals.end_date DESC');
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $deals = $resultSet->toArray();
        foreach ( $deals as $key => $deal ) {
            $d[$key]['sku']             = "<a href='/update-deals/". $deal['sku'] . "'>" .$deal['sku'] ."</a>";
            $d[$key]['special_price']   = $deal['special_price'];
            $d[$key]['inventory']       = $deal['inventory'];
            $d[$key]['start_date']      = $deal['start_date'];
            $d[$key]['end_date']        = $deal['end_date'];
            $d[$key]['max_qty']         = $deal['max_qty'];
            $d[$key]['us_standard']     = $deal['us_standard'];
        }
        return $d;
    }

    /**
     * Searches for Sku to display all fields in the front end.
     * @param $sku
     * @return bool
     */
    public function validateSku($sku)
    {
        $select = $this->sql->select()
            ->from('deals')
            ->columns(['sku','special_price','inventory','start_date','end_date','max_qty','us_standard']);
        $filter = new Where;
        if ( $sku ) {
            $filter->equalTo('deals.sku', $sku);
        }
        $select->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        if ( count($resultSet->toArray() ) ) {
            return true;
        }
        return false;
    }

    /**
     * Selects from the DB and displays from with values for a particular sku.
     * @param $sku
     * @return array
     */
    public function fetchDeals($sku)
    {
        $select = $this->sql->select()
            ->from('deals')
            ->columns(['sku','special_price','inventory','start_date','end_date','max_qty','us_standard']);
        $filter = new Where;
        if ( $sku ) {
            $filter->equalTo('deals.sku', $sku);
        }
        $select->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }

        return $resultSet->toArray();
    }

    /**
     * Updates DB when a field for a particular sku has changed.
     * @param Deals $deals
     * @return string
     */
    public function updateDeals(Deals $deals)
    {
        $update = $this->sql->update('deals')
                            ->set([
                                'special_price'     =>  $deals->getSpecialPrice(),
                                'inventory'         =>  $deals->getInventory(),
                                'start_date'        =>  date('Y-m-d g:i:s',strtotime($deals->getStartDate())),
                                'end_date'          =>  date('Y-m-d g:i:s',strtotime($deals->getEndDate())),
                                'max_qty'           =>  $deals->getMaxQty(),
                                'us_standard'       =>  $deals->getUsStandard(),
                                ])
                            ->where(['sku'=>$deals->getSku()
                            ]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        $result = $deals->getSku() . ' was successfully updated!';
        return $result;
    }

    /**
     * Deletes deal from DB
     * @param $sku
     */
    public function deleteDeal( $sku )
    {
        $delete = $this->sql->delete('deals')->where(['sku'=>$sku]);
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $statement->execute();
    }
}