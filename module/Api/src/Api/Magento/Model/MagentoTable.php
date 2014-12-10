<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate;
use Content\ContentForm\Model\ProductsTable;

class MagentoTable {

    /**
     * Traits
     */
    use EventManagerAwareTrait, Spex;

    /**
     * @var \Zend\Db\Adapter\Adapter object
     */
    protected $adapter;

    /**
     * @var \Zend\Db\Sql\Sql object
     */
    protected $sql;

    /**
     * Part of catalogInventoryStockItemUpdateEntity in API.
     * is_in_stock is not really being used. I thought it might be at some point.
     * I'll leave it for now in case it is.
     * @var array
     */
    protected $stockData  = [
        'qty'=>'qty',
        'is_in_stock'=> 'is_in_stock',
    ];

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    /**
     * Fill fetch new images and place them in an array for viewing in a data table.
     * @param null $sku
     * @param null $limit
     * @return array $newImages
     */
    public function fetchNewImages($sku = Null, $limit = Null)
    {
        $select = $this->sql->select()
                  ->from('productattribute_images')
                  ->columns([
                            'valueid'       =>  'value_id',
                            'entityId'      =>  'entity_id',
                            'label'         =>  'label',
                            'filename'      =>  'filename',
                            'changedby'     =>  'changedby',
                            'position'      =>  'position',
                            'creation'      =>  'date_created',
                            ]);
        $select->join(['u'=>'users'], 'u.userid = productattribute_images.changedby',['fname'=>'firstname','lname'=>'lastname']);
        $select->join(['p'=>'product'], 'p.entity_id = productattribute_images.entity_id',['sku'=>'productid']);
        $filter = new Where;
        if ( $sku ){
            $filter->like('p.productid',$sku.'%');
        }
        if ( $limit ) {
            $select->limit($limit);
        }
        $filter->equalTo('productattribute_images.dataState',2);
        $select->where($filter);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $images = $resultSet->toArray();
        $soapCount = 0;
        $newImages = [];
        foreach( $images as $image ) {
            $newImages[$soapCount]['valueid'] = $image['valueid'];
            $newImages[$soapCount]['entityId'] = $image['entityId'];
            $newImages[$soapCount]['position'] = $image['position'];
            $newImages[$soapCount]['sku'] = $image['sku'];
            $newImages[$soapCount]['label'] = $image['label'];
            $newImages[$soapCount]['filename'] = '<img width="50" height="50" src="'.$image['filename'].'" />';
            $newImages[$soapCount]['creation'] = $image['creation'];
            $newImages[$soapCount]['fullname'] = $image['fname'] . ' ' . $image['lname'] ;
            $soapCount++;
        }
        return $newImages;
    }

    /**
     * @Description This method just arranges your UI selection to a 0-based array. This method can be taken away and
     * done in JS. But for now it works.
     * @param $images
     * @return array $soapImages
     */
    public function orderImages($images)
    {
        $imgCount = 0;
        $soapImages = [];
        foreach ( $images as $key => $content ) {
            $soapImages[$imgCount]['imageid'] = $content['imageid'];
            $soapImages[$imgCount]['id'] = $content['id'];
            $soapImages[$imgCount]['filename'] = $content['filename'];
            $soapImages[$imgCount]['sku'] = $content['sku'];
            $soapImages[$imgCount]['label'] = $content['label'];
            $soapImages[$imgCount]['position'] = $content['position'];
            $imgCount++;
        }
        return $soapImages;
    }

