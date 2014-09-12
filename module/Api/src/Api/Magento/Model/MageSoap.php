<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/9/14
 * Time: 5:40 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;


class MageSoap extends AbstractSoap{

    use EventManagerAwareTrait;

    protected $adapter;

    protected $totaltime;

    protected $sql;

    protected $dirtyCount;

    protected $attributeDirtyCount = 0;

    protected $dirtyItems;

    protected $imgPk = array();

    protected $_startTime;

    protected $_stopTime;

    protected $_totalTime;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
        parent::__construct(SOAP_URL);
//        parent::__construct(SOAP_URL_STAGE);
    }

    public function startStopwatch()
    {
        $mtime = microtime() ;
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->_startTime = $mtime;
    }

    public function stopStopwatch()
    {
        $mtime = microtime() ;
        $mtime = explode(" ",$mtime);
//        var_dump($mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->_stopTime = $mtime;
        $this->_totalTime = round(($this->_stopTime-$this->_startTime),4);
        return date("H:i:s",$this->_totalTime);
    }

    public function soapUpdateProducts($data)
    {
//        $soapHandle = new Client(SOAP_URL);
//        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
//        $i = 0;
        $this->startStopwatch();
        $packet = [];
//        $results = [];
        $skuCollection = [];
        foreach($data as $key => $value){
            if( isset($value['id']) ) {
                $entityID = $value['id'];
                $select = $this->sql->select()->from('product')->columns(['sku'=>'productid'])->where(['entity_id'=>$entityID]);
                $statement = $this->sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $resultSet = new ResultSet;
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet->initialize($result);
                }
                //TODO have to implement a count feature for this.
//        $resultSet->count()
                $skuCollection[$key] = $resultSet->toArray()[0]['sku'];
                array_shift($value);
                $updatedValue = current($value);
//                    $this->productAttribute();
//                    $attributeCode = lcfirst(current(array_keys($value)));
                $attributeCode =  current(array_keys($value));
                $attributeCode = $attributeCode == 'title' ? 'name' : $attributeCode;
                $attributeCode = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',$attributeCode  ));
                //$updatedKey = $this->lookupAttribute(lcfirst(current(array_keys($value))));
//                    echo $updatedKey . ' ' ;
                $packet[$key] = array('entity_id' => $entityID, array($attributeCode => $updatedValue));
//                $i++;
            }
        }

//        $this->insertIntoMageLog($skuCollection ,'catalog_product.update');
        return $this->soapCall($packet, 'catalog_product.update', $skuCollection);
//        $a = 0;
//        $batch = [];
//        while( $a < count($packet) ){
//            $x = 0;
//            while($x < 10 && $a < count($packet)){
//                $batch[$x] = array('catalog_product.update', $packet[$a]);
//                $x++;
//                $a++;
//            }
//            sleep(15);
//            $results[] = $soapHandle->call('multiCall',array($session, $batch));
//            $this->insertIntoMageLog($skuCollection ,'catalog_product.update');
//        }
//        return $results;
    }

    public function soapLinkedProducts($linkedProds)
    {
        $soapHandle = new Client(SOAP_URL);
        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $packet = array();
        $results = Null;
        foreach($linkedProds as $key => $fields){
            $entityId = $linkedProds[$key]['entityId'];
            $dataState = (int)$linkedProds[$key]['dataState'];
            $linkedEntityId = $linkedProds[$key]['linkedEntityId'];
            $type = $linkedProds[$key]['type'];
            if( 3 === $dataState ){
                $packet[$key]['dataState'] = (int)$dataState;
                $packet[$key] = array('type'=>$type, 'product'=>$entityId, 'linkedProduct'=>$linkedEntityId );
            }
            if( 2 === $dataState ){
                $packet[$key]['dataState'] = (int)$dataState;
                $packet[$key] = array('type'=>$type, 'product'=>$entityId, 'linkedProduct'=>$linkedEntityId );
            }
        }

        $a = 0;
        $batch = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)) {
                if( $packet[$a]['dataState'] == 3 ) {
                    $batch[$x] = array('catalog_product_link.remove', $packet[$a]);
                } else {
                    $batch[$x] = array('catalog_product_link.assign', $packet[$a]);
                }
                $x++;
                $a++;
            }
            sleep(15);
            $results[] = $soapHandle->call('multiCall',array($session, $batch));

        }
        return $results;
    }

    public function soapMedia($media = array())
    {
        $packet = $skuCollection = [];
//        if(!is_array($media)) {
//            throw new \InvalidArgumentException(
//                sprintf("Bad argument in class %s for function %s in line %s.",__CLASS__, __FUNCTION__, __LINE__)
//            );
//        }
        $this->startStopwatch();
//        $soapHandle = new Client(SOAP_URL);
//        $session = $soapHandle->call('login',[SOAP_USER, SOAP_USER_PASS]);
        foreach($media as $key => $imgFile) {
//                $imgDomain = $media[$key]['domain'];//this will change to whatever cdn we will have.
            $imgName = $imgFile['filename'];
            $this->imgPk[] = $imgFile['value_id'];
            $entityId = $imgFile['entity_id'];
            $imgPath = file_get_contents("public".$imgName);
//                $imgPath = 'http://www.focuscamera.com/media/catalog/product'.$imgName;

//                $fileContents = file_get_contents($imgPath);
            $fileContentsEncoded = base64_encode($imgPath);
//                $fileContentsEncoded = base64_encode($fileContents);
            $file = array(
                'content'   =>  $fileContentsEncoded,
                'mime'  =>  'image/jpeg',
            );
            $select = $this->sql->select();
            $select->from('product')->columns(array('sku'=>'productid'))->where(array('entity_id'=>$entityId));
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $products = $resultSet->toArray();
            $skuCollection[] = $sku = $products[0]['sku'];
            $packet[$key] = [
                $sku,
                [
                    'file'  =>   $file,
                    'label' =>  $imgFile['label'],//'no label',
                    'position'  => $imgFile['position'],//'0',
//                        'types' =>  array('thumbnail'), //what kind of images is this?
                    'excludes'  =>  0,
                    'remove'    =>  0,
                    'disabled'  =>  0,
                ]
            ];
        }
        return $this->soapCall($packet, 'catalog_product_attribute_media.create', $skuCollection);

//        $results = [];
//        $a = 0;
//        $batch = [];
//        while( $a < count($packet) ){
//            $x = 0;
//            while($x < 10 && $a < count($packet)){
//                $batch[$x] = array('catalog_product_attribute_media.create', $packet[$a]);
//                $x++;
//                $a++;
//            }
//            sleep(15);
//            $results[] = $soapHandle->call('multiCall',array($session, $batch));
//        }
//        $totalTime = $this->stopStopwatch();
//        $this->insertIntoMageLog($skuCollection ,'catalog_product_attribute_media.create', $totalTime);
//        return $results;
    }

    public function soapCategoriesUpdate($categories)
    {
//        $result = false;
//        $soapHandle = new Client(SOAP_URL);
        $packet = $skuCollection = array();
//        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        foreach($categories as $key => $fields){
            $entityId = $categories[$key]['entityId'];
            $skuCollection[] = $sku = $categories[$key]['sku'];
            $dataState = (int)$categories[$key]['dataState'];
            $categortyId = $categories[$key]['categortyId'];
            if( 3 === $dataState ){
                $packet[$key]['dataState'] = (int)$dataState;
                $packet[$key] = array('categoryId'=>$categortyId,'product'=>$entityId );
            }
            if( 2 === $dataState ){
                $packet[$key]['dataState'] = (int)$dataState;
                $packet[$key] = array('categoryId'=>$categortyId,'product'=>$entityId );
            }
        }
        return $this->_soapCall($packet, 'catalog_category.removeProduct', $skuCollection);

        $a = 0;
        $batch = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                if( $packet[$a]['dataState'] == 3 ) {
                    $batch[$x] = array('catalog_category.removeProduct', $packet[$a]);
                } else {
                    $batch[$x] = array('catalog_category.assignProduct', $packet[$a]);
                }
                $x++;
                $a++;
            }
            sleep(15);
            $result[] = $soapHandle->call('multiCall',array($session, $batch));
        }
        return $result;
    }



    public function soapAddProducts($newProds)
    {
        $packet = [];
//        $soapHandle = new Client(SOAP_URL);
//        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $attributeSet = $this->_getAttributeSet();
//        $fetchAttributeList = [$session, 'product_attribute_set.list'];
//        $attributeSets = $soapHandle->call('call', $fetchAttributeList);
//        $attributeSet = current($attributeSets);
//        $set = array(
//            'name'    =>  'This is a Test for Name',
//            'description'   =>  'This is a Test for Description',
//        );
//        $packet = [$session, 'catalog_product.create', ['simple', $attributeSet['set_id'], '123456654321', $set]];
//        echo '<pre>';
//        var_dump($packet);
//        try{
//            $results = $soapHandle->call('call', $packet );
//        } catch (\SoapFault $e){
//            trigger_error($e->getMessage(), E_USER_ERROR ); //should possibly go in log file?
//            $results = $e->getCode(); //should be return to controller?
//        }
//        die();
//        return $results;
//
////        $count = 0;
        $skuCollection = [];
        $attributes = [];
        foreach($newProds as $index => $fields) {
            $keys = array_keys($newProds[$index]);
            $skuCollection[] = $sku = $newProds[$index]['sku'];
            array_shift($keys);
            array_shift($newProds[$index]);
            $packetCount = 0;
            foreach($keys as $ind => $attFields){
                $attributes[$attFields] = $attFields == 'website' ? [$newProds[$index][$attFields]] : $newProds[$index][$attFields];
            }
            $packet[$index] = array('simple', $attributeSet['set_id'], $sku, $attributes );
        }
//    var_dump($packet);
//        die();
         return $this->_soapCall($packet, 'catalog_product.create', $skuCollection);
//        die();
//        $a = 0;
//        $batch = [];
//        while( $a < count($packet) ){
//            $x = 0;
//            while($x < 10 && $a < count($packet)){
//                $batch[$x] = array('catalog_product.create',$packet[$a]);
//                $x++;
//                $a++;
//            }
//            sleep(15);
//                $results[] = $soapHandle->call('multiCall',array($session,$batch));
//        }
//        return $results;
    }

    public function insertIntoMageLog($Skus, $resource, $speed)
    {
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        foreach( $Skus as $sku ){
            $fieldValueMap = array(
                'sku'   =>  $sku,
                'resource'  =>  $resource,
                 'speed'  =>  $speed,
                'pushedby'   =>   $user,
            );
            $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);//'fields' => $mapping,
            $this->getEventManager()->trigger('construct_mage_log', null, array('makeFields'=>$eventWritables));
        }
    }

} 