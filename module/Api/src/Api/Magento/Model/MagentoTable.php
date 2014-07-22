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

    protected $sql;

    protected $dirtyCount;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function sqlQuery($tableName = null, $fields = array(), $where = array())
    {
        $this->sql = new Sql($this->adapter);
        $this->select = $this->sql->select();
        $this->select->from($tableName);
        if(count($where) > 0){
            $this->select->where($where);
        }
        $this->select->columns($fields);

//        return $sql;
    }

    public function countMagentoUpdates()
    {
        $fields = array(
            'id'    =>  'entity_id',
            'sku'   =>  'productid',
            'state' =>  'dataState',
            'lastModified'  =>  'lastModifiedDate',
        );
        $where = array(
            'dataState' => '1',
        );
        $this->sqlQuery('product', $fields, $where);
//        $sql = new Sql($this->adapter);
//        $this->select = $sql->select('product');
//        $this->select->columns(array(
//                'id'    =>  'entity_id',
//                'sku'   =>  'productid',
//                'state' =>  'dataState',
//                'lastModified'  =>  'lastModifiedDate',
//            ));

//        $titleJoin = new Expression('title.entity_id = product.entity_id and title.attribute_id = 96 and product.dataState = 1');
//        $quantityJoin = new Expression('quantity.entity_id = product.entity_id and title.attribute_id = 96 and product.dataState = 1');
//        $this->select->join(array('title' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));
//        $this->select->where($where);
        $statement = $this->sql->prepareStatementForSqlObject($this->select);
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

    public function grabDirtyAttributes($entityID, $sku)
    {
        $dirtyAttributes = array();
        $attributes = array('varchar', 'int', 'decimal', 'datetime');
        $fields = array(
            'id'   =>  'entity_id',
            'attributeID' =>  'attribute_id',
            'value'  => 'value',
            'state' =>  'dataState',
            'lastModDate'   =>  'lastModifiedDate',
        );
        $where = array(
            'dataState'     =>  1,
            'entity_id' => $entityID,

        );
        foreach($attributes as $atts){
            $this->sqlQuery('productattribute_'. $atts, $fields, $where);
            $statement = $this->sql->prepareStatementForSqlObject($this->select);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }

//            echo "<pre>";
//            var_dump($resultSet->toArray());
//            echo gettype($dirtyAttributes);
            $dirtySku = array('sku' => $sku);
            $x[] = $resultSet->toArray();
            array_push($x,$dirtySku);
//            array_push($dirtySku, $resultSet->toArray());
//            $dirtyAttributes = array_merge($dirtyAttributes ,$resultSet->toArray());
//            array_push($dirtySku, $dirtyAttributes );

//            var_dump($dirtyAttributes);
//            echo gettype($dirtyAttributes);
//            var_dump($sku );
//            array_push ( $dirtyAttributes, $sku );

//        This is my query.
//            echo "<pre>";
//        var_dump($resultSet);


        }
echo "<pre>";
        var_dump($x);
//        array_push ( $dirtyAttributes, array('sku'=>$sku));
//        var_dump($dirtyAttributes);
//        return $dirtyAttributes;
//        die();
    }

    public function setDirtyCount($dirtyCount)
    {
        $this->dirtyCount = $dirtyCount;
    }

    public function getDirtyCount()
    {
        return $this->dirtyCount;
    }

    public function grabSkuData()
    {
        $dirtyCount = 0;
        $cleanCount = 0;
        $newCount = 0;
        $result = $this->countMagentoUpdates();
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
//            'dirty' =>  $dirtyCount,
//            'clean' =>  $cleanCount,
//            'new'   =>  $newCount
        );

    }

    public function soapContent($data)
    {
//        echo SOAP_URL;
//        die();
        $soapClient = new SoapClient(SOAP_URL);
        $session = $soapClient->login('Adellimore', 'krimson1');
        foreach($data['data'] as $key => $value){
//                echo $data['data'][$key]['sku'] . ' ' . $data['data'][$key]['title'] . "<br />";
            $sku = $data['data'][$key]['sku'];
            $title = $data['data'][$key]['title'];
            $result = $soapClient->call($session, 'catalog_product.update',array(
//                    '1',array(
                    $sku,array(
                        'name'  =>  $title
                    )
                )
            );
        }
        return $result;
//        var_dump($result);
//        die();
    }

    public function updateToClean($data)
    {
        $sql = new Sql($this->adapter);
        foreach($data['data'] as $key => $value){
//                echo $data['data'][$key]['sku'] . ' ' . $data['data'][$key]['title'] . "<br />";
                $sku = $data['data'][$key]['sku'];
//                $state = $data['data'][$key]['state'];

        $update = $sql->update('product');
//        $update->columns(array(
//            'state' =>  'dataState',
//        ));
        $update->where(
            array(
                'productid' =>  $sku
            )
        );
        $update->set(array(
            'dataState' => 0,
//            'lastModifiedDate'  => date('Y-m-d h:i:s')
        ));
//        $titleJoin = new Expression('title.entity_id = product.entity_id and title.attribute_id = 96');
//        $this->select->join(array('title' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));
        $statement = $sql->prepareStatementForSqlObject($update);
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
    }

}