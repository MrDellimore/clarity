<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/7/14
 * Time: 12:56 PM
 */

namespace Content\ManageCategories\Model;

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
use Content\ContentForm\Model\ProductsTable;
use Zend\Db\Sql\Predicate;


class CategoryTable {

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }
//use spex;
//select productid, v.value, i.domain, i.filename, o.value from product p
//inner join productcategory g
//on g.entity_id=p.entity_id and g.category_id = 3
//left join productattribute_varchar v
//on p.entity_id = v.entity_id and v.attribute_id = 96
//left join productattribute_images i
//on i.entity_id = p.entity_id and i.default = 1 and i.disabled = 0
//left join productattribute_int iman
//on iman.entity_id = p.entity_id and iman.attribute_id = 102
//left join productattribute_option o
//on o.option_id = iman.value and o.attribute_id = 102
//limit 10;
    /**
     * Description: Queries product table and left joins productcategory, varchar, imges, and (int,option) for manufacturer.
     * Filters by category id first with an inner join and then filters by sku and/or limit.
     * @param null $sku
     * @param null $limit
     * @param null $cats
     * @return array $category
     */
    public function fetchCategoryProducts($sku =null, $limit= null, $cats= null)
    {
        $category = [];
        $count = 0;
        $select = $this->sql->select()->from('product')->columns(['sku'=>'productid']);
        $cat = new Expression('c.entity_id=product.entity_id and c.category_id = '. $cats . ' and c.dataState != 3');
        $name = new Expression('v.entity_id = product.entity_id and v.attribute_id = 96');
        $images = new Expression('i.entity_id = product.entity_id and i.default = 1 and i.disabled = 0');
        $intTable = new Expression('man.entity_id = product.entity_id and man.attribute_id = 1641');
        $manufacturer = new Expression('opt.option_id = man.value and opt.attribute_id = 1641');
        $select->join(['c'=>'productcategory'],$cat, ['catid'=>'catid','cateroty_id'=>'category_id']);
        $select->join(['v'=>'productattribute_varchar'],$name, ['value'=>'value'], Select::JOIN_LEFT);
        $select->join(['i'=>'productattribute_images'],$images, ['domain'=>'domain', 'filename'=>'filename'], Select::JOIN_LEFT);
        $select->join(['man'=>'productattribute_int'],$intTable, ['optionId'=>'value'], Select::JOIN_LEFT);
        $select->join(['opt'=>'productattribute_option'],$manufacturer, ['manufacturer'=>'value'], Select::JOIN_LEFT);
        $filter = new Where;
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
            $select->where($filter);
        }
        $select->limit($limit);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $categoryProducts = $resultSet->toArray();
        foreach ( $categoryProducts as $prods ) {
            $category[$count]['catid'] = $prods['catid'];
            $category[$count]['sku'] = $prods['sku'];
            $category[$count]['value'] = "<img src='".$prods['domain'].$prods['filename']."' width='100' height='100' /> <br />". $prods['value'];
//            $category[$count]['imagename'] = "<img src='".$prods['domain'].$prods['filename']."' width='50' height='50' />";
//            $category[$count]['imagename'] = $prods['domain'].$prods['filename'];
            $category[$count]['manufacturer'] = $prods['manufacturer'];
            $count++;
        }