    /**
     * @description This method stacks the attributes when the entity ids are the same based on what selection the user
     * made in the UI checkboxes.
     * @param $checkboxSku
     * @return array $grouped
     */
    public function groupSku($checkboxSku)
    {
        $count = 0;
        $checkedIds = $checkedProperties = $grouped = $checkedValues = $checkedSku = [];
        foreach ( $checkboxSku as $checkbox ) {
            $checkedIds[$count] = $checkbox['id'];
            $checkedProperties[$count] = $checkbox['property'];
            $checkedValues[$count] = $checkbox['newValue'];
            $checkedSku[$count] = $checkbox['sku'];
            $count++;
        }
//        Get all ids for each one. If there are dups then make them unique and start from a 0-based array
        $uniqueIds = array_values(array_unique($checkedIds));

        foreach ($uniqueIds as $key => $uids) {
            $count = 0;
            $grouped[$key]['id'] = $uids;
            foreach ( $checkedIds as $index => $ids ) {
//                checks to see if the entity ids are the same.
//                checkedIds is an array that has all the ids(entity_id) that the user has clicked on.
//                It will compare this array with the unique array.
//                i.e: if there are 4 ids(entity_id) for 168 with attributes: name, url_key, manufacturer, and brand.
//                if will stack these on top of each other like so:
//                (entity_id)168 =>
//                       'name' => 'value'
//                       'url_key' => 'value'
//                       'manufacturer' => 'value'
//                       'brand' => 'value'

                if ( $uids == $ids ) {
//                    This commented out condition checked to see if qty was part of the attribute passing through.
//                    If so it added stock_data as an index to the array. Mage update API needs it.
//                    I commented it out because Andrew has a job in Management Studio that will update qty.
//                    if ( in_array($checkedProperties[$index], $this->stockData) ) {
//                        $grouped[$key][$count]['property'] = ['stock_data'=>$checkedProperties[$index]];
//                    } else {
                    if ( $checkedProperties[$index] != 'qty' ) {
                        $grouped[$key][$count]['property'] = $checkedProperties[$index];
                    }
//                    }
                    $grouped[$key][$count]['newValue'] = $checkedValues[$index];
                    $grouped[$key][$count]['sku'] = $checkedSku[$index];
                    $count++;
                }
            }
        }
        return $grouped;
    }

    /**
     * This method can be taken out and done in JS instead, in the data tables, or use array_values.
     * What it does is takes the array and re-orders it to start from 0.
     * @param $checkboxNewSku
     * @return array $groupedNewSku
     */
    public function groupNewSku($checkboxNewSku)
    {
        $count = 0;
        $groupedNewSku = [];
        foreach ($checkboxNewSku as $newSku) {
            $groupedNewSku[$count]['id'] = $newSku['id'];
            $groupedNewSku[$count]['sku'] = $newSku['sku'];
                    $count++;
        }
        return $groupedNewSku;
    }

    /**
     * This method can be taken out and done in JS instead, in the data tables, or use array_values.
     * What it does is takes the array and re-orders it to start from 0.
     * @param $checkboxCategory
     * @return array $groupedCategories
     */
    public function groupCategories($checkboxCategory)
    {
        $count = 0;
        $groupedCategories = [];
        foreach ($checkboxCategory as $categories) {
            $groupedCategories[$count]['id'] = $categories['id'];
            $groupedCategories[$count]['categoryId'] = $categories['categoryId'];
            $groupedCategories[$count]['dataState'] = $categories['dataState'];
            $groupedCategories[$count]['sku'] = $categories['sku'];
            $count++;
        }
        return $groupedCategories;
    }

    /**
     * This method can be taken out and done in JS instead, in the data tables, or use array_values.
     * What it does is takes the array and re-orders it to start from 0.
     * @param $checkboxRelated
     * @return array $groupedLinks
     */
    public function groupRelated($checkboxRelated)
    {
        $count = 0;
        $groupedLinks = [];
        foreach ($checkboxRelated as $related) {
            $groupedLinks[$count]['id'] = $related['id'];
            $groupedLinks[$count]['dataState'] = $related['dataState'];
            $groupedLinks[$count]['linkedId'] = $related['linkedId'];
            $groupedLinks[$count]['type'] = $related['type'];
            $groupedLinks[$count]['sku'] = $related['sku'];
            $count++;
        }
        return $groupedLinks;
    }

