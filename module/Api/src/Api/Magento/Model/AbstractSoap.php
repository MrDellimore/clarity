<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/10/14
 * Time: 9:31 AM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;

class AbstractSoap {

    protected $_soapHandle;

    protected $_session;

    public function __construct($soapURL)
    {
        $this->_soapHandle = new Client($soapURL);
        $this->_session = $this->_soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
    }

    protected function _getAttributeSet()
    {
        $fetchAttributeList = [$this->_session, 'product_attribute_set.list'];
        $attributeSets = $this->_soapHandle->call('call', $fetchAttributeList);
        $attributeSet = end($attributeSets);
        return $attributeSet;
    }

    protected function _soapCall($packet, $resource, $skuCollection)
    {
//        echo '<pre>';
        $a = 0;
        $batch = $results = $status = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)) {
                if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 3 ) {
                    $batch[$x] = array($resource, $packet[$a]);
                }
                else if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 2 ) {
                    $batch[$x] = array($resource, $packet[$a]);
                } else {
                    $batch[$x] = array($resource, $packet[$a]);
                }
                $x++;
                $a++;
            }
            sleep(15);
//            var_dump($batch);
            $results[] = $this->_soapHandle->call('multiCall',array($this->_session, $batch));
        }
//        die();
//        foreach ( $results as $key => $res ) {
//            foreach ( $res as $index => $r ) {
//                if( !isset($r['faultCode']) ) {
//                    $status[] = $results;
//                } else {
//                    $status[] = $r['faultMessage'];
//                }
//            }
//        }
//        var_dump($batch);
        $totalTime = $this->stopStopwatch();
//        $this->insertIntoMageLog($skuCollection ,$resource, $totalTime);
        return $results;
    }
} 