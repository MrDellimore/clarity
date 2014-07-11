<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/10/14
 * Time: 8:58 AM
 */

namespace Search\Model;


class DirtyFields {

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

    protected $dirtyField;

    /**
     * @param mixed $asiaFirstClass
     */
    public function setAsiaFirstClass($asiaFirstClass)
    {
        $this->asiaFirstClass = $asiaFirstClass;
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
    }

    /**
     * @return mixed
     */
    public function getAsiaPriority()
    {
        return $this->asiaPriority;
    }

    /**
     * @param mixed $canadaFirstClass
     */
    public function setCanadaFirstClass($canadaFirstClass)
    {
        $this->canadaFirstClass = $canadaFirstClass;
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
    public function setCost($dirtyField, $cost)
    {
        $this->dirtyField = $dirtyField;
        $this->dirtyField->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

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
    }

    /**
     * @return mixed
     */
    public function getRebateStartEndDate()
    {
        return $this->rebateStartEndDate;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
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
     * @param mixed $urlKey
     */
    public function setUrlKey($urlKey)
    {
        $this->urlKey = $urlKey;
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
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }



}