    /**
     * Description: This method is to populate the DataTable for changed/dirty products to be sent over the wire to focuscamera.
     * It grabs all attributes that have a dataState of 1.
     * This method will have to be audited. Andrew changed the product table to contain extra fields:
     * price (attribute_id = 99), qty (attribute_id = 1), status (attribute_id = 273), contentreviewed (attribute_id = 1676) ( note: using the mage api this would be content_reviewed)
     * checkout : http://www.magentocommerce.com/api/soap/catalog/catalogProduct/catalog_product.update.html
     * These attributes used to be in the corresponding attribute tables (decimal, int, int, int), respectively.
     * @TODO This method has a nasty bug that I was not able to fix. Since it uses the data table for viewing. The data table has a limit capability that restricts the viewing
     * to whateve the user selects in the dropdown (10,20,30,All). The bug is that if the user select 10 as a limit, the source code below will iterate each attribute 10 times.
     * So if they use 20 it will select all attributes but display 20 per attribute that has CHANGED.
     * @param $sku
     * @param $limit
     * @param $productsTable
     * @return array | $soapBundle
     */
    public function fetchChangedProducts($sku = Null , $limit= Null, ProductsTable $productsTable)
    {
        $soapBundle = [];
//        I commented this section out because I decided to left join the product table on line 298.

//        $select = $this->sql->select();
//        $select->from('product');
//        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
////        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'), Select::JOIN_LEFT);
//        $filter = new Where;
//        if( !empty($sku) ){
//            $filter->like('product.productid',$sku.'%');
//        }
//        $select->where($filter);
////        $select->limit((int)$limit);
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $products = $resultSet->toArray();
        $results = $this->productAttributeLookup($this->sql);
        $soapCount = 0;
//        foreach( $products as $product ) {
            foreach( $results as $attributes ) {
                $dataType = $attributes['dataType'];
                $attributeId = $attributes['attId'];
                $attributeCode = $attributes['attCode'];
                    if ( $attributeCode != 'qty' ) {
                        $selectAttribute = $this->sql->select()
                                                     ->from('productattribute_'.$dataType)
//                                                      moved to lines 305-306
//                                                     ->where(['attribute_id'=>$attributeId,/*'entity_id'=>$product['id'], */'productattribute_'.$dataType.'.dataState'=>1])
                                                     ->columns(['id'=>'entity_id', $attributeCode=>'value', 'ldate'=>'lastModifiedDate']);
                        $selectAttribute->join(array('u' => 'users'),'u.userid = productattribute_'.$dataType.'.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'), Select::JOIN_LEFT);
                        $selectAttribute->join(array('p' => 'product'),'p.entity_id= productattribute_'.$dataType.'.entity_id' ,['item'=>'productid'], Select::JOIN_LEFT);
                        //TODO If website is to be sent over this left join in the product table is where it will go.
                        $filter = new Where;
                        $filter->equalTo('productattribute_'.$dataType.'.attribute_id',$attributeId);
                        $filter->equalTo('productattribute_'.$dataType.'.dataState',1);
//                      If user didn't submit a sku then don't check for skus.
                        if ( $sku ) {
//                            Makes sure that Sku exists
                            if( !( $productsTable->validateSku( $sku ) ) ) {
                                $filter->like('p.productid', $sku.'%');
                                $filter->orPredicate(new Predicate\Like('productattribute_'.$dataType.'.value','%'.$sku.'%'));
                            } else {
                               $filter->equalTo('p.productid' , $sku);
                            }
                        }
                        $selectAttribute->where($filter);
                        $selectAttribute->order('productattribute_'.$dataType.'.lastModifiedDate ASC');
                        $selectAttribute->limit($limit);
                        $attStmt = $this->sql->prepareStatementForSqlObject($selectAttribute);
                        $attResult = $attStmt->execute();
                        $attSet = new ResultSet;
                        if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                            $attSet->initialize($attResult);
                        }
//                        Thie comment below will echo out the actual queries above in the developer tools console
//                        echo $selectAttribute->getSqlString(new \Pdo($this->adapter));
                        $productAttributes = $attSet->toArray();
//                      start of if
                        if(!empty($productAttributes )) {
//                            for ( $i = 0; $i < $limit; $i++ ) {
                                foreach ( $productAttributes as $prdAtts ) {
                                    $soapBundle[$soapCount]['id'] = $prdAtts['id'];
                                    $soapBundle[$soapCount]['item'] = $prdAtts['item'];
                                    $soapBundle[$soapCount]['oproperty'] = $attributeCode;
//                                    some attributes contain "_" (underscores) and since this is for viewing in the data table I replace it with a space.
                                    $property = preg_match('(_)',$attributeCode) ? str_replace('_',' ',$attributeCode) : $attributeCode;
                                    $soapBundle[$soapCount]['property'] = ucfirst($property);
                                    $soapBundle[$soapCount]['newValue'] = $prdAtts[$attributeCode];
                                    $soapBundle[$soapCount]['ldate'] = date('m-d-Y H:i:s',strtotime( $prdAtts['ldate'] ) );
                                    $soapBundle[$soapCount]['fullName'] = $prdAtts['fName']. ' ' . $prdAtts['lName'];
                                    $soapCount++;
                                }       // End of foreach
//                            }   //      End of for loop
                        }       //  End of if
                    }       //  End of if
            }       //End of foreach
        return $soapBundle;
    }

    /**
     * Fetches all linked products that are either new or are to be deleted and return them to the data table for viewing.
     * @param null $sku
     * @param null $limit
     * @param \Content\ContentForm\Model\ProductsTable $productsTable
     * @return array $linker
     */
    public function fetchLinkedProducts($sku = Null , $limit= Null, ProductsTable $productsTable)
    {
        $select = $this->sql->select()->columns(['entityId'=>'entity_id','sku'=>'productid'])->from('product');
        $dataState = new Expression("l.entity_id=product.entity_id and l.dataState in(2,3)");
        $select->join(['l'=>'productlink'], $dataState,['entityId'=>'entity_id', 'linkedEntityId'=>'linked_entity_id', 'dataState'=>'dataState']);
        $select->join( ['t'=>'productlink_type'], 'l.link_type_id = t.link_type_id',['type'=>'code']);
        $select->join( array('pid'=>'product'), 'pid.entity_id=l.entity_id',array('sku'=>'productid'), Select::JOIN_LEFT);
        $select->join( array('plid'=>'product'), 'plid.entity_id=l.linked_entity_id',array('linkedSku'=>'productid'), Select::JOIN_LEFT);
        $select->join( array('u'=>'users'), 'u.userid = l.changedby',array('fname'=>'firstname', 'lname'=>'lastname'));

        $filter = new Where();
        if( $sku ) {
//            This condition will validate if the sku exists as a whole if it doesn't it will do a like. If it does as a whole it will do an equal
//            similar to i.e: where pid.productid like "$sku%"
//            similar to i.e: where pid.productid = "$sku"
            if( !( $productsTable->validateSku( $sku ) ) ) {
                $filter->like('pid.productid', $sku.'%');

            } else {
                $filter->equalTo('pid.productid' , $sku);
            }
            $select->where($filter);
        }
        if( $limit ) {
            $select->limit((int)$limit);
        }
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
        $linkedProducts = $resultSet->toArray();
        $linker = [];
        $linkCount = 0;
        foreach ( $linkedProducts as $linked ) {
            $linker[$linkCount]['id']           = $linked['entityId'];
            $linker[$linkCount]['sku']          = $linked['sku'];
            $linker[$linkCount]['linkedId']     = $linked['linkedEntityId'];
            $linker[$linkCount]['linkedSku']     = $linked['linkedSku'];
            $linker[$linkCount]['dataState']    = $linked['dataState'];
//            There are 4 different data state that we have.
//            0 is clean, 1, is changed/dirty, 2 is new, 3 is deleted.
            $linker[$linkCount]['state']        = ((int)$linked['dataState'] == 2) ? 'New' : 'Delete';
            $linker[$linkCount]['type']         = ucfirst(str_replace('_',' ',$linked['type']));
            $linker[$linkCount]['fullname']     = $linked['fname'] . ' ' . $linked['lname'];
            $linkCount++;
        }
        return $linker;
    }

    /**
     * Once soap call those through and the response is positive or true. dataState 3 is deleted from Spex and dataState 2 is changed to a 0 for clean. Then the toastr that Andrew
     * came up with will populate.
     * @param $linkedProducts
     * @return string $result
     */
    public function updateLinkedProductstoClean($linkedProducts)
    {
        $result = '';
        $dataState = (int)$linkedProducts['dataState'];
        if ( $dataState === 3 ) {
            $delete = $this->sql->delete('productlink');
            $delete->where(array('entity_id'=>$linkedProducts['id'], 'linked_entity_id'=>$linkedProducts['linkedId']));
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $statement->execute();
            $result .= $linkedProducts['id'] . ' is no longer linked to ' . $linkedProducts['linkedId'].'<br />';
        } else {
            $update = $this->sql->update('productlink');
            $update->set(array('dataState'=>0))
                ->where(array('entity_id'=>$linkedProducts['id'], 'linked_entity_id'=>$linkedProducts['linkedId']));
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
            $result .= $linkedProducts['id'] . ' is linked to ' . $linkedProducts['linkedId'].'<br />';

        }
        return $result;
    }

    /**
     * Once soap call those through and the response is positive or true. dataState 3 is deleted from Spex and dataState 2 is changed to a 0 for clean. Then the toastr that Andrew
     * came up with will populate.
     * @param $cats
     * @return string $result
     */
    public function updateProductCategoriesToClean($cats)
    {
        $result = '';
        $dataState = (int)$cats['dataState'];
        if( $dataState === 2 ){
            $update = $this->sql->update('productcategory')->set(['dataState'=>0])->where(['entity_id'=>$cats['id'], 'category_id'=>$cats['categoryId']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
            $result .= $cats['sku'] . " has been added to categories in Magento Admin<br />";
        }
        if( $dataState === 3 ){
            $delete = $this->sql->delete('productcategory');
            $delete->where(['entity_id'=>$cats['id'], 'category_id'=>$cats['categoryId']]);
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $statement->execute();
            $result .= $cats['sku'] . " has been deleted from categories in Magento Admin<br />";
        }
        return $result;
    }

    /**
     * Fetches all categories for products that are either new or are to be deleted and return them to the data table for viewing.
     * @param null $sku
     * @param null $limit
     * @param \Content\ContentForm\Model\ProductsTable $productsTable
     * @return array $soapCategories
     */
    public function fetchChangedCategories($sku = null, $limit = null, ProductsTable $productsTable)
    {
        $soapCategories = [];
        $categoryCount = 0;
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id', 'sku'=>'productid']);
        $dataState = new Expression("c.entity_id=product.entity_id and c.dataState in(2,3)");

        $select->join(['c'=>'productcategory'], $dataState,['categoryId'=>'category_id', 'dataState'=>'dataState']);

        $select->join(['u'=>'users'], 'u.userid = c.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);

        $select->join(['cat'=>'category'] , 'cat.category_id = c.category_id', ['category'=>'title']);
        $filter = new Where();
        if( $sku ) {
            if( !( $productsTable->validateSku( $sku ) ) ) {
                $filter->like('product.productid', $sku.'%');

            } else {
                $filter->equalTo('product.productid' , $sku);
            }
            $select->where($filter);
        }
        if( $limit ){
            $select->limit((int)$limit);
        }

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
        $categories = $resultSet->toArray();
        foreach ( $categories as $category ) {
            $soapCategories[$categoryCount]['sku'] = $category['sku'];
            $soapCategories[$categoryCount]['id'] = $category['entityId'];
            $soapCategories[$categoryCount]['categoryId'] = $category['categoryId'];
            $soapCategories[$categoryCount]['category'] = $category['category'];
            $soapCategories[$categoryCount]['dataState'] = $category['dataState'];
            $soapCategories[$categoryCount]['state'] = ( $category['dataState'] == 2 ) ? 'New' : "Delete";
            $soapCategories[$categoryCount]['fullname'] = $category['fname']. ' ' . $category['lname'];
            $categoryCount++;
         }
        return $soapCategories;
    }

    /**
     * Images will be updated to clean if the image was created in Mage Admin. I'm using the filename in the where clause
     * because that's the only unique field per image.
     * @param $images
     * @return string $result
     */
    public function updateImagesToClean($images)
    {
        $result ='';
        $update = $this->sql->update('productattribute_images')->set(['dataState'=>0])->where(['filename'=>$images['filename']]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        $result .= $images['sku'] .  " with image label " . $images['label'] . " has been updated in Mage Admin.<br />";
        return $result;
    }

    /**
     * This method will have to be audited/refactored. Since Andrew changed the table structure for product table, if price, status, or content_reviewed change, not that they will
     * this method will not work for them. This method only alters the attributes tables for the correponding attributes that have changed.
     * @param $changedProducts
     * @return string $results
     */
    public function updateToClean($changedProducts)
    {
        $results = $sku = '';
        $entityId = $changedProducts['id'];
        array_shift($changedProducts);
        foreach ( $changedProducts as $attribute ) {
            $property = $attribute['property'];
            $sku = $attribute['sku'];
            $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$property]);
            $attributeId = $lookup[0]['attId'];
            $dataType = $lookup[0]['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$entityId]);
            $prdAttStatement = $this->sql->prepareStatementForSqlObject($update);
            $prdAttStatement->execute();
        }
        $results .= $sku . " has been updated in Magento Admin<br />";
        return $results;
    }

    /**
     * This method will fetch all new products that were created. Again, this method will have to be audited/refactored. Andrew moved over price, qty, status, and content_reviewed over to the
     * product table.
     * Note: a couple of things to mention.
     * 1. When creating a product status has to included in the list of attributes to send over. But also status has to be an integer when sent over. If status is Null in the db then just make it a 0 in the code.
     * 2. Also when being displayed in the data table. There are many products that are new. But as per Andrew only display product where the content_reviewed is 1. This is also reflected in the Newly Created KPI
     * (Key Performance Indicators).
     * 3. When creating a product qty has to be in an array itself called stock_data. Check out line 613-614 below.
     * 4. Also websites has to be plural and it's value has to be in an array.
     * For more information checkout the documentation: http://www.magentocommerce.com/api/soap/catalog/catalogProduct/catalog_product.create.html
     * @param $newProducts
     * @return array $soapBundle
     */
    public function fetchNewProducts($newProducts)
    {
        $soapBundle = [];
        $startCount = 0;

        foreach ( $newProducts as $nProd ) {
            $select = $this->sql->select()->from('product')->columns([
                'productType'   =>  'product_type',
                'website'       =>  'website',
            ])->where(['entity_id'=>$nProd['id']]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $entityId = $nProd['id'];
            $sku = $nProd['sku'];
            $products = $resultSet->toArray();
            foreach ( $products as $index => $value ) {
                $attributes = $this->productAttributeLookup($this->sql);
                foreach( $attributes as $attribute ) {
                    $tableType = (string)$attribute['dataType'];
                    $attributeId = (int)$attribute['attId'];
                    $attributeCode = $attribute['attCode'];
                    $selectAtts = $this->sql->select()->from('productattribute_'. $tableType)
                                                      ->columns([$attributeCode=>'value', 'attId'=>'attribute_id']);
                    $filterAttributes = new Where;
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.entity_id',$entityId);
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.attribute_id',$attributeId);
                    $selectAtts->where($filterAttributes);
                    $attStatement = $this->sql->prepareStatementForSqlObject($selectAtts);
                    $attResult = $attStatement->execute();
                    $attSet = new ResultSet;
                    if ( $attResult instanceof ResultInterface && $attResult->isQueryResult() ) {
                        $attSet->initialize($attResult);
                    }
                    $attributeValues = $attSet->toArray();
                    foreach( $attributeValues as $keyValue => $valueOption ) {
                        $soapBundle[$startCount]['id'] = $entityId;
                        $soapBundle[$startCount]['sku'] = $sku;
                        $soapBundle[$startCount]['websites'] = [$products[$index]['website']];
                        if ( array_key_exists($attributeCode,$this->stockData) ) {
                            $soapBundle[$startCount]['stock_data'][$attributeCode] = $valueOption[$attributeCode];
                        } else {
                            if( is_null($attributeValues[$keyValue][$attributeCode]) && $attributeCode ==  'status' ){
                                $soapBundle[$startCount][$attributeCode] = 0;
                            }
                            if( isset($attributeValues[$keyValue][$attributeCode]) ) {
                                $soapBundle[$startCount][$attributeCode] = $attributeCode == 'status' ? (int)$valueOption[$attributeCode] : $valueOption[$attributeCode] ;
                            }
                        }
                    }
                }
            }
            $startCount++;
        }
        return $soapBundle;

    }

    /**
     * This method just fetches new items and displayed them in the data table.
     * Note: in order for it to be displayed content_reviewed(1676) has to have a value 1 and the product/sku has to be dataState of 2.
     * @param $sku
     * @param $limit
     * @param \Content\ContentForm\Model\ProductsTable $productsTable
     * @return array $soapBundle
     */
    public function fetchNewItems($sku = Null, $limit = Null, ProductsTable $productsTable)
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = $optionValues = [];
        $select = $this->sql->select()->from('product')->columns([
            'id'            =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'creation'      =>  'creationdate',
            'creator'      =>  'changedby',
        ]);
        $filter = new Where;
        $filter->in('product.dataState',array(2));
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);
        $select->join(['u'=>'users'],'u.userid=i.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);
        if( $sku ) {
            if( !( $productsTable->validateSku( $sku ) ) ) {
                $filter->like('product.productid', $sku.'%');

            } else {
                $filter->equalTo('product.productid' , $sku);
            }
        }
        $select->where($filter);
        $select->limit((int)$limit);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        $startCount = 0;
        foreach($products as $product) {
            $soapBundle[$startCount]['sku'] = $product['sku'];
            $soapBundle[$startCount]['id'] = (int)$product['id'];
            $soapBundle[$startCount]['creation'] = date('m-d-Y',strtotime($product['creation']));
            $soapBundle[$startCount]['fullname'] = $product['fname'] . ' ' . $product['lname'];
            $startCount++;
        }
        return $soapBundle;
    }

    /**
     * This method will update the product table from dataState of 2 to 0 and its corresponding attributes table from (1|2) to 0
     * @param $newProducts
     * @param $mageEntityId
     * @return bool
     */
    public function updateNewProduct( $newProducts, $mageEntityId )
    {
        $sku = $newProducts['sku'];
        $oldEntityId = $newProducts['id'];
//        The array shift moves the first three indeces in the assoc array out and makes the rest of the code focus on the attributes for the particular sku that was new.
        array_shift($newProducts);
        array_shift($newProducts);
        array_shift($newProducts);
        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0 ])->where(['productid'=>$sku]);
        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
        $prdStmt->execute();
        foreach( $newProducts as $attributeCode => $attributeValue ) {
            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
            if( !empty($lookupVals[0]) ) {
                $attributeId = $lookupVals[0]['attId'];
                $dataType = $lookupVals[0]['dataType'];
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $stmt->execute();
            }
        }
        return true;
    }

    /**
     * When creating a new product that was transplanted from SellerCloud, Spex provides it with an entity_id. When this new product/Sku is persisted into Mage DB,
     * there will be a new entity_id that Mage will respond with. If this entity_id already exists in Spex under a different Sku than the one that was originally sent.
     * We have to do a couple of things.
     * 1. We have to get the max entity_id in the product table and add 1 to it.
     * 2. We then update the Sku (not the one that was sent to Mage DB for creation) that had that dup entity_id and update it with step 1 above.
     * We have to do this with the product table and all the attribute tables as well as the productlink table and the productcategory table that have this dup entity_id.
     * 3. We then take the Sku that was sent and update it with the entity_id that was returned and update the product table, the attribute tables, productlink table, and productcategory table.
     *      We update it with the new entity_id from Mage but also change the dataState from 2 to 0.
     * Sound like a lot huh? It kind of was, but the fun part was thinking of how to accomplish this. Don't you just love it? Woo!!
     * image table doens't need to be added to this because... It's a new product. What possible images can a new product have.
     * @param $newProducts
     * @param $maxEntityId
     * @param $existingSku
     * @param $existingEntityId
     * @param $mageEntityId
     * @return bool
     */
    public function updateExistingProduct($newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId)
    {
//        Mage entity id exists already so update with max entity id.
        $sku = $newProducts['sku'];
        $oldEntityId = $newProducts['id'];

//        The array shift moves the first three indeces in the assoc array out and makes the rest of the code focus on the attributes for the particular sku that was new.
        array_shift($newProducts);
        array_shift($newProducts);
        array_shift($newProducts);
//        This whole jazz here is for the Sku (not the one that was sent) with the dup entity_id returned from Mage.
//        This part is for the product table.
        $existingProduct = $this->sql->update('product')->set(['entity_id'=>$maxEntityId])->where(['productid'=>$existingSku]);
        $existingStmt = $this->sql->prepareStatementForSqlObject($existingProduct);
        $existingResponse = $existingStmt->execute();

//        This is for all the attribute tables.
        $lookupExistingVals = $this->productAttributeLookup( $this->sql );
        foreach ( $lookupExistingVals as $key => $attributes ){
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$maxEntityId])->where(['attribute_id'=>$attributeId, 'entity_id'=>$existingEntityId]);
            $stmt = $this->sql->prepareStatementForSqlObject($update);
            $attributeResp = $stmt->execute();
        }

