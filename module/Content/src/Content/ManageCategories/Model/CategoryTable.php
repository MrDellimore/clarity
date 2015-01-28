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

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }


    /**
     * @Description: Queries product table and left joins productcategory, varchar, imges, and (int,option) for manufacturer.
     * Filters by category id first with an inner join and then filters by sku and/or limit.
     * Sql:
     * use spex;
     * select productid, v.value, i.domain, i.filename, o.value from product p
     * inner join productcategory g
     * on g.entity_id=p.entity_id and g.category_id = 47
     * left join productattribute_varchar v
     * on p.entity_id = v.entity_id and v.attribute_id = 96
     * left join productattribute_images i
     * on i.entity_id = p.entity_id and i.default = 1 and i.disabled = 0
     * left join productattribute_int iman
     * on iman.entity_id = p.entity_id and iman.attribute_id = 102
     * left join productattribute_option o
     * on o.option_id = iman.value and o.attribute_id = 102
     * limit 10;
     * @param null $sku
     * @param null $limit
     * @param null $cats
     * @return array $category
     */
    public function fetchCategoryProducts($sku =null, $limit= null, $cats= null)
    {
        $category = [];
        $select = $this->sql->select()->from('product')->columns(['id'=>'entity_id','sku'=>'productid']);
        $cat = new Expression('c.entity_id=product.entity_id and c.category_id = '. $cats . ' and c.dataState != 3');
        $name = new Expression('v.entity_id = product.entity_id and v.attribute_id = 96');
        $images = new Expression('i.entity_id = product.entity_id and i.default = 1 and i.disabled = 0');
        $intTable = new Expression('man.entity_id = product.entity_id and man.attribute_id = 1641');
        $manufacturer = new Expression('opt.option_id = man.value and opt.attribute_id = 1641');
        $select->join(['c'=>'productcategory'],$cat, ['catid'=>'category_id']);
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

        $i=0;
        foreach ( $categoryProducts as $prods ) {
            $category[$i]['catid'] = $prods['catid'];
            $category[$i]['Entityid'] = $prods['id'];
            $category[$i]['sku'] = $prods['sku'];
            $category[$i]['value'] = "<img src='".$prods['domain'].$prods['filename']."' width='100' height='100' /> <br />". $prods['value'];
            $category[$i]['manufacturer'] = $prods['manufacturer'];
            $i++;
        }
        return $category;
    }

    /**
     * @Description: method will populate a datatable so that users can add product to specific categories.
     * @param null $sku
     * @param $limit
     * @param null $managedProducts
     * @internal param Null $managedProducts
     * @return array
     */
    public function populateProducts($sku = Null , $limit, $managedProducts = Null )
    {
        $hiddenInputs = [];
        $select = $this->sql->select()->from('product')->columns(array('Entityid' => 'entity_id', 'sku' => 'productid'));
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
        $producttable = new ProductsTable($this->adapter);
        $filter = new Where();
        if ( $sku || count($managedProducts) ) {
            if ( count($managedProducts) ) {
                $cnt = 0;
                foreach ( $managedProducts as $products ) {
                    $hiddenInputs[$cnt]['Entityid'] = $products['Entityid'] ;
                    $hiddenInputs[$cnt]['sku'] = $products['sku'] ;
                    $hiddenInputs[$cnt]['value'] = "<img width='100' height='100' src='". $products['img'] . "' /><br />" . $products['name'] ;
                    $hiddenInputs[$cnt]['manufacturer'] = $products['manufacturer'] ;
                    $cnt++;
                }
            }
            if( !($producttable->validateSku($sku)) ){
                $filter->like('product.productid', $sku.'%');
                $filter->orPredicate(new Predicate\Like('t.value','%'.$sku.'%'));
                $select->where($filter);
            } else {
                $select->where(['product.productid' => $sku]);
            }
        }
        $select->limit($limit);
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
            $product[$count]['Entityid'] = $prods['Entityid'];
            $product[$count]['sku'] = $prods['sku'];
            $product[$count]['value'] = "<img src='".$prods['domain'].$prods['filename']."' width='100' height='100' /> <br />".$prods['name'];
            $product[$count]['manufacturer'] = $prods['manufacturer'];
            $count++;
        }
        if ( !empty($hiddenInputs) ) {
            return $hiddenInputs;
        }
        return $product;
    }




} 