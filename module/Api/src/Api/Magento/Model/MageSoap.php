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
use Zend\EventManager\EventManagerAwareTrait;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;


class MageSoap extends AbstractSoap
{

    /**
     * Trait
     */
    use EventManagerAwareTrait;

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var int
     */
    protected $totaltime;

    /**
     * @var array
     */
    protected $imgPk = array();

    /**
     * @var int
     */
    protected $_startTime;

    /**
     * @var int
     */
    protected $_stopTime;

    /**
     * @var int
     */
    protected $_totalTime;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        parent::__construct(SOAP_URL);
    }

    /**
     * I created this method as the start of a start of a stop watch. I could have probably used Symfony 2's stopwatch.
     * http://symfony.com/doc/current/components/stopwatch.html
     */
    public function startStopwatch()
    {
        $mtime = microtime() ;
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->_startTime = $mtime;
    }

    /**
     * This method is when the runner push stop on his stopwatch.
     * @return bool|string
     */
    public function stopStopwatch()
    {
        $mtime = microtime() ;
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->_stopTime = $mtime;
        $this->_totalTime = (int)$this->_stopTime-(int)$this->_startTime;
        return date("H:i:s", $this->_totalTime);
    }

    /**
     * This method the attributes and places them in a packet to be sent to magento.
     * I'm grabbing the sku here for logging. In the Mage Logs I want to notify the user what Sku was push to Mage for the update.
     * The property index comes from table-managed.js in the updateMageItems function.
     * @param $changedProducts
     * @return array
     */
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
//                        This condition was for stock_status. But since we're doing qty/inventory every hour through sellercloud we dont need this here. But I'll keep it for
//                        some future case.
//                        if ( is_array($attribute) ) {
//                            foreach ( $attribute as $mageAttribute => $realAttribute ) {
//                                $atts[$mageAttribute][$realAttribute] = $newValue;
//                            }
//                        } else {
//                              This condition can probably be taken out but should be tested first. Leaving line 121 would probably be the only statement that should be left.
                            if ( !(strcmp($newValue,strip_tags( $newValue ) ) === 0) ) {
                                $atts[$attribute] = html_entity_decode ($newValue );
                            } else {
                                $atts[$attribute] = $newValue;
                            }
//                        }
                    }
                }
                $packet[$key] = array('entity_id' => (int)$entityID, $atts);
            }
//            This had to re-create this array to flush it. That is to say, I had to empty that contents so it would not aggregate to the succeeding iteration.
            $atts = [];
        }
        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
    }

    /**
     * Create a packet for Related Products for a soap call to Mage.
     * @param $linkedProds
     * @return array
     */
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
        return $this->_soapCall($packet, null, $skuCollection);
    }

    /**
     * Create a packet for New Images for existing Skus in Mage. For more information check out:
     * http://www.magentocommerce.com/api/soap/catalog/catalogProductAttributeMedia/catalog_product_attribute_media.create.html
     * @param array $media
     * @return array
     */
    public function soapMedia($media = array())
    {
        $packet = $skuCollection = [];
        $this->startStopwatch();
        foreach($media as $key => $imgFile) {
//            this will change to whatever cdn we will have.
//                $imgDomain = $media[$key]['domain'];
            $imgName = $imgFile['filename'];
            $this->imgPk[] = $imgFile['imageid'];
            $entityId = $imgFile['id'];
            $imgPath = file_get_contents("public".$imgName);
            $fileContentsEncoded = base64_encode($imgPath);
            $file = array(
                'content'   =>  $fileContentsEncoded,
                'mime'  =>  'image/jpeg',
            );
            $skuCollection[] = $sku = $imgFile['sku'];
            $packet[$key] = [
                $sku,
                [
                    'file'  =>   $file,
                    'label' =>  $imgFile['label'],
                    'position'  => $imgFile['position'],
                    'excludes'  =>  0,
                    'remove'    =>  0,
                    'disabled'  =>  0,
                ]
            ];
        }
        return $this->_soapCall($packet, 'catalog_product_attribute_media.create', $skuCollection);
    }

    /**
     * Creates a packet for Categories to be sent over to Mage.
     * @param $categories
     * @return array
     */
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
        return $this->_soapCall($packet, Null, $skuCollection);
    }


    /**
     * Creates a packet for product attributes that have changed.
     * @param $changedProds
     * @return array
     */
    public function soapChangedProducts($changedProds)
    {
        $packet = [];
        $skuCollection = [];
        $attributes = [];
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
        return $this->_soapCall($packet, 'catalog_product.update', $skuCollection);
    }

    /**
     * Creates a packet for new products to be sent over to Mage.
     * @param $newProds
     * @return array
     */
    public function soapAddProducts($newProds)
    {
        $packet = [];
        $attributeSet = $this->_getAttributeSet();
        $skuCollection = [];
        $attributes = [];
        foreach( $newProds as $index => $fields ) {
            $keys = array_keys($fields);
            $skuCollection[] = $sku = $fields['sku'];
            array_shift($keys);
            array_shift($keys);
            array_shift($fields);
            array_shift($fields);
            foreach( $keys as $ind => $attFields ) {
                $attributes[$attFields] = ($attFields == 'website') ? [$newProds[$index][$attFields]] : $newProds[$index][$attFields];
            }
            $packet[$index] = array('simple', $attributeSet, $sku, $attributes );
            $attributes = [];
        }
         return $this->_soapCall($packet, 'catalog_product.create', $skuCollection);
    }

    /**
     * When a soap call is made to Mage, it will be recorded and sent to this method for persistence. It is a Mage Api History Logger.
     * @param $Sku
     * @param $resource
     * @param $speed
     * @param $status
     */
    public function insertIntoMageLog($Sku, $resource, $speed, $status)
    {
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        if( is_null($user) ) {
            $user = 'Console';
        }
//        foreach( $Skus as $sku ){
        $fieldValueMap = array(
            'sku'       =>  $Sku,
            'resource'  =>  $resource,
            'speed'     =>  $speed,
            'pushedby'  =>   $user,
            'status'    =>  $status,
        );
        $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);
        $this->getEventManager()->trigger('construct_mage_log', null, array('makeFields'=>$eventWritables));
//        }
    }

} 