//      This is for the productcategory table.
        $updateExistingCat = $this->sql->update('productcategory')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateStatement = $this->sql->prepareStatementForSqlObject($updateExistingCat);
        $updateResponse = $updateStatement->execute();

//        This is for the productlink table.
        $updateExistingLink = $this->sql->update('productlink')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateExistingStmt = $this->sql->prepareStatementForSqlObject($updateExistingLink);
        $existingResponse = $updateExistingStmt->execute();

//        Doubt this one is needed but can't hurt to have. If the entity_id doesn't exist no biggie it just won't execute an update query.
        $updateExistingImage = $this->sql->update('productattribute_images')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateImageStatement = $this->sql->prepareStatementForSqlObject($updateExistingImage);
        $updateResponse = $updateImageStatement->execute();

//        This whole jazz here is to update the Sku that was sent over to Mage with the returned entity_id.
//        This part is f or the product table.
        $updateNew = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['productid'=>$sku]);
        $updateStmt = $this->sql->prepareStatementForSqlObject($updateNew);
        $newResponse = $updateStmt->execute();

//        This is for all of the attributes tables that correspond with entity_id.
        $lookupNewVals = $this->productAttributeLookup( $this->sql );
        foreach ( $lookupNewVals as $key => $attributes ){
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId ,'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);
            $stmt = $this->sql->prepareStatementForSqlObject($update);
            $attributeResp = $stmt->execute();
        }

