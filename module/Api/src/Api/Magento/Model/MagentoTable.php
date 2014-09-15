<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\Platform\Mysql;

class MagentoTable {

    use EventManagerAwareTrait;

    protected $adapter;

    protected $totaltime;

    protected $sql;

    protected $dirtyCount;

    protected $attributeDirtyCount = 0;

    protected $dirtyItems;

    protected $imgPk = array();

    /*$catalogInventoryStockItemUpdateEntity*/
    protected $stockData  = [
        'qty'=>'qty',
        'is_in_stock'=> 'is_in_stock',
    ];

    use Spex;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function fetchImages()
    {

        return $this->productAttribute($this->sql,array(),array('dataState'=>2),'images')->toArray();
    }

    public function fetchCleanCount()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->where(array( 'dataState' => '0'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    public function fetchNewCount()
    {
        $select = $this->sql->select();
        $select->from('product');
//        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'modifieddate', 'item' => 'productid'));
        $select->where(array( 'dataState' => '2'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $cleanCount = $resultSet->count();
        return $resultSet->count();
    }

    public function fetchImageCount()
    {
        $select = $this->sql->select()->from('productattribute_images')->where(['dataState'=>2]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    public function fetchChangedProducts()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));

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
//        TODO have to add my trait for product attribute look up to select table type attribute id and attribute code.
//        TODO from there I would use the table type to access each table using the attribute id.
//        $columns = array('dataType'=>'backend_type','attributeId'=>'attribute_id','attributeCode'=>'attribute_code');

        $results = $this->productAttributeLookup($this->sql);
//        $dataType = $results[0]['dataType'];
//        $attributeId = $results[0]['attributeId'];
//        $attributeCode = $results[0]['attributeCode'] === 'name' ? 'title' : $results[0]['attributeCode'];
        foreach($results as $key => $attributes){
            $dataType = $attributes['dataType'];
            $attributeId = $attributes['attId'];
            $attributeCode = $attributes['attCode'] === 'name' ? 'title' : $attributes['attCode'];
            $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
            if(is_array($newAttribute)){
                foreach($newAttribute as $newAtt){
                    $result[] = $newAtt;
                }
            }
        }

//        $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Title
//        $newAttribute = $this->fetchAttribute( 'varchar','96','title');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Price
//        $newAttribute = $this->fetchAttribute( 'decimal','99','price');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Inventory
//        $newAttribute = $this->fetchAttribute( 'int','1','Inventory');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as  $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Status
//        $newAttribute = $this->fetchAttribute( 'int','273','Status');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch URLkey
//        $newAttribute = $this->fetchAttribute( 'varchar','481','url_key');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Cost
//        $newAttribute = $this->fetchAttribute( 'decimal','100','cost');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Rebate Price
//        $newAttribute = $this->fetchAttribute( 'decimal','1590','rebate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Mail in Rebate Price
//        $newAttribute = $this->fetchAttribute( 'decimal','1593','mailinRebate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Special Price
//        $newAttribute = $this->fetchAttribute( 'decimal','567','specialPrice');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Special Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','568','specialEndDate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Special End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','569','specialStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Rebate Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1591','rebateEndDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Rebate End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1592','rebateStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Mail in Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1594','mailinEndDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Mail in  End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1595','mailinStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch metaTitle
//        $newAttribute = $this->fetchAttribute( 'varchar','103','meta_title');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch metaDescription
//        $newAttribute = $this->fetchAttribute( 'varchar','105','meta_description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Description
//        $newAttribute = $this->fetchAttribute( 'text','97','description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch inBox
//        $newAttribute = $this->fetchAttribute('text','1633','inBox');
//        // die(print_r($newAttribute);
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch includesFree
//        $newAttribute = $this->fetchAttribute( 'text','1679','includesFree');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Short Description
//        $newAttribute = $this->fetchAttribute( 'text','506','short_description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//               $result[] = $newAtt;
//            }
//        }
        $this->setDirtyItems($this->getDirtyCount(), $this->getAggregateAttributeDirtyCount());
        return $result;
    }

    public function setDirtyItems($dirtyProducts, $dirtyAttributes)
    {
        $this->dirtyItems = $dirtyProducts + $dirtyAttributes;
    }

    public function getDirtyItems()
    {
        return $this->dirtyItems;
    }


    public function getAggregateAttributeDirtyCount()
    {
        return $this->attributeDirtyCount;
    }

    public function setAggregateAttributeDirtyCount($attributeDirtyCount)
    {
        $this->attributeDirtyCount += $attributeDirtyCount;
    }

    public function fetchLinkedProducts()
    {
        $select = $this->sql->select();
        $filter = new Where();
        $filter->in('productlink.dataState',array(2,3));
        $select->from('productlink')
            ->columns(array('entityId'=>'entity_id','linkedEntityId'=>'linked_entity_id', 'dataState'=>'dataState'))
            ->join( array('t'=>'productlink_type'), 't.link_type_id=productlink.link_type_id',array('type'=>'code'))
//               ->join( array('p'=>'product'), 'p.entity_id=productlink.entity_id',array('sku'=>'productid'))
//               ->where(array('productcategory.dataState'=>2,'productcategory.dataState'=>3),PredicateSet::OP_OR);
            ->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
//        $resultSet->count()
        return $resultSet->toArray();
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

    public function updateLinkedProductstoClean($linkedProducts)
    {
        $result ='';
        foreach($linkedProducts as $key => $fields){
            $dataState = (int)$linkedProducts[$key]['dataState'];
            if( $dataState === 2){
                $update = $this->sql->update('productlink');
                $update->set(array('dataState'=>0))
                    ->where(array('entity_id'=>$linkedProducts[$key]['entityId'], 'linked_entity_id'=>$linkedProducts[$key]['linkedEntityId']));
                $statement = $this->sql->prepareStatementForSqlObject($update);
                $result = $statement->execute();
            } else {
                $delete = $this->sql->delete('productlink');
                $delete->where(array('entity_id'=>$linkedProducts[$key]['entityId'], 'linked_entity_id'=>$linkedProducts[$key]['linkedEntityId']));
                $statement = $this->sql->prepareStatementForSqlObject($delete);
                $result = $statement->execute();
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
        $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $select->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $this->setAggregateAttributeDirtyCount($resultSet->count());
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


    public function soapMedia($media = array())
    {
        $packet = [];
//        if(!is_array($media)) {
//            throw new \InvalidArgumentException(
//                sprintf("Bad argument in class %s for function %s in line %s.",__CLASS__, __FUNCTION__, __LINE__)
//            );
//        }
        $soapHandle = new Client(SOAP_URL);
        $session = $soapHandle->call('login',[SOAP_USER, SOAP_USER_PASS]);
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
            $sku = $products[0]['sku'];
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
        $results = [];
        $a = 0;
        $batch = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                $batch[$x] = array('catalog_product_attribute_media.create', $packet[$a]);
                $x++;
                $a++;
            }
            sleep(15);
            $results[] = $soapHandle->call('multiCall',array($session, $batch));
        }
        return $results;
    }

    public function fetchCategoriesSoap()
    {
        $select = $this->sql->select();
        $filter = new Where();
        $filter->in('productcategory.dataState',array(2,3));
        $select->from('productcategory')
               ->columns(array('entityId'=>'entity_id','categortyId'=>'category_id', 'dataState'=>'dataState'))
               ->join( array('p'=>'product'), 'p.entity_id=productcategory.entity_id',array('sku'=>'productid'))
//               ->where(array('productcategory.dataState'=>2,'productcategory.dataState'=>3),PredicateSet::OP_OR);
               ->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
//        $resultSet->count()
        return $resultSet->toArray();
    }

    public function soapCategoriesUpdate($categories)
    {
        $result = false;
        $soapHandle = new Client(SOAP_URL);
        $packet = array();
        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        foreach($categories as $key => $fields){
            $entityId = $categories[$key]['entityId'];
            $sku = $categories[$key]['sku'];
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

    public function groupProducts($changedProds)
    {
        echo '<pre>';
//        var_dump($changedProds);
        $soapBundle = [];
        foreach ( $changedProds as $key => $prods) {
            $sku = $prods['item'];
            echo $changedProds[$key]['item']. ' ' . $changedProds[$key++]['item'] . '<br />';
            if( $changedProds[$key]['item'] ==  $changedProds[$key++]['item'] ) {
//                echo 'haha';
            }

            array_shift($prods);
            $attributeCode = (current(array_keys($prods)) === 'title') ? 'name' : current(array_keys($prods));
            $attributeValue = current($prods);
            $soapBundle[$key]['sku'] = $prods['item'];
            $soapBundle[$key][$attributeCode] =$attributeValue;
        }
        var_dump($soapBundle);

        die();
    }

    public function soapUpdateProducts($data)
    {
        $soapHandle = new Client(SOAP_URL);
        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $i = 0;
        $packet = [];
        $results = [];
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
        $a = 0;
        $batch = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                $batch[$x] = array('catalog_product.update', $packet[$a]);
                $x++;
                $a++;
            }
            sleep(15);
            $results[] = $soapHandle->call('multiCall',array($session, $batch));
            $this->insertIntoMageLog($skuCollection ,'catalog_product.update');
        }
        return $results;
    }

    public function updateImagesToClean($images)
    {
        $result ='';
        foreach($images as $image){
            $update = $this->sql->update('productattribute_images')->set(['dataState'=>0])->where(['value_id'=>$image['value_id']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
        }
        return $result;
    }

    public function updateProductCategoriesToClean($catsToUpdate)
    {
        $result ='';
        foreach($catsToUpdate as $key => $fields){
            $dataState = (int)$catsToUpdate[$key]['dataState'];
            if( $dataState === 2){
                $update = $this->sql->update('productcategory');
                $update->set(array('dataState'=>0))
                       ->where(array('entity_id'=>$catsToUpdate[$key]['entityId'], 'category_id'=>$catsToUpdate[$key]['categortyId']));
                $statement = $this->sql->prepareStatementForSqlObject($update);
                $result = $statement->execute();
            } else {
                $delete = $this->sql->delete('productcategory');
                $delete->where(array('entity_id'=>$catsToUpdate[$key]['entityId'], 'category_id'=>$catsToUpdate[$key]['categortyId']));
                $statement = $this->sql->prepareStatementForSqlObject($delete);
                $result = $statement->execute();
            }
        }
        return $result;
    }

    public function updateToClean($data)
    {
        $result = '';
        foreach($data as $key => $value){
            //this sku part might have to be refactored
                if(array_key_exists('sku', $data[$key])){
                    $update = $this->sql->update();
                    $update->table('product');
                    $update->set(array('dataState'=>'0'));
                    $update->where(array('productid'=>$data[$key]['sku']));
                    $statement = $this->sql->prepareStatementForSqlObject($update);
                    $result = $statement->execute();
                    $resultSet = new ResultSet;
                    if ($result instanceof ResultInterface && $result->isQueryResult()) {
                        $resultSet->initialize($result);
                    }
                } else {
                    $entityId = $data[$key]['id'];
//                        $sku = $data[$key]['item'];
                    array_shift($data[$key]);
                    $attributeField = current(array_keys($data[$key]));
                    $attributeField = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',$attributeField  ));

//                    $columns = array('attributeId' => 'attribute_id', 'backendType' => 'backend_type');
                    $where = array('attribute_code' => ($attributeField == 'title') ? 'name' : $attributeField);
                    $results = $this->productAttributeLookup($this->sql, $where);
                    $attributeId = $results[0]['attId'];
                    $tableType = $results[0]['dataType'];
                    $set = array('dataState'=>'0');
                    $where = array('entity_id'=>$entityId, 'attribute_id'=>$attributeId);
                    $result = $this->productUpdateaAttributes($this->sql, $tableType, $set, $where);
                }
        }
        return $result;
    }

    public function soapAddProducts($newProds)
    {
//        echo '<pre>';
        $results = [];
        $packet = [];
        $soapHandle = new Client(SOAP_URL);
        $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $fetchAttributeList = [$session, 'product_attribute_set.list'];
        $attributeSets = $soapHandle->call('call', $fetchAttributeList);
        $attributeSet = current($attributeSets);
//        $set = array(
//            'name'    =>  '11" MB Air Kate Spade Dots',
//            'description'   =>  'Kate Spade MacBook Air Slip Sleeve 11" Dots/Polyurethane',
//        );
//        $packet = [$session, 'catalog_product.create', ['simple', $attributeSet['set_id'], '031460', $set]];
//        try{
//            $results = $soapHandle->call('call', $packet );
//        } catch (\SoapFault $e){
//            trigger_error($e->getMessage(), E_USER_ERROR ); //should possibly go in log file?
//            $results = $e->getCode(); //should be return to controller?
//        }
//        return $results;

//        $count = 0;
        $set = [];
        foreach($newProds as $index => $fields){
            $keys = array_keys($newProds[$index]);
            $sku = $newProds[$index]['sku'];
            array_shift($keys);
            array_shift($newProds[$index]);
            $packetCount = 0;
            foreach($keys as $ind => $attFields){
                $set[$packetCount] = [
                    $keys[$ind]   =>  $keys[$ind] == 'website' ? [$newProds[$index][$keys[$ind]]] : $newProds[$index][$keys[$ind]],
                ];
                $packetCount++;
            }
//            $packet[$index] = array('simple', $sku, $set);
            $packet[$index] = array('simple', $attributeSet['set_id'], $sku, $set);
        }

        $a = 0;
        $batch = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                $batch[$x] = array('catalog_product.create',$packet[$a]);
                $x++;
                $a++;
            }
//            echo '<pre>';
//var_dump($batch);
            sleep(15);
            try {
                $results[] = $soapHandle->call('multiCall',array($session,$batch));
            } catch ( \SoapFault $e ) {
                error_log( $e->getMessage() . ' ' . $e->getCode() . ': Sku already exists in Mage Database.' );
            }
        }
        return $results;
    }

    public function fetchNewItems()
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = $optionValues = [];
        $select = $this->sql->select()->from('product')->columns([
            'entityId'  =>  'entity_id',
            'sku'   =>  'productid',
            'productType'  =>  'product_type',
            'website'   =>  'website',
            'dateCreated'   =>  'creationdate',
        ])->where(array('product.dataState'=>2))->quantifier(Select::QUANTIFIER_DISTINCT);
        $statusIntJoin = new Expression('i.entity_id = product.entity_id and i.attribute_id = 273');
        $select->join(['i'=>'productattribute_int'],$statusIntJoin ,['status'=>'value'] ,Select::JOIN_LEFT);
//        $statusOptionJoin = new Expression('o.attribute_id = i.attribute_id and o.value = i.option');
//        $select->join(['o'=>'productattribute_option'],$statusOptionJoin ,['Status'=>'value'] ,Select::JOIN_LEFT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
//        $productSku = [
//            '8404B002','9128B002','9128B016','9521B002','9522B002','9543B001','9546B001','9547B001','9770B001'
//        ];
//        $productSku = [
//            'ATIINSTANTLABK1','ATIP2786IMPOSSIBLEK1','ATIP2785IMPOSSIBLEK2','ATIINSTANTLABK2','ATIP2786IMPOSSIBLEK2','ATIP3107IMPOSSIBLEK1','ATIP2785IMPOSSIBLEK3','ATIINSTANTLABK3','51-0816',
//            'COMP-6SAFTX4','HS-B250XT','URC-LOG880','3041-EFESTX2','1086-EFESTX2','3245-EFESTX2','3042-EFEST','4066-EFESTX2','3164-EFESTX2','4084-EFESTX2','AEFEIMR18350P10K2','3164-EFEST','3245-EFEST',
//            '1086-EFEST','3041-EFEST','4066-EFEST','4084-EFEST','3891-EFEST','AEFEIMR18350P10K1','WP812B','KODAAENERG12','TV434','ALEXLSD16GCTBK1','1163-MACK','COMP-4SAFTX2','COMP-4SAFTX4','COMP-4SAFT',
//            'COMP-4SAFTX5','COMP-4SAFTX10','COMP-4SAFTX25','ASLICBHK1','FILS49','SUR6277','09064','TH800667','AVORD5012K1','AVORVNQ1026K1','AWES2331K1','1365-659','SH0BZ'
//        ];

//        $skuCount = count($productSku);
        $startCount = 0;
//        for( $i = 0; $i < $skuCount; $i++){
            foreach($products as $index => $value) {
//                if($productSku[$i] == $value['sku']) {
                    $entityId = $products[$index]['entityId'];
                    $attributes = $this->productAttributeLookup($this->sql);
                    foreach( $attributes as $key => $attribute ) {
                        $tableType = (string)$attribute['dataType'];
                        $attributeId = (int)$attribute['attId'];
                        $attributeCode = $attribute['attCode'];
                        $selectAtts = $this->sql->select()->from('productattribute_'. $tableType)->columns([$attributeCode=>'value', 'attId'=>'attribute_id'])->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2]);
                        $attStatement = $this->sql->prepareStatementForSqlObject($selectAtts);
                        $attResult = $attStatement->execute();
                        $attSet = new ResultSet;
                        if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                            $attSet->initialize($attResult);
                        }
                        $attributeValues = $attSet->toArray();
//                        $attributeValues = $this->productAttribute($this->sql, [$attributeCode=>'value', 'attId'=>'attribute_id'],['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2],$tableType)->toArray();
                        foreach($attributeValues as $keyValue => $valueOption) {
//                        $soapBundle[$index]['entityId'] = $entityId;
//                        $soapBundle[$index]['dataState'] = $products[$index]['dataState'];
//                        $soapBundle[$index]['entityId'] = $products[$index]['entityId'];
                            $soapBundle[$startCount]['sku'] = $products[$index]['sku'];
                            $soapBundle[$startCount]['website'] = $products[$index]['website'];
                            $soapBundle[$startCount]['status'] = (is_null($products[$index]['status'])) ? 2 : $products[$index]['status'];
                            if ( array_key_exists($attributeCode,$this->stockData) ) {
                                $soapBundle[$startCount]['stock_data'][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                            } else {
                                $soapBundle[$startCount][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                            }

        //                        $optionID = $attributeValues[$keyValue][$attributeCode];
        //                        echo $optionID . ' ';
        //                        echo $attributeValues[$keyValue]['manufacturer']. ' ' ;
        //                        echo $attributeId. ' ' ;
        //                        $optionSelect = $this->sql->select()->from('productattribute_option')->columns([$attributeCode=>'value'])->where(['option_id'=>$optionID, 'attribute_id'=>$attributeId]);
        //                        $optionStatement =  $this->sql->prepareStatementForSqlObject($optionSelect);
        //                        $optionResult = $optionStatement->execute();
        //                        $resultOpSet = new ResultSet;
        //                        if ($optionResult instanceof ResultInterface && $optionResult->isQueryResult()) {
        //                            $resultOpSet->initialize($optionResult);
        //                        }

        //                        $optionValues = $resultOpSet->toArray();

        //                        $optionValues = $this->productAttribute($this->sql, [$attributeCode=>'value'],['option_id'=>$optionID, 'attribute_id'=>$attributeId] ,'option')->toArray();
        //                        foreach( $optionValues as $keys => $option) {
        //                            foreach( $option as $ind => $value ){
        //                                $soapBundle[$index][$ind] = $option[$ind];
        ////                                echo $option[$ind] . ' ' ;
        //                            }
        //                        }

                        }
                    }
                    $startCount++;//starCount was here
//                }
//            }
        }
//        echo '<pre>';
//        var_dump($soapBundle);
//        die();
        return $soapBundle;
    }

    public function updateProduct($oldEntity, $newEntity )
    {
        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$newEntity, 'dataState'=>0 ])->where(['productid'=>$oldEntity['sku']]);
        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
        $response = $prdStmt->execute();
        return $response;
    }

    public function updateProductAttribute($oldEntity, $newEntity )
    {
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id'])->where(['productid'=>$oldEntity['sku']]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $response = $statement->execute();
        $resultSet = new ResultSet;
        if ($response instanceof ResultInterface && $response->isQueryResult()) {
            $resultSet->initialize($response);
        }
        $oEntityId = $resultSet->toArray();
        $oeid = $oEntityId[0]['entityId'];
        array_shift($oldEntity);
        array_shift($oldEntity);
        array_shift($oldEntity);
        foreach( $oldEntity as $attributeCode => $attributeValue ) {
            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
            echo '<pre>';
//            var_dump($lookupVals);
            if( !empty($lookupVals[0]) ) {
//                var_dump($lookupVals);
                $attributeId = $lookupVals[0]['attId'];
                $dataType = $lookupVals[0]['dataType'];
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$newEntity, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oeid]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $attributeResp = $stmt->execute();
//                echo '--------------<br />';
//                $attSet = new ResultSet;
//                if ($attResponse instanceof ResultInterface && $attResponse->isQueryResult()) {
//                    $attSet->initialize($attResponse);
//                }
//                $attributeValues = $attSet->toArray();
            }
//            if( $dataType == 'int' ) {
//                $option = $this->sql->select()->from('productattribute_'.$dataType)->columns(['option'=>'value'])->where(['entity_id'=>$oeid,'attribute_id'=>$attributeId]);
//                $opStmt = $this->sql->prepareStatementForSqlObject($option);
//                $opResp = $opStmt->execute();
//                $opSet = new ResultSet;
//                if ($opResp instanceof ResultInterface && $opResp->isQueryResult()) {
//                    $opSet->initialize($opResp);
//                }
//                $op = $opSet->toArray();
//                if( !empty($op) ) {
//                    $opUpdate = $this->sql->update('productattribute_option')->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'option_id'=>$op[0]['option']]);
//                    $opStmt = $this->sql->prepareStatementForSqlObject($opUpdate);
//                    $opStmt->execute();
//                }
//            }
        }
        return $attributeResp;
    }

