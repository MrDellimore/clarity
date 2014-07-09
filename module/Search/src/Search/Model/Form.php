<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 6/24/14
 * Time: 4:51 PM
 */

namespace Search\Model;


class Form {

    protected $images;

    protected $relatedProjects;

    protected $sku;

    protected $name;

    protected $inventory;

    protected $urlKey;

    protected $status;

    protected $manufacturer;

    protected $visibility;

    protected $condition;

    protected $taxClass;

    protected $stockStatus;

    protected $price;

    protected $cost;

    protected $rebatePrice;

    protected $rebateStartEndDate;

    protected $specialPrice;

    protected $specialStartEndDate;

    protected $mailInRebate;

    protected $mailInStartEndDate;

    protected $weight;

    protected $usExpedited;

    protected $usTwoDay;

    protected $canadaPriority;

    protected $canadaFirstClass;

    protected $asiaPriority;

    protected $asiaFirstClass;

    protected $europePriority;

    protected $europeFirstClass;

    protected $outsideAsiaPriority;

    protected $outsideAsiaFirstClass;

    protected $usOneDay;

    protected $usStandard;

    protected $metaTitle;

    protected $metaKeywords;

    protected $metaDescription;

    protected $description;

    public function __construct(Images $img, RelatedProducts $rp){
        $this->images = $img;
        $this->relatedProjects = $rp;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
//        echo "<pre>";
//        var_dump($images);
        foreach($images as $method => $values){
//            echo $method . "<br />";
            $setMethod = 'set'.ucfirst($method);
//            echo count($values);
//            var_dump($values);
//            foreach($values as $vals){
//                echo $setMethod  . ' ' . $vals. "<br />";
                $this->images->$setMethod($values);
//            }
//            $this->images->$setMethod()
        }
//        $setMethods = array();
//        var_dump(get_class_methods(get_class($this->images)));
//        $setMethods = get_class_methods(get_class($this->images));
//        foreach($setMethods as $imageMethod ){
//            if( preg_match('/setLabel/',$imageMethod)){
//                $this->images->$imageMethod($images['label']);
//            }
//            if( preg_match('/setLabel/',$imageMethod)){
//                $this->images->$imageMethod($images['label']);
//            }
//        }
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $relatedProjects
     */
    public function setRelatedProjects($relatedProjects)
    {
        $this->relatedProjects = $relatedProjects;
    }

    /**
     * @return mixed
     */
    public function getRelatedProjects()
    {
        return $this->relatedProjects;
    }

    //protected $attributes; // This might be an array for Categories

    //TODO: Add properties for Category form


    /**
     * @param mixed $asiaFirstClass
     */
    public function setAsiaFirstClass($asiaFirstClass)
    {
        $this->asiaFirstClass = $asiaFirstClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAsiaFirstClass()
    {
        return $this->asiaFirstClass;
    }

    /**
     * @param mixed $asiaPriority
     */
    public function setAsiaPriority($asiaPriority)
    {
        $this->asiaPriority = $asiaPriority;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getAsiaPriority()
    {
        return $this->asiaPriority;
    }

    /**
     * @param mixed $attributes
     */
//    public function setAttributes($attributes)
//    {
//        $this->attributes = $attributes;
//    }

    /**
     * @return mixed
     */
//    public function getAttributes()
//    {
//        return $this->attributes;
//    }

    /**
     * @param mixed $canadaFirstClass
     */
    public function setCanadaFirstClass($canadaFirstClass)
    {
        $this->canadaFirstClass = $canadaFirstClass;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getCanadaFirstClass()
    {
        return $this->canadaFirstClass;
    }

    /**
     * @param mixed $canadaPriority
     */
    public function setCanadaPriority($canadaPriority)
    {
        $this->canadaPriority = $canadaPriority;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getCanadaPriority()
    {
        return $this->canadaPriority;
    }

    /**
     * @param mixed $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $crossPosition
     */
//    public function setCrossPosition($crossPosition)
//    {
//        $this->crossPosition = $crossPosition;
//    }

    /**
     * @return mixed
     */
//    public function getCrossPosition()
//    {
//        return $this->crossPosition;
//    }

    /**
     * @param mixed $crossQty
     */
//    public function setCrossQty($crossQty)
//    {
//        $this->crossQty = $crossQty;
//    }

    /**
     * @return mixed
     */
//    public function getCrossQty()
//    {
//        return $this->crossQty;
//    }

    /**
     * @param mixed $crossSku
     */
//    public function setCrossSku($crossSku)
//    {
//        $this->crossSku = $crossSku;
//    }

    /**
     * @return mixed
     */
//    public function getCrossSku()
//    {
//        return $this->crossSku;
//    }

    /**
     * @param mixed $crossTitle
     */
//    public function setCrossTitle($crossTitle)
//    {
//        $this->crossTitle = $crossTitle;
//    }

    /**
     * @return mixed
     */
//    public function getCrossTitle()
//    {
//        return $this->crossTitle;
//    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $europeFirstClass
     */
    public function setEuropeFirstClass($europeFirstClass)
    {
        $this->europeFirstClass = $europeFirstClass;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getEuropeFirstClass()
    {
        return $this->europeFirstClass;
    }

    /**
     * @param mixed $europePriority
     */
    public function setEuropePriority($europePriority)
    {
        $this->europePriority = $europePriority;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getEuropePriority()
    {
        return $this->europePriority;
    }

    /**
     * @param mixed $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $mailInRebate
     */
    public function setMailInRebate($mailInRebate)
    {
        $this->mailInRebate = $mailInRebate;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getMailInRebate()
    {
        return $this->mailInRebate;
    }

    /**
     * @param mixed $mailInStartEndDate
     */
    public function setMailInStartEndDate($mailInStartEndDate)
    {
        $this->mailInStartEndDate = $mailInStartEndDate;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getMailInStartEndDate()
    {
        return $this->mailInStartEndDate;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param mixed $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param mixed $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $outsideAsiaFirstClass
     */
    public function setOutsideAsiaFirstClass($outsideAsiaFirstClass)
    {
        $this->outsideAsiaFirstClass = $outsideAsiaFirstClass;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getOutsideAsiaFirstClass()
    {
        return $this->outsideAsiaFirstClass;
    }

    /**
     * @param mixed $outsideAsiaPriority
     */
    public function setOutsideAsiaPriority($outsideAsiaPriority)
    {
        $this->outsideAsiaPriority = $outsideAsiaPriority;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getOutsideAsiaPriority()
    {
        return $this->outsideAsiaPriority;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $rebatePrice
     */
    public function setRebatePrice($rebatePrice)
    {
        $this->rebatePrice = $rebatePrice;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getRebatePrice()
    {
        return $this->rebatePrice;
    }

    /**
     * @param mixed $rebateStartEndDate
     */
    public function setRebateStartEndDate($rebateStartEndDate)
    {
        $this->rebateStartEndDate = $rebateStartEndDate;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getRebateStartEndDate()
    {
        return $this->rebateStartEndDate;
    }

    /**
     * @param mixed $relatedPosition
     */
//    public function setRelatedPosition($relatedPosition)
//    {
//        $this->relatedPosition = $relatedPosition;
//    }

    /**
     * @return mixed
     */
//    public function getRelatedPosition()
//    {
//        return $this->relatedPosition;
//    }

    /**
     * @param mixed $relatedQty
     */
//    public function setRelatedQty($relatedQty)
//    {
//        $this->relatedQty = $relatedQty;
//        return $this;
//    }

    /**
     * @return mixed
     */
//    public function getRelatedQty()
//    {
//        return $this->relatedQty;
//    }

    /**
     * @param mixed $relatedSku
     */
//    public function setRelatedSku($relatedSku)
//    {
//        $this->relatedSku = $relatedSku;
//    }

    /**
     * @return mixed
     */
//    public function getRelatedSku()
//    {
//        return $this->relatedSku;
//    }

    /**
     * @param mixed $relatedTitle
     */
//    public function setRelatedTitle($relatedTitle)
//    {
//        $this->relatedTitle = $relatedTitle;
//    }

    /**
     * @return mixed
     */
//    public function getRelatedTitle()
//    {
//        return $this->relatedTitle;
//    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $specialPrice
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @param mixed $specialStartEndDate
     */
    public function setSpecialStartEndDate($specialStartEndDate)
    {
        $this->specialStartEndDate = $specialStartEndDate;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getSpecialStartEndDate()
    {
        return $this->specialStartEndDate;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $stockStatus
     */
    public function setStockStatus($stockStatus)
    {
        $this->stockStatus = $stockStatus;
    }

    /**
     * @return mixed
     */
    public function getStockStatus()
    {
        return $this->stockStatus;
    }

    /**
     * @param mixed $taxClass
     */
    public function setTaxClass($taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * @return mixed
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * @param mixed $thumbnail
     */
//    public function setThumbnail($thumbnail)
//    {
//        $this->thumbnail = $thumbnail;
//    }

    /**
     * @return mixed
     */
//    public function getThumbnail()
//    {
//        return $this->thumbnail;
//    }

    /**
     * @param mixed $urlKey
     */
    public function setUrlKey($urlKey)
    {
        $this->urlKey = $urlKey;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getUrlKey()
    {
        return $this->urlKey;
    }

    /**
     * @param mixed $usExpedited
     */
    public function setUsExpedited($usExpedited)
    {
        $this->usExpedited = $usExpedited;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getUsExpedited()
    {
        return $this->usExpedited;
    }

    /**
     * @param mixed $usOneDay
     */
    public function setUsOneDay($usOneDay)
    {
        $this->usOneDay = $usOneDay;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getUsOneDay()
    {
        return $this->usOneDay;
    }

    /**
     * @param mixed $usStandard
     */
    public function setUsStandard($usStandard)
    {
        $this->usStandard = $usStandard;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getUsStandard()
    {
        return $this->usStandard;
    }

    /**
     * @param mixed $usTwoDay
     */
    public function setUsTwoDay($usTwoDay)
    {
        $this->usTwoDay = $usTwoDay;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getUsTwoDay()
    {
        return $this->usTwoDay;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        //return $this;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function __call($method, $arg){
        return;
    }
} 