//        echo $select->getSqlString(new \Pdo($this->adapter));

        return $category;
    }

    /**
     * Description: method will populate a datatable so that users can add product to specific categories.
     * @param null $sku
     * @param $limit
     * @param null $manangedProducts
     * @internal param Null $managedProducts
     * @return array
     */
    public function populateProducts($sku = Null , $limit, $manangedProducts = Null , $cat = Null)
    {

//        if ( count($manangedProducts) ) {
//            echo 'works';
//            foreach( $manangedProducts as $mgdProds ){
//                var_dump($mgdProds['value']);
//                var_dump($mgdProds['name']);
//            }
//        }
        $hiddenInputs = [];
        $select = $this->sql->select()->from('product')->columns(array('id' => 'entity_id', 'sku' => 'productid'));
        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $images = new Expression('i.entity_id = product.entity_id and i.default = 1 and i.disabled = 0');
        $intTable = new Expression('man.entity_id = product.entity_id and man.attribute_id = 1641');
        $manufacturer = new Expression('opt.option_id = man.value and opt.attribute_id = 1641');
//        $category = new Expression('pc.category_id != '. $cat . ' and (pc.dataState != 3 or pc.dataState != 2)');
//        $select->join(array('pc' => 'productcategory'), $category ,[], Select::JOIN_LEFT);
        $select->join(array('t' => 'productattribute_varchar'), $titleJoin,array('name' => 'value'), Select::JOIN_LEFT);
        $select->join(['i'=>'productattribute_images'],$images, ['domain'=>'domain', 'filename'=>'filename'], Select::JOIN_LEFT);
        $select->join(['man'=>'productattribute_int'],$intTable, ['optionId'=>'value'], Select::JOIN_LEFT);
        $select->join(['opt'=>'productattribute_option'],$manufacturer, ['manufacturer'=>'value'], Select::JOIN_LEFT);
//        $select->quantifier(Select::QUANTIFIER_DISTINCT);

        $producttable = new ProductsTable($this->adapter);
        $filter = new Where();

        if ( $sku || count($manangedProducts) ) {
//            var_dump(count($manangedProducts));
            $cnt = 0;
            foreach ( $manangedProducts as $key => $products ) {
//                $hiddenInputs[$key]['checked'] = "<td class='sorting_1'><label for='addCategoryProduct'></label><input type='checkbox' checked name='addCategoryProduct[][sku]' class='addCategoryProduct'></td>";
                $hiddenInputs[$cnt]['id'] = $products['id'] ;
                $hiddenInputs[$cnt]['sku'] = $products['sku'] ;
//                $hiddenInputs[$key]['name'] = "<td class=' value'>" . $products['id'] . "</td>";
                $hiddenInputs[$cnt]['value'] = "<img width='100' height='100' src='". $products['img'] . "' /><br />" . $products['name'] ;
                $hiddenInputs[$cnt]['manufacturer'] = $products['manufacturer'] ;
                $cnt++;
//                echo $products['id'] . ' ' . $products['img']. ' ' . $products['name']. ' ' . $products['manufacturer']. ' ' . $products['sku'] ."\n";
            }
//            var_dump($manangedProducts['value']);
//            var_dump($manangedProducts['name']);
//            echo 'haha';
            if( !($producttable->validateSku($sku)) ){
                $filter->like('product.productid', $sku.'%');
                $filter->orPredicate(new Predicate\Like('t.value','%'.$sku.'%'));
                $select->where($filter);
            } else {
                $select->where(['product.productid' => $sku]);
            }
        }
        $select->limit($limit);
//        echo $select->getSqlString(new \Pdo($this->adapter));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $count = 0;
        $product = [];
        $products = $resultSet->toArray();
        foreach ( $products as $prods ) {
            $product[$count]['id'] = $prods['id'];
            $product[$count]['sku'] = $prods['sku'];
            $product[$count]['value'] = "<img src='".$prods['domain'].$prods['filename']."' width='100' height='100' /> <br />".$prods['name'];
//            $product[$count]['image'] = "<img src='".$prods['domain'].$prods['filename']."' width='100' height='100' /> <br />".$prods['name'];
//            $category[$count]['imagename'] = "<img src='".$prods['domain'].$prods['filename']."' width='50' height='50' />";
//            $category[$count]['imagename'] = $prods['domain'].$prods['filename'];
            $product[$count]['manufacturer'] = $prods['manufacturer'];
            $count++;
        }
        if ( !empty($hiddenInputs) ) {
            return $hiddenInputs;
        }
        return $product;
    }


    /**
     * Description: method will update productcategory table for selected catid and skus with a dataState of 3 (to be deleted). It must first query the product table to
     * acquire entity id.
     * @param $sku
     * @param $catId
     * @param $user
     * @return string
     * */
    public function removeProducts($sku, $catId, $catPk, $user)
    {
        $results = '';
//        $selectId = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id'])->where(['productid'=>$sku]);
//        $statement = $this->sql->prepareStatementForSqlObject($selectId);
//        $result = $statement->execute();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $prdEntity = $resultSet->toArray();
        $updateCat = $this->sql->update('productcategory')
                  ->set(['dataState'=>3,'changedby'=>$user])
                  ->where([
                          'catid'   =>  $catPk,
//                        'category_id'=> $catId,
//                        'entity_id'=> (int)$prdEntity[0]['entityId']
            ]);
        $statement = $this->sql->prepareStatementForSqlObject($updateCat);
        $statement->execute();
        $results .= $sku . ' has been removed from category.';
        return $results;
    }

    /**
     * @Description: This method will add/insert to the productcategory table new products or entity ids.
     * @param $products
     * @param $category
     * @param $user
     * @return string $result
     * */
    public function addProducts($products, $category, $user)
    {
        $res = $response = '';
        foreach ( $products as $product ) {
//            $select = $this->sql->select()->from('productcategory')->where(['entity_id'=>$product['id'], 'category_id'=>$category]);
//            $statement = $this->sql->prepareStatementForSqlObject($select);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
//            $cat = $resultSet->toArray();
//            $response = !empty($cat);
//            if ( !count($resultSet->toArray()) ) {
                $insertCat = $this->sql->insert()->into('productcategory')
                                                ->columns(['entity_id','category_id','dataState','changedby'])
                                                ->values(['entity_id'=>$product['id'], 'category_id'=>$category, 'dataState'=>2,'changedby'=>$user]);
                $statement = $this->sql->prepareStatementForSqlObject($insertCat);
                $statement->execute();
                $res .= "SKU: " . $product['sku'] . " for Category ID " . $category . " has been added.<br />";
//            }
//            if ( $response ) {
//                $res = "";
//        }
        }

        return $res;
    }

} 