<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use SoapClient;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;


class MagentoTable {

    protected $adapter;

    protected $select;

    protected $data = array();

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function dbConnect()
    {
        $sql = new Sql($this->adapter);
        $this->select = $sql->select('product');
        $this->select ->columns(array(
                'id'    =>  'entity_id',
                'sku'   =>  'productid',
                'state' =>  'dataState',
                'lastModified'  =>  'modifieddate',
            ));
        $titleJoin = new Expression('title.entity_id = product.entity_id and title.attribute_id = 96');
        $this->select->join(array('title' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));
        $statement = $sql->prepareStatementForSqlObject($this->select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        This is my query.
//        var_dump($resultSet);
//        die();
        return $resultSet;
    }

    public function grabSkuData()
    {
        $dirtyCount = 0;
        $cleanCount = 0;
        $newCount = 0;
        $result = $this->dbConnect();
        $this->data = $result->toArray();
        foreach($this->data as $key => $value){
            if ('1' == $value['state']){
                $dirtyCount++;
            }
            if ('0' == $value['state']){
                $cleanCount++;
            }
            if ('2' == $value['state']){
                $newCount++;
            }
        }
        return array(
            'data' => $this->data,
            'dirty' =>  $dirtyCount,
            'clean' =>  $cleanCount,
            'new'   =>  $newCount
        );

    }

    public function soapContent($data)
    {
        $soapClient = new SoapClient('https://www.focuscamera.com/index.php/api/soap/index/?wsdl');
        $session = $soapClient->login('Adellimore', 'krimson1');
        $result = $soapClient->call($session, 'catalog_product.update',array(
            'name'  =>  $data['title']
        ));
        var_dump($result);
    }

} 