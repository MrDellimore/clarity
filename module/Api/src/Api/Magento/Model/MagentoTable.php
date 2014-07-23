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
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;
use Zend\Filter\Null;
use Zend\Db\Sql\AbstractSql;


class MagentoTable {

    protected $adapter;

    protected $select;

    protected $data = array();

    protected $sql;

    protected $dirtyCount;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function lookupClean()
    {
        $select = $this->sql->select();
        $select->from('product');
//        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'modifieddate', 'item' => 'productid'));

        $select->where(array( 'dataState' => '0'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $cleanCount = $resultSet->count();
        return $resultSet->count();
    }

    public function lookupDirt()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'modifieddate', 'item' => 'productid'));

        $select->where(array( 'dataState' => '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $dirtyCount = $resultSet->count();
        $this->setDirtyCount($dirtyCount);
//        echo $dirtyCount;
//        die();
        //add to resultset additional Data
        $result = $resultSet->toArray();
//        $result = $result;
//        echo "<pre>";
//        print_r($result);
//        echo $result[0]['sku'];
//        echo 'haha';
        //Fetch Title
        $select = $this->sql->select();
        $newAttibute = $this->fetchAttribute($select, 'varchar','96','title');
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

//        $result[] =$newAttibute;

        //Fetch Price
        $newAttibute = $this->fetchAttribute($select, 'decimal','99','price');
//        $result[array_keys($newAttibute[0]] = current($newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

//        var_dump($result);
//        die();
        //Fetch Inventory
        $newAttibute = $this->fetchAttribute($select, 'int','1','Inventory');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as  $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Status
        $newAttibute = $this->fetchAttribute($select, 'int','273','Status');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch URLkey
        $newAttibute = $this->fetchAttribute($select, 'varchar','481','urlKey');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Cost
        $newAttibute = $this->fetchAttribute($select, 'decimal','100','cost');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Rebate Price
        $newAttibute = $this->fetchAttribute($select, 'decimal','1590','rebate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Mail in Rebate Price
        $newAttibute = $this->fetchAttribute($select, 'decimal','1593','mailinRebate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Special Price
        $newAttibute = $this->fetchAttribute($select, 'decimal','567','specialPrice');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }


        //Fetch Special Start Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','568','specialEndDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }



        //Fetch Special End Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','569','specialStartDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }


        //Fetch Rebate Start Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','1591','rebateEndDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }


        //Fetch Rebate End Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','1592','rebateStartDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }


        //Fetch Mail in Start Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','1594','mailinEndDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Mail in  End Date
        $newAttibute = $this->fetchAttribute($select, 'datetime','1595','mailinStartDate');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch metaTitle
        $newAttibute = $this->fetchAttribute($select, 'varchar','103','meta_title');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch metaDescription
        $newAttibute = $this->fetchAttribute($select, 'varchar','105','metaDescription');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Description
        $newAttibute = $this->fetchAttribute($select, 'text','97','description');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch inBox
        $newAttibute = $this->fetchAttribute($select, 'text','1633','inBox');
        // die(print_r($newAttibute);
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch includesFree
        $newAttibute = $this->fetchAttribute($select, 'text','1679','includesFree');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }

        //Fetch Short Description
        $newAttibute = $this->fetchAttribute($select, 'text','506','short_description');
//        $result[array_keys($newAttibute[0]] = $newAttibute;
        foreach($newAttibute as $newAtt){
            $result[] = $newAtt;
        }
//echo "<pre>";
//        echo 'haha';
//var_dump($result);
//        echo 'hoho';
//        die();
        return $result;
    }

    public function fetchAttribute(AbstractSql $sql, $tableType, $attributeid, $property, $sqlType = true){
        if($sqlType){
            $sql->from('productattribute_'.$tableType);

            $sql->columns(array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate'));
            $sql->join(array('p' => 'product'),'p.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid'));
            $sql->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));

            $statement = $this->sql->prepareStatementForSqlObject($sql);
            $result = $statement->execute();

            $resultSet = new ResultSet;

            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $result = $resultSet->toArray();

            //check if array passed or value given
            if(!(is_array($result)) || current($result)[$property] == ''){
                $result = array('id' => null);
            }
    //        else{
    //            $result = current($result);
    //        }


        }

        return $result;
    }

    /*
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
    //        $where = array(
    //            'dataState' => '1',
    //        );
            $this->sqlQuery('product', $fields);
            $titleJoin = new Expression('title.entity_id = product.entity_id and title.attribute_id = 96 and title.dataState = 1');
            $costJoin = new Expression('cost.entity_id = product.entity_id and cost.attribute_id = 100 and cost.dataState = 1');
            $this->select->join(array('title' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'), Select::JOIN_LEFT);
            $this->select->join(array('cost' => 'productattribute_decimal'), $costJoin,array('cost' => 'value'), Select::JOIN_LEFT);
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

        public function grabDirtyAttributes($entityID,$sku)
        {
            $dirtyAttributes = array();
            $attributes = array('varchar', 'text', 'int', 'decimal', 'datetime');

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
    //            $dirtySku = array('sku' => $sku);
                $dirtyAttributes = array_merge($resultSet->toArray(), $dirtyAttributes );
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

            array_push ( $dirtyAttributes, array('sku'=>$sku));
    //        var_dump($dirtyAttributes);
            return $dirtyAttributes;
    //        die();
        }
*/
        public function setDirtyCount($dirtyCount)
        {
            $this->dirtyCount = $dirtyCount;
        }

        public function getDirtyCount()
        {
            return $this->dirtyCount;
        }
/*
        public function grabSkuData()
        {
            $dirtyCount = 0;
            $cleanCount = 0;
            $newCount = 0;
            $result = $this->countMagentoUpdates();
            $this->data = $result->toArray();
    //        foreach($this->data as $key => $value){
    //            if ('1' == $value['state']){
    //                $dirtyCount++;
    //            }
    //            if ('0' == $value['state']){
    //                $cleanCount++;
    //            }
    //            if ('2' == $value['state']){
    //                $newCount++;
    //            }
    //        }
            echo "<pre>";
            var_dump($this->data);
            die();
            return array(
                'data' => $this->data,
    //            'dirty' =>  $dirtyCount,
    //            'clean' =>  $cleanCount,
    //            'new'   =>  $newCount
            );

        }
*/

        public function lookupAttribute($key)
        {
            switch($key){
                case 'sku':
                    return 'product';
                case 'title':
                    return 'name';
                case 'description':
                    return $key;
                case 'urlKey':
                    return 'url_key';
            }

        }
        public function soapContent($data)
        {
            echo "<pre>";
//            var_dump($data);

            $soapClient = new SoapClient(SOAP_URL);
            $session = $soapClient->login('Adellimore', 'krimson1');
            $i = 0;
            $updateBatch = array();
            foreach($data as $key => $value){
//                echo gettype($data[$key]);
//                var_dump($data[$key]);
//                var_dump($data);

//                if( array_key_exists('sku', $data[$key]) || !is_null($data[$key]) ){
//                    continue;
//                } else if(is_null($data[$key])){
//                    unset($data[$key]);
//                } else{
//                    foreach($value as $val){
//var_dump($value);
                        if( isset($value['id']) ) {
//                            echo 'haha';
                            $entityID = $value['id'];
//                            $sku = end($value);
                            array_shift($value);
                            $updatedValue = current($value);
//                            echo end($value);
                            $updatedKey = $this->lookupAttribute(lcfirst(current(array_keys($value))));
//                            var_dump($val);
                            $updateBatch[$i] = array('entity_id' => $entityID, array($updatedKey => $updatedValue));
//                            echo $entityID . ' ' . $updatedKey . ' ' . $updatedValue. "<br />";
                            $i++;
                        }
//                    }
//                }
            }
            $a = 0;
//            echo "<pre>";
//            echo 'updateBatch';
//            var_dump($updateBatch);
            while( $a < count($updateBatch) ){
                $x = 0;
                while($x < 10 && $a < count($updateBatch)){
                    $queueBatch[$x] = array('catalog_product.update', $updateBatch[$a]);
                    $x++;
                    $a++;
                }

//                echo 'queueBatch';
                sleep(15);
                $result = $soapClient->multiCall($session, $queueBatch);
//                var_dump($result);
//                var_dump($queueBatch);

            }
//            die();
            return $result;
        }

        public function fetchTableType($att)
        {
            $tableType = array('product', 'varchar', 'text');
            $attributes = array('sku', 'title', 'description');

            if(in_array($att, $attributes)){
                $returnAtt = $this->lookupAttribute($att);
            }
            if('product' == $returnAtt){
                $returnAtt = $returnAtt.'id';
            }
            switch($returnAtt){
                case 'productid':
                    return 'product';
                case 'name':
                    return 'varchar';
                case 'description':
                    return 'text';
            }
        }

        public function updateToClean($data)
        {
            echo "<pre>";
            var_dump($data);
            $update = $this->sql->update();
            foreach($data as $key => $value){
//                if(is_null($data[$key])) {
//                    unset($data[$key]);
//                }
//                echo 'haha';
//                var_dump($value);
                foreach($value as $attribute => $attValue){
//                    public function fetchAttribute(AbstractSql $sql, $tableType, $attributeid, $property, $sqlType = true);
//echo $attribute . ' ' ;
//                        $this->fetchAttribute($update, $this->fetchTableType($attribute), , , false);
                }
    //                echo $data['data'][$key]['sku'] . ' ' . $data['data'][$key]['title'] . "<br />";
//                    $sku = $data[$key]['sku'];
    //                $state = $data['data'][$key]['state'];

//            $update = $this->sql->update('product');
    //        $update->columns(array(
    //            'state' =>  'dataState',
    //        ));
//            $update->where(
//                array(
//                    'productid' =>  $sku
//                )
//            );
//            $update->set(array(
//                'dataState' => 0,
//                'lastModifiedDate'  => date('Y-m-d h:i:s')
//            ));
    //        $titleJoin = new Expression('title.entity_id = product.entity_id and title.attribute_id = 96');
    //        $this->select->join(array('title' => 'productattribute_varchar'), $titleJoin,array('title' => 'value'));
//            $statement = $this->sql->prepareStatementForSqlObject($update);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
    //        This is my query.
    //        var_dump($resultSet);

            }
            die();

            return $resultSet;
        }
}