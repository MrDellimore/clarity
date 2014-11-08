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

    //    public function soapUpdateProducts($changedProducts)
//    {
//        $this->startStopwatch();
//        $packet = $skuCollection = $atts = [];
//        foreach( $changedProducts as $key => $attributes ) {
//            $entityID = $attributes['id'];
//            array_shift($attributes);
//
//            foreach( $attributes as $ind => $attrib ) {
//                $skuCollection[] = $attrib['sku'];
//                $newValue = $attrib['newValue'];
//                foreach( $attrib as $prop => $attribute ) {
//                    if ( $prop == 'property' ) {
//                        $atts[$attribute] = $newValue;
//                    }
//                }
//                $packet[$key] = array('entity_id' => $entityID, $atts);
//            }
//            $atts = [];
//        }
//        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
//    }

    public function soapUpdateProducts($changedProducts)
    {
        $this->startStopwatch();
        $packet = $skuCollection = $atts = $realAttribute = [];
        foreach( $changedProducts as $key => $attributes ) {
            $entityID = $attributes['id'];
            array_shift($attributes);
            foreach( $attributes as $ind => $attrib ) {
                $skuCollection[] = $attrib['sku'];
                $newValue = $attrib['newValue'];
                foreach( $attrib as $prop => $attribute ) {
                    if ( $prop == 'property' ) {
                        if ( is_array($attribute) ) {
                            foreach ( $attribute as $mageAttribute => $realAttribute ) {
                                $atts[$mageAttribute][$realAttribute] = $newValue;
                            }
                        } else {
                            $atts[$attribute] = $newValue;
                        }
                    }
                }
                $packet[$key] = array('entity_id' => $entityID, $atts);
            }
            $atts = [];
        }
//var_dump($packet);
//die();
        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
    }

    public function soapLinkedProducts($linkedProds)
    {
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
        $packet = $skuCollection = [];
        $this->startStopwatch();
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
        $packet = $skuCollection = array();
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
    }


    public function soapChangedProducts($changedProds)
    {
        $packet = [];
        $skuCollection = [];
        $attributes = [];
        //var_dump($changedProds);
        foreach( $changedProds as $index => $fields ) {
            $keys = array_keys($fields);
            $skuCollection[] = $sku = $fields['sku'];
            $entityID = $fields['id'];
            array_shift($keys);
            array_shift($keys);
            array_shift($fields);
            array_shift($fields);
            foreach( $keys as $ind => $attFields ) {
                $attributes[$attFields] = ($attFields == 'website') ? [$changedProds[$index][$attFields]] : $changedProds[$index][$attFields];
            }
            $packet[$index] = array('entity_id' => $entityID, $attributes);
            $attributes = [];
        }
//        echo '<pre>';
//        var_dump($packet);
//        die();
        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
    }



    public function soapAddProducts($newProds)
    {
        $packet = [];
        $attributeSet = $this->_getAttributeSet();
        $skuCollection = [];
        $attributes = [];
        foreach( $newProds as $index => $fields ) {
            $keys = array_keys($fields);
            $skuCollection[] = $sku = $fields['sku'];
//            array_shift($keys);
            array_shift($keys);
            array_shift($keys);
//            array_shift($fields);
            array_shift($fields);
            array_shift($fields);
            foreach( $keys as $ind => $attFields ) {
                $attributes[$attFields] = ($attFields == 'website') ? [$newProds[$index][$attFields]] : $newProds[$index][$attFields];
            }
            $packet[$index] = array('simple', $attributeSet, $sku, $attributes );
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
//        if( is_null($user) ) {
//            $user = 'Console';
//        }
//        foreach( $Skus as $sku ){
            $fieldValueMap = array(
                'sku'       =>  $Sku,
                'resource'  =>  $resource,
                'speed'     =>  $speed,
                'pushedby'  =>   $user,
                'status'    =>  $status,
            );
            $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);//'fields' => $mapping,
            $this->getEventManager()->trigger('construct_mage_log', null, array('makeFields'=>$eventWritables));
//        }
    }

} 