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
    public function fetchCategoryProducts($sku =null, $limit= null, $cats= null)
    {
//        echo $cats;
        $category = [];
        $count = 0;
        $select = $this->sql->select()->from('product')->columns(['sku'=>'productid']);
        $cat = new Expression('c.entity_id=product.entity_id and c.category_id = '. $cats);
        $name = new Expression('v.entity_id = product.entity_id and v.attribute_id = 96');
        $images = new Expression('i.entity_id = product.entity_id and i.default = 1 and i.disabled = 0');
        $intTable = new Expression('man.entity_id = product.entity_id and man.attribute_id = 1641');
        $manufacturer = new Expression('opt.option_id = man.value and opt.attribute_id = 1641');
        $select->join(['c'=>'productcategory'],$cat, ['cateroty_id'=>'category_id']);
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

} 