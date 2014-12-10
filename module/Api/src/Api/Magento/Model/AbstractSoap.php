<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/10/14
 * Time: 9:31 AM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;

abstract class AbstractSoap
{

    /**
     * @var \Zend\Soap\Client
     */
    protected $_soapHandle;

    /**
     * @var mixed
     */
    protected $_session;

    /**
     * Connects to Mage and gets a session.
     * @param $soapURL
     */
    public function __construct($soapURL)
    {
        $this->_soapHandle = new Client($soapURL);
        $this->_session = $this->_soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
    }

    /**
     * Makes an API call to retrieve an attribute set.
     * @return string $attributeSet
     */
    protected function _getAttributeSet()
    {
        $fetchAttributeList = [$this->_session, 'product_attribute_set.list'];
        $attributeSets = $this->_soapHandle->call('call', $fetchAttributeList);
        $attributeSet = '';
        foreach ( $attributeSets as $aSets ) {
            if ( (int) $aSets['set_id'] === 9 ) {
                $attributeSet = $aSets['set_id'];
            }
        }
        return $attributeSet;
    }

    /**
     * This method places everything into batches. Batches of 10 Skus. So if there are 55 Skus. It will create 5 batches with 5 left over in another batch.
     * @param $packet
     * @param null $resource
     * @param array $skuCollection
     * @return array $result
     */
    protected function _soapCall($packet, $resource = Null, $skuCollection)
    {
        $a = 0;
        $batch = $batchCall = $results = $result = $status = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)) {
//                This condition is only for linked products and categories
                if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 3 ) {
                    $resource = $packet[$a]['resource'];
                    $batchCall[$x] = array($resource, $packet[$a]);
//                This condition is only for linked products and categories
                } else if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 2 ) {
                    $resource = $packet[$a]['resource'];
                    $batchCall[$x] = array($resource, $packet[$a]);
                } else {
                    $batchCall[$x] = array($resource, $packet[$a]);
                }
                $x++;
                $a++;
            }
            $batch = $batchCall;
            $batchCall = [];
            sleep(15);
//            Makes an Api call
            $results[] = $this->_soapHandle->call('multiCall',array($this->_session, $batch));
        }
//        This whole jazz can be place into its own method.
        $totalTime = $this->stopStopwatch();
        foreach ( $results as $key => $res ) {
            foreach ( $res as $index => $r ) {
                if( isset($r['faultCode']) && (int)$r['faultCode'] == 1 ) {
                    $result[$key][$index] = False;
                    if ( $key == 0 ) {
                        $this->insertIntoMageLog($skuCollection[$index] ,'Sku already exists', $totalTime, 'Fail');
                    } else {
                        $this->insertIntoMageLog($skuCollection[(int)$key.$index] ,'Sku already exists', $totalTime, 'Fail');
                    }
                } else if( isset($r['isFault']) ) {
                    $result[$key][$index] = False;
                    if ( $key == 0 ) {
                        $this->insertIntoMageLog($skuCollection[$index] ,$r['faultMessage'], $totalTime, 'Fail');
                    } else {
                        $this->insertIntoMageLog($skuCollection[(int)$key.$index] ,$r['faultMessage'], $totalTime, 'Fail');
                    }
                } else {
                    if(  isset($packet[$index]['dataState']) &&  (int)$packet[$index]['dataState'] === 3 ) {
                        $resource = $packet[$index]['resource'];
                    }
                    if(  isset($packet[$index]['dataState']) &&  (int)$packet[$index]['dataState'] === 2 ) {
                        $resource = $packet[$index]['resource'];
                    }
                    $result[$key][$index] = $results[$key][$index];
                    if ( $key == 0 ) {
                        $this->insertIntoMageLog($skuCollection[$index] ,$resource, $totalTime, 'Success');
                    } else {
                        $this->insertIntoMageLog($skuCollection[(int)$key.$index] ,$resource, $totalTime, 'Success');
                    }
                }
            }
        }
        return $result;
    }
} 