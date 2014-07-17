<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Soap\AutoDiscover;


class MagentoTable {

    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function soapContent()
    {
        $autodiscover = new AutoDiscover();
        $autodiscover->setClass(__CLASS__)
                     ->setUri('https://www.focuscamera.com/index.php/api/soap/index/?wsdl')
    }

} 