//        This is for the productcategory table.
        $existingEntityCategory = $this->sql->update('productcategory')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
        $existingEntityCategoryStmt = $this->sql->prepareStatementForSqlObject($existingEntityCategory);
        $existingResponse = $existingEntityCategoryStmt->execute();

//        This is for the productlink table.
        $existingEntityLink = $this->sql->update('productlink')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
        $existingEntityLinkStmt = $this->sql->prepareStatementForSqlObject($existingEntityLink);
        $existingResponse = $existingEntityLinkStmt->execute();
        return true;
    }

    /**
     * This is where the magic happens for the above two methods.
     * @param $newProducts
     * @param $mageEntityId
     * @return string $result
     */
    public function updateNewItemsToClean($newProducts, $mageEntityId)
    {
        $result  = $maxEntityId = '';
//        If return entity_id already exists select the entity_id and the sku for said dup entity_id.
        $dupEntityIdExists = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id','sku'=>'productid'])->where(['entity_id'=>$mageEntityId]);
        $dupStatement = $this->sql->prepareStatementForSqlObject($dupEntityIdExists);
        $dupResponse = $dupStatement->execute();
        $dupSet = new ResultSet;
        if ($dupResponse instanceof ResultInterface && $dupResponse->isQueryResult()) {
            $dupSet->initialize($dupResponse);
        }
        $id = $dupSet->toArray();
//        if the above query found something then the entity_id return from Mage exists in Spex.
        if( count($id) ) {
//            Crap! entity_id returned from Mage already exists in Spex. This makes me have to think! Alright... Let's do it.
            $existingSku = $id[0]['sku'];
            $existingEntityId = $id[0]['entityId'];
//            Grab the max entity_id in the product table.
            $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
            foreach( $entityId as $eid ) {
                foreach( $eid as $maxEntityID ) {
//                    Add 1 to the max entity_id
                    $maxEntityId = $maxEntityID + 1;
                    $this->updateExistingProduct( $newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId );
                }
            }
            $result .= $existingSku . ' has been updated in Spex with ' . $maxEntityId . '<br />';
            $result .= $newProducts['sku'] . ' has been added to Magento Admin with new ID ' . $mageEntityId . '<br />';
        } else {
//            entity_id did not exist in product table. Yeah!! Alright. Makes things simple.
            $response = $this->updateNewProduct($newProducts, $mageEntityId);
            $result .= $newProducts['sku'] . ' has been added to Magento Admin with ID ' . $mageEntityId . '<br />';
        }
        return $result;
    }

    /**
     * @Description: This method is different because of the checkboxes in the UI. I will probably have to refactor this
     * at some point in the future. For now it works perfectly. I have an index property that contains a string or an array.
     * The array is because of qty. Qty in Mage Soap API has to be in the stock_data array. In spex it doens't exist so I have
     * to account for this. When sent through the wire I have to insert it but when updating attributes tables I have to
     * take it out so that update statement for int table works properly.
     * @param $products
     * @return array | $productSkus
     * */
    public function adjustUpdateProductKeys($products)
    {
        $productSkus = [];
        foreach ( $products as $key => $atts ) {
            $productSkus[$key]['id'] = $atts['id'];
            array_shift($atts);
            foreach ($atts as $index => $properties ) {
                $productSkus[$key][$index]['sku'] = $properties['sku'];
                foreach ( $properties as $attributes => $value ) {
                    if( $attributes == 'property' ) {
                        if ( is_array($value) ) {
                            foreach( $value as $mageAtt => $spexAtt ) {
                                $productSkus[$key][$index]['property'] = $spexAtt;
                            }
                        } else {
                            $productSkus[$key][$index]['property'] = $value;
                        }
                    }
                    if ( $attributes == 'newValue' ) {
                        $productSkus[$key][$index]['newValue'] = $value;
                    }
                }
            }
        }
        return $productSkus;
    }

    /**
     * Note: the param was $newProducts but I changed it to $products to make it useful for new products and changed products since they both
     * require qty to be an assoc array under stock_data.
     * @Description: This method de-inserts/takes away the stock_data key so that when attributes are being cleaned it cleans the elements of stock_data.
     * Since stock_data doesn't actually exist as an attribute but qty does, it being attribute_id 1 in the int attributes table.
     * @param $products
     * @return array | $productSkus
     **/
    public function adjustProductKeys($products)
    {
        $shiftedStockData = $productSkus = [];
        foreach( $products as $key => $acode ) {
            foreach( $acode as $index => $aValues ) {
                if( $index == 'stock_data' && isset($products[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
//                    TODO might have to add a foreach here for stock_data,since this will have multiple attributes within.
//                    if( isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
                    $productSkus[$key][current(array_keys($this->stockData))] = $products[$key]['stock_data'][current(array_keys($this->stockData))];
                    $shiftedAttribute = array_shift($this->stockData);
                    $shiftedStockData[$shiftedAttribute] =  $shiftedAttribute;
//                    }
                } else {
                    $productSkus[$key][$index] = $products[$key][$index];
                }
            }
            $this->stockData = $shiftedStockData + $this->stockData;
        }
        return $productSkus;
    }

}