    public function updateNewProduct( $oldEntity, $newEntity )
    {
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id'])->where(['productid'=>$oldEntity['sku']]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $response = $statement->execute();
        $resultSet = new ResultSet;
        if ($response instanceof ResultInterface && $response->isQueryResult()) {
            $resultSet->initialize($response);
        }
        $oEntityId = $resultSet->toArray();
        $oeid = $oEntityId[0]['entityId'];
        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$newEntity, 'dataState'=>0 ])->where(['productid'=>$oldEntity['sku']]);
        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
        $response = $prdStmt->execute();
        array_shift($oldEntity);
        array_shift($oldEntity);
        array_shift($oldEntity);
        foreach( $oldEntity as $attributeCode => $attributeValue ) {
            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
//            echo '<pre>';
//            var_dump($lookupVals);
            if( !empty($lookupVals[0]) ) {
//                var_dump($lookupVals);
                $attributeId = $lookupVals[0]['attId'];
                $dataType = $lookupVals[0]['dataType'];
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$newEntity, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oeid]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $attributeResp = $stmt->execute();
//                echo '--------------<br />';
//                $attSet = new ResultSet;
//                if ($attResponse instanceof ResultInterface && $attResponse->isQueryResult()) {
//                    $attSet->initialize($attResponse);
//                }
//                $attributeValues = $attSet->toArray();
            }
//            if( $dataType == 'int' ) {
//                $option = $this->sql->select()->from('productattribute_'.$dataType)->columns(['option'=>'value'])->where(['entity_id'=>$oeid,'attribute_id'=>$attributeId]);
//                $opStmt = $this->sql->prepareStatementForSqlObject($option);
//                $opResp = $opStmt->execute();
//                $opSet = new ResultSet;
//                if ($opResp instanceof ResultInterface && $opResp->isQueryResult()) {
//                    $opSet->initialize($opResp);
//                }
//                $op = $opSet->toArray();
//                if( !empty($op) ) {
//                    $opUpdate = $this->sql->update('productattribute_option')->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'option_id'=>$op[0]['option']]);
//                    $opStmt = $this->sql->prepareStatementForSqlObject($opUpdate);
//                    $opStmt->execute();
//                }
//            }
        }
//        var_dump($attributeResp);
        return $attributeResp;
    }

//    public function validateSkuExists($newEntityIds, $oldEntityIds)
//    {
//        foreach($newEntityIds as $index => $newEIds ) {
//            foreach( $newEIds as $key => $EntityId ) {
//                $select = $this->sql->select()->from('product')->where(['entity_id'=>$EntityId]);
//                $statement = $this->sql->prepareStatementForSqlObject($select);
//                $response = $statement->execute();
//                $resultSet = new ResultSet;
//                if ($response instanceof ResultInterface && $response->isQueryResult()) {
//                    $resultSet->initialize($response);
//                }
//                $id = $resultSet->toArray();
//                if( count($id) ) {
//                    $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
//                    foreach( $entityId as $eid ) {
//                        foreach( $eid as $maxEntityID ) {
//                            $newEntityId = $maxEntityID + 1;
//                            $response = $this->updateNewProduct( $oldEntityIds[$key], $newEntityId );
////                            $attResponse = $this->updateProductAttribute( $oldEntityIds[$key], $newEntityId );
////                            $prodResponse = $this->updateProduct( $oldEntityIds[$key], $newEntityId );
//                        }
//                    }
//                }
//                else {
//                    $response = $this->updateNewProduct( $oldEntityIds[$key], $EntityId );
//
////                    $attResponse = $this->updateProductAttribute($oldEntityIds[$key], $EntityId);
////                    $prodResponse = $this->updateProduct($oldEntityIds[$key], $EntityId);
//                }
//            }
//        }
//        return true;
//    }

    public function validateSkuExists($newProducts ,$mageEntityId)
    {
        $dupEntityIdExists = $this->sql->select()->from('product')->where(['entity_id'=>$mageEntityId]);
        $dupStatement = $this->sql->prepareStatementForSqlObject($dupEntityIdExists);
        $dupResponse = $dupStatement->execute();
        $dupSet = new ResultSet;
        if ($dupResponse instanceof ResultInterface && $dupResponse->isQueryResult()) {
            $dupSet->initialize($dupResponse);
        }
        $id = $dupSet->toArray();
        if( count($id) ) {
            $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
            foreach( $entityId as $eid ) {
                foreach( $eid as $maxEntityID ) {
                    $newEntityId = $maxEntityID + 1;
                    $response = $this->updateNewProduct( $newProducts, $newEntityId );
                }
            }
        } else {
            $response = $this->updateNewProduct($newProducts, $mageEntityId);
        }
        return $response;
    }

    public function updateNewItemsToClean($newProducts, $mageEntityId)
    {
//        $mageEntityIds = [];
//        foreach ($newEntityIds as $key => $entityId ) {
//            foreach( $entityId as $ind => $id ){
//                $mageEntityIds[$ind] = $entityId[$ind];
//            }
//        }
//        $oldEntityIds = [];
//        foreach( $newProducts as $key => $acode ) {
//            foreach( $acode as $index => $aValues ) {
//                if($index == 'stock_data') {
//                    if( isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
//                        $oldEntityIds[$key][current(array_keys($this->stockData))] = $newProducts[$key]['stock_data'][current(array_keys($this->stockData))];
//                        array_shift($this->stockData);
//                    }
//                } else {
//                    $oldEntityIds[$key][$index] = $newProducts[$key][$index];
//                }
//            }
//        }

//        return $this->validateSkuExists($newEntityIds, $oldEntityIds);
        return $this->validateSkuExists($newProducts, $mageEntityId);
    }

    public function adjustProductKeys($newProducts)
    {
        foreach( $newProducts as $key => $acode ) {
            foreach( $acode as $index => $aValues ) {
                if($index == 'stock_data') {
                    if( isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
                        $oldEntityIds[$key][current(array_keys($this->stockData))] = $newProducts[$key]['stock_data'][current(array_keys($this->stockData))];
                        array_shift($this->stockData);
                    }
                } else {
                    $oldEntityIds[$key][$index] = $newProducts[$key][$index];
                }
            }
        }
        return $oldEntityIds;
    }

}