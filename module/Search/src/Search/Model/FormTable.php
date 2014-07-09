<?php

namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Search\Model\Form;
use Search\Helper\FormatFields;

class FormTable{

    protected $sku;

    protected $select = Null;

    protected $sql;

    protected $skuFields = array();

    protected $form;

    public function __construct(Adapter $adapter, Form $form){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
        $this->form = $form;
    }

    public function executeQuery(){
        $statement = $this->sql->prepareStatementForSqlObject($this->select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        This is my query.
//        var_dump($resultSet);
        return $resultSet;
    }

    public function isSkuValid(ResultSet $result){
        if(!$result->valid()){
            return False;
        }
        return true;
    }

    public function isSelect(){
        if( is_null($this->select) ) {
            $this->select = $this->sql->select();
            $this->selectQuery();
        }
        return $this->select;

    }

    public function selectQuery(){
        $this->select->from('product')
            ->where(
                array(
                    'productid' => $this->sku
                )
            );
    }

    /**
     * @param $sku
     * @throws \Exception
     * @return int
     */
    public function validateSku($sku){
//        $this->selectQuery($sku);
        $this->sku = $sku;
        $this->isSelect();
        $resultSet = $this->executeQuery();
        if( !$this->isSkuValid($resultSet) ){
            return false;
        }
        $skuList = array();
        $skuList = $resultSet->current();
        return $skuList['entity_id'];
    }

    public function joinTables($entityID){
        $this->skuFields = [
            //Standard
            'title:varchar:t' => new Expression("t.entity_id = $entityID and t.attribute_id = 96"),
            'quantity:int:q' => new Expression("q.entity_id = $entityID and q.attribute_id = 1"),
            'urlkey:varchar:ukey'  => new Expression("ukey.entity_id = $entityID and ukey.attribute_id = 481"),
            //Pricing
            'price:decimal:price' => new Expression("price.entity_id = $entityID and price.attribute_id = 99"),
            'cost:decimal:cost'  => new Expression("cost.entity_id = $entityID and cost.attribute_id = 100"),
            'rebate:decimal:rebate'  => new Expression("rebate.entity_id = $entityID and rebate.attribute_id = 1590"),
            'mailInRebate:decimal:mailin'  => new Expression("mailin.entity_id = $entityID and mailin.attribute_id = 1593"),
            'special:decimal:special'  => new Expression("special.entity_id = $entityID and special.attribute_id = 567"),
            //Pricing Dates
       'specialDateFrom:datetime:sdf'   => new Expression("sdf.entity_id = $entityID and sdf.attribute_id = 568"),
       'specialDateTo:datetime:sdt'   => new Expression("sdt.entity_id = $entityID and sdt.attribute_id = 569"),
         'rebateDateFrom:datetime:rdf'   => new Expression("rdf.entity_id = $entityID and rdf.attribute_id = 1591"),
        'rebateDateTo:datetime:rdt'   => new Expression("rdt.entity_id = $entityID and rdt.attribute_id = 1592"),
            'mailrebateDateFrom:datetime:mrdf'   => new Expression("mrdf.entity_id = $entityID and mrdf.attribute_id = 1594"),
            'mailrebateDateTo:datetime:mrdt'   => new Expression("mrdt.entity_id = $entityID and mrdt.attribute_id = 1595"),
            //Shipping
            'weight:decimal:weight'   => new Expression("weight.entity_id = $entityID and weight.attribute_id = 101"),
            'usExpedited:varchar:use'   => new Expression("use.entity_id = $entityID and use.attribute_id = 1642"),
            'usTwoDay:varchar:utd'  => new Expression("utd.entity_id = $entityID and utd.attribute_id = 1645"),
            'usOneDay:varchar:uod'  => new Expression("uod.entity_id = $entityID and uod.attribute_id = 1643"),
            'usStandard:varchar:us'  => new Expression("us.entity_id = $entityID and us.attribute_id = 1644"),
            'canPriority:varchar:cp'  => new Expression("cp.entity_id = $entityID and cp.attribute_id = 1649"),
            'canFirstClass:varchar:cfc'  => new Expression("cfc.entity_id = $entityID and cfc.attribute_id = 1648"),
            'asiaPriority:varchar:ap'  => new Expression("ap.entity_id = $entityID and ap.attribute_id = 1651"),
            'asiaFirstClass:varchar:afc'  => new Expression("afc.entity_id = $entityID and afc.attribute_id = 1650"),
            'europeFirstClass:varchar:efc'  => new Expression("efc.entity_id = $entityID and efc.attribute_id = 1646"),
            'europePriority:varchar:ep'  => new Expression("ep.entity_id = $entityID and ep.attribute_id = 1647"),
            'outsideAsiaPriority:varchar:oap'  => new Expression("oap.entity_id = $entityID and oap.attribute_id = 1653"),
            'outsideAsiaFirstClass:varchar:oafc'  => new Expression("oafc.entity_id = $entityID and oafc.attribute_id = 1652"),

            //Description
            'metaTitle:varchar:mt'  => new Expression("mt.entity_id = $entityID and mt.attribute_id = 103"),
            'metaDescription:varchar:md'  => new Expression("md.entity_id = $entityID and md.attribute_id = 105"),

        ];
    }



    public function setupData($data){

                return [
                    'sku'  => strtoupper($this->sku),
                    'name'  => $data['title'] = isset($data['title']) ? $data['title'] : '',
                    'inventory'  => $data['quantity'] = isset($data['quantity']) ? $data['quantity'] : '',
                    'urlKey'  => $data['urlkey'] = isset($data['urlkey']) ? $data['urlkey'] : '' ,
                    'price'  => $data['price'] = isset($data['price']) ? $data['price'] : '',
                    'cost'  => $data['cost'] = isset($data['cost']) ? $data['cost'] : 0,
                    'rebatePrice'  => $data['rebate'] = isset($data['rebate']) ? $data['rebate'] : 0,
                    'rebateStartEndDate'   => $data['rebateDateFromTo'] = (isset($data['rebateDateFrom']) &&
                            isset($data['rebateDateTo'])) ?
                            FormatFields::reformatDate($data['rebateDateFrom'], $data['rebateDateTo'])  : 'N/A',
                    'specialPrice'  => $data['special'] = isset($data['special']) ? $data['special'] : 0,
                    'specialStartEndDate'  => $data['specialDateFromTo'] = (isset($data['specialDateFrom']) &&
                            isset($data['specialDateTo'])) ?
                            FormatFields::reformatDate($data['specialDateFrom'], $data['specialDateTo'])  : 'N/A',
                    'mailInRebate'  => $data['mailInRebate'] = isset($data['mailInRebate']) ? $data['mailInRebate'] : 0,

                    'mailInStartEndDate'   => $data['mailRebateDateFromTo'] = (isset($data['mailrebateDateFrom']) &&
                                                                                            isset($data['mailrebateDateTo'])) ?
                                                                                            FormatFields::reformatDate($data['mailrebateDateFrom'], $data['mailrebateDateTo'])  : 'N/A',

                    'weight'  => $data['weight'] = isset($data['weight']) ? $data['weight'] : 0,
                    'usExpedited'  => $data['usExpedited'] = isset($data['usExpedited']) ? $data['usExpedited'] : 0,
                    'usTwoDay'  => $data['usTwoDay'] = isset($data['usTwoDay']) ? $data['usTwoDay'] : 0,
                    'canadaPriority'  => $data['canPriority'] = isset($data['canPriority']) ? $data['canPriority'] : 0,
                    'canadaFirstClass'  => $data['canFirstClass'] = isset($data['canFirstClass']) ? $data['canFirstClass'] : 0,
                    'asiaPriority'  => $data['asiaPriority'] = isset($data['asiaPriority']) ? $data['asiaPriority'] : 0,
                    'asiaFirstClass'  => $data['asiaFirstClass'] = isset($data['asiaFirstClass']) ? $data['asiaFirstClass'] : 0,
                    'europePriority'  => $data['europePriority'] = isset($data['europePriority']) ? $data['europePriority'] : 0,
                    'europeFirstClass'  => $data['europeFirstClass'] = isset($data['europeFirstClass']) ? $data['europeFirstClass'] : 0,
                    'outsideAsiaPriority'  => $data['outsideAsiaPriority'] = isset($data['outsideAsiaPriority']) ? $data['outsideAsiaPriority'] : 0,
                    'outsideAsiaFirstClass'  => $data['outsideAsiaFirstClass'] = isset($data['outsideAsiaFirstClass']) ? $data['outsideAsiaFirstClass'] : 0,
                    'usOneDay'  => $data['usOneDay'] = isset($data['usOneDay']) ? $data['usOneDay'] : 0,
                    'usStandard'  => $data['usStandard'] = isset($data['usStandard']) ? $data['usStandard'] : 0,
                    'metaTitle'  => $data['metaTitle'] = isset($data['metaTitle']) ? $data['metaTitle'] : 0,
                    'metaDescription'  => $data['metaDescription'] = isset($data['metaDescription']) ? $data['metaDescription'] : 0,

                ];
    }




    /**
     * @param null $entityID
     * @internal param $sku
     * @return Form
     */

    public function lookupData($entityID){
        $this->joinTables($entityID);
        foreach( $this->skuFields as $backEndType => $expressionObject){
            $keys = explode(':',$backEndType);
            $dbName = $keys[0];
            $dbDataType = $keys[1];
            $alias = $keys[2];
            $this->select->join(
                array(
                    $alias => 'productattribute_' . $dbDataType),
                $expressionObject,

//                        'p' =>  'productattribute_decimal'),
//                    $this->joinTables($entityID),
                array(
                    $dbName => 'value'
                ),
                Select::JOIN_LEFT
            );
        }
        $data = array();
        $data = $this->executeQuery()->current();

        return $data;

    }
}