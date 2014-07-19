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


class MagentoTable {

    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function dbConnect()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('product');
        $select->columns(array(
                'id'    =>  'entity_id',
                'sku'   =>  'productid',
                'state' =>  'dataState',
                'lastModified'  =>  'modifieddate',
            ));
        $statement = $sql->prepareStatementForSqlObject($select);
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

    public function grabSkus()
    {
        $data = array();
        $dirtyCount = 0;
        $cleanCount = 0;
        $newCount = 0;
        $result = $this->dbConnect();
        $data = $result->toArray();
        foreach($data as $key => $value){
//            echo 'haha';
//            var_dump($value);
//            echo 'hoho';

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
//        die();
//        echo "<pre>";
//        var_dump($result->toArray());
//        die();

//        $result->rewind();
//        $skuID = array();
//        $sku = array();
//        $skuLastModifiedDate = array();
//        while($row = $result->current()){
//            $result->next();
//            echo $row['id'] . ' ' . $row['sku'] . ' ' . $row['state'] . "\n";
//            if($row['state'] == 1){
//                $dirtyCount++;
//                $skuLastModifiedDate['dirty'][] = $row['lastModified'];
//                $skuID['dirty'][] = $row['id'];
//                $sku['dirty'][] = $row['sku'];
//            }
//            if($row['state'] == 0){
//                $cleanCount++;
//                $skuLastModifiedDate['clean'][] = $row['lastModified'];
//                $skuID['clean'][] = $row['id'];
//                $sku['clean'][] = $row['sku'];
//            }
//            if($row['state'] == 2){
//                $newCount++;
//                $skuLastModifiedDate['new'][] = $row['lastModified'];
//                $skuID['new'][] = $row['id'];
//                $sku['new'][] = $row['sku'];
//            }
//
//        }
        return array(
            'data' => $data,
//            'id' => $skuID,
//            'sku' => $sku,
//            'lastModifiedDate' => $skuLastModifiedDate,
            'dirty' =>  $dirtyCount,
            'clean' =>  $cleanCount,
            'new'   =>  $newCount
        );

    }

    public function soapContent()
    {


        $soapClient = new SoapClient('https://www.focuscamera.com/index.php/api/soap/index/?wsdl');
        $session = $soapClient->login('Adellimore', 'krimson1');
        $result = $soapClient->call($session, 'catalog_product.update',array(
            'name'
        ));
//        var_dump($result);
    }

} 