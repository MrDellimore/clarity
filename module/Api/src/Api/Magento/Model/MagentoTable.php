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
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('user' => 'firstname'));

        $select->where(array( 'dataState' => '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $dirtyCount = $resultSet->count();
        $this->setDirtyCount($dirtyCount);
        $result = $resultSet->toArray();

        //Fetch Title
        $newAttribute = $this->fetchAttribute( 'varchar','96','title');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Price
        $newAttribute = $this->fetchAttribute( 'decimal','99','price');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Inventory
        $newAttribute = $this->fetchAttribute( 'int','1','Inventory');
        if(is_array($newAttribute)){
            foreach($newAttribute as  $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Status
        $newAttribute = $this->fetchAttribute( 'int','273','Status');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch URLkey
        $newAttribute = $this->fetchAttribute( 'varchar','481','urlKey');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Cost
        $newAttribute = $this->fetchAttribute( 'decimal','100','cost');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Rebate Price
        $newAttribute = $this->fetchAttribute( 'decimal','1590','rebate');
//        $result[array_keys($newAttribute[0]] = $newAttribute;
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Mail in Rebate Price
        $newAttribute = $this->fetchAttribute( 'decimal','1593','mailinRebate');
//        $result[array_keys($newAttribute[0]] = $newAttribute;
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Special Price
        $newAttribute = $this->fetchAttribute( 'decimal','567','specialPrice');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Special Start Date
        $newAttribute = $this->fetchAttribute( 'datetime','568','specialEndDate');
//        $result[array_keys($newAttribute[0]] = $newAttribute;
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Special End Date
        $newAttribute = $this->fetchAttribute( 'datetime','569','specialStartDate');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Rebate Start Date
        $newAttribute = $this->fetchAttribute( 'datetime','1591','rebateEndDate');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Rebate End Date
        $newAttribute = $this->fetchAttribute( 'datetime','1592','rebateStartDate');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch Mail in Start Date
        $newAttribute = $this->fetchAttribute( 'datetime','1594','mailinEndDate');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Mail in  End Date
        $newAttribute = $this->fetchAttribute( 'datetime','1595','mailinStartDate');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch metaTitle
        $newAttribute = $this->fetchAttribute( 'varchar','103','meta_title');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch metaDescription
        $newAttribute = $this->fetchAttribute( 'varchar','105','metaDescription');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Description
        $newAttribute = $this->fetchAttribute( 'text','97','description');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch inBox
        $newAttribute = $this->fetchAttribute('text','1633','inBox');
        // die(print_r($newAttribute);
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }

        //Fetch includesFree
        $newAttribute = $this->fetchAttribute( 'text','1679','includesFree');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
                $result[] = $newAtt;
            }
        }
        //Fetch Short Description
        $newAttribute = $this->fetchAttribute( 'text','506','short_description');
        if(is_array($newAttribute)){
            foreach($newAttribute as $newAtt){
               $result[] = $newAtt;
            }
        }
        return $result;
    }

    public function fetchAttribute($tableType, $attributeid, $property)
    {
            $select = $this->sql->select();

            $select->from('productattribute_'.$tableType);

            $select->columns(array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate'));
            $select->join(array('p' => 'product'),'p.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid'));
            $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ' ,array('user' => 'firstname'));
            $select->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));

            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

            $resultSet = new ResultSet;

            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $result = $resultSet->toArray();

            //check if array passed or value given
            if(!(is_array($result)) || current($result)[$property] == ''){
                $result = null;

            }

        return $result;
    }


        public function setDirtyCount($dirtyCount)
        {
            $this->dirtyCount = $dirtyCount;
        }

        public function getDirtyCount()
        {
            return $this->dirtyCount;
        }


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
            $soapClient = new SoapClient(SOAP_URL);
            $session = $soapClient->login(SOAP_USER, SOAP_USER_PASS);
            $i = 0;
            $updateBatch = array();
            foreach($data as $key => $value){
                        if( isset($value['id']) ) {
                            $entityID = $value['id'];
                            array_shift($value);
                            $updatedValue = current($value);
                            $updatedKey = $this->lookupAttribute(lcfirst(current(array_keys($value))));
                            $updateBatch[$i] = array('entity_id' => $entityID, array($updatedKey => $updatedValue));
                            $i++;
                        }
//                    }
//                }
            }
            $a = 0;
            while( $a < count($updateBatch) ){
                $x = 0;
                while($x < 10 && $a < count($updateBatch)){
                    $queueBatch[$x] = array(PRODUCT_UPDATE, $updateBatch[$a]);
                    $x++;
                    $a++;
                }
                sleep(15);
                $result = $soapClient->multiCall($session, $queueBatch);
            }
            return $result;
        }

        /*
         * todo switch to SQL lookup
         */
        public function fetchAttributeID($attributeField)
        {
            switch($attributeField){
                case 'title':
                    return 96;
                case 'description':
                    return 97;
                case 'short_descrition':
                    return 506;

            }
        }

        /*
         * todo no longer needed after sql lookup
         * select backend
         */
        public function fetchTableType($tableType)
        {
            switch($tableType){
                case ($tableType == 'title'):
                    return 'varchar';
                case ($tableType == 'description' || $tableType == 'inbox'):
                    return 'text';


            }
        }

        public function updateToClean($data)
        {
            foreach($data as $key => $value){
                $update = $this->sql->update();
                    if(array_key_exists('sku', $data[$key])){
                        $update->table('product');
                        $update->set(array('dataState'=>'0'));
                        $update->where(array('productid'=>$data[$key]['sku']));
                    } else {
                        $entityId = $data[$key]['id'];
                        $sku = $data[$key]['item'];
                        array_shift($data[$key]);
                        $attributeField = current(array_keys($data[$key]));
//                        $attributeValue = current($data[$key]);
                        $update->table('productattribute_' . $this->fetchTableType($attributeField));
                        $update->set(array('dataState'=>'0'));
                        $update->where(array('entity_id'=>$entityId, 'attribute_id'=>$this->fetchAttributeID($attributeField)));
                    }
//                }
                $statement = $this->sql->prepareStatementForSqlObject($update);
                $result = $statement->execute();
                $resultSet = new ResultSet;
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet->initialize($result);
                }
            }
            return $resultSet;
        }
}