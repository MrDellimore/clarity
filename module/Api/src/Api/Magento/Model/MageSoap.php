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
//        $this->sql = new Sql($this->adapter);
        parent::__construct(SOAP_URL);
//        parent::__construct(SOAP_URL_STAGE2);
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
        $this->_totalTime = (int)$this->_stopTime-(int)$this->_startTime;
        return date("H:i:s", $this->_totalTime);
    }

    public function soapUpdateProducts($changedProducts)
    {
        $this->startStopwatch();
        $packet = $skuCollection = $atts = [];
//        echo 'changed Products';
//        var_dump($changedProducts);
        foreach( $changedProducts as $key => $attributes ) {
//            echo 'attributes';
//            var_dump($attributes);
            $entityID = $attributes['id'];
            array_shift($attributes);

            foreach( $attributes as $ind => $attrib ) {
//                echo 'attribs';
//                var_dump($attrib);

                $skuCollection[] = $attrib['sku'];
//                $property = $attrib['property'];
                $newValue = $attrib['newValue'];
//                $atts[$property] = $newValue;
                foreach( $attrib as $prop => $attribute ) {
                    if ( $prop == 'property' ) {
                        $atts[$attribute] = $newValue;
                    }
//                    $packet[$key] = array('entity_id' => $entityID, $atts);
                }
                $packet[$key] = array('entity_id' => $entityID, $atts);
//                $atts = [];
            }
                            $atts = [];
        }
//var_dump($packet);
//die();
        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
    }

    public function soapLinkedProducts($linkedProds)
    {
//        $soapHandle = new Client(SOAP_URL);
//        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $packet = $skuCollection = array();
        $results = Null;
        foreach($linkedProds as $key => $linked){
            $entityId = $linked['id'];
            $dataState = (int)$linked['dataState'];
            $linkedEntityId = $linked['linkedId'];
            $skuCollection[] = $linked['sku'];
            $type = $linked['type'];
            if( 3 === $dataState ) {
                $packet[$key] = [
                    'type'          =>  lcfirst(str_replace(' ','_',$type)),
                    'product'       =>  $entityId,
                    'linkedProduct' =>  $linkedEntityId,
                    'resource'      =>  'catalog_product_link.remove',
                    'dataState'     =>  (int)$dataState,
                ];
            }
            if( 2 === $dataState ) {
                $packet[$key] = [
                    'type'          =>  lcfirst(str_replace(' ','_',$type)),
                    'product'       =>  $entityId,
                    'linkedProduct' =>  $linkedEntityId,
                    'resource'      =>  'catalog_product_link.assign',
                    'dataState'     =>  (int)$dataState,
                ];
            }
        }
//        echo '<pre>';
//        var_dump($packet);
//        die();
        return $this->_soapCall($packet, null, $skuCollection);
    }

    public function soapMedia($media = array())
    {
//        ["imageid"]=>
//    string(6) "322288"
//    ["id"]=>
//    string(3) "169"
//    ["filename"]=>
//    string(53) "/images/10e4957d6fa56ce12a8fa333ef8bfd6ba74881f4.jpeg"
//    ["sku"]=>
//    string(8) "2292B001"
        $packet = $skuCollection = [];
        $this->startStopwatch();
//        $soapHandle = new Client(SOAP_URL);
//        $session = $soapHandle->call('login',[SOAP_USER, SOAP_USER_PASS]);
        foreach($media as $key => $imgFile) {
//                $imgDomain = $media[$key]['domain'];//this will change to whatever cdn we will have.
            $imgName = $imgFile['filename'];
            $this->imgPk[] = $imgFile['imageid'];
            $entityId = $imgFile['id'];
            $imgPath = file_get_contents("public".$imgName);
//                $imgPath = 'http://www.focuscamera.com/media/catalog/product'.$imgName;

//                $fileContents = file_get_contents($imgPath);
            $fileContentsEncoded = base64_encode($imgPath);
//                $fileContentsEncoded = base64_encode($fileContents);
            $file = array(
                'content'   =>  $fileContentsEncoded,
                'mime'  =>  'image/jpeg',
            );
//            $select = $this->sql->select();
//            $select->from('product')->columns(array('sku'=>'productid'))->where(array('entity_id'=>$entityId));
//            $statement = $this->sql->prepareStatementForSqlObject($select);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
//            $products = $resultSet->toArray();
            $skuCollection[] = $sku = $imgFile['sku'];
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
//        var_dump($packet);
//        die();
        return $this->_soapCall($packet, 'catalog_product_attribute_media.create', $skuCollection);
    }

    public function soapCategoriesUpdate($categories)
    {
//        $result = false;
//        $soapHandle = new Client(SOAP_URL);
        $packet = $skuCollection = array();
//        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        foreach($categories as $key => $category){
            $entityId = $category['id'];
            $skuCollection[] = $sku = $category['sku'];
            $dataState = (int)$category['dataState'];
            $categortyId = $category['categoryId'];
            if( 3 === $dataState ) {
                $packet[$key] = [
                    'categoryId'    =>      $categortyId,
                    'product'       =>      $entityId,
                    'dataState'     =>      (int)$dataState,
                    'resource'      =>      'catalog_category.removeProduct',
                ];
            }
            if( 2 === $dataState ) {
                $packet[$key] = [
                    'categoryId'    =>  $categortyId,
                    'product'       =>  $entityId,
                    'dataState'     =>  (int)$dataState,
                    'resource'      =>  'catalog_category.assignProduct',
                ];
            }
        }
//        var_dump($packet);
//        die();
        return $this->_soapCall($packet, Null, $skuCollection);

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
//        var_dump($newProds);
        foreach($newProds as $index => $fields) {
            $keys = array_keys($newProds[$index]);
            $skuCollection[] = $sku = $newProds[$index]['sku'];
            array_shift($keys);
            array_shift($newProds[$index]);
            foreach( $keys as $ind => $attFields ) {
                $attributes[$attFields] = ($attFields == 'website') ? [$newProds[$index][$attFields]] : $newProds[$index][$attFields];
            }
            $packet[$index] = array('simple', $attributeSet['set_id'], $sku, $attributes );
            $attributes = [];
        }
//        echo '<pre>';
//    var_dump($packet);
//        die();
         return $this->_soapCall($packet, 'catalog_product.create', $skuCollection);
    }

    public function insertIntoMageLog($Sku, $resource, $speed, $status)
    {
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
//        foreach( $Skus as $sku ){
            $fieldValueMap = array(
                'sku'   =>  $Sku,
                'resource'  =>  $resource,
                 'speed'  =>  $speed,
                'pushedby'   =>   $user,
                'status'    =>  $status,
            );
            $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);//'fields' => $mapping,
            $this->getEventManager()->trigger('construct_mage_log', null, array('makeFields'=>$eventWritables));
//        }
    }

} 