<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 12/11/14
 * Time: 11:35 AM
 */

namespace Marketing\Deals\Entity;


class Deals {

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var string
     */
    protected $specialPrice;

    /**
     * @var int
     */
    protected $inventory;

    /**
     * @var string
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $endDate;

    /**
     * @var int
     */
    protected $maxQty;

    /**
     * @var string
     */
    protected $usStandard;

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param int $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return int
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param int $maxQty
     */
    public function setMaxQty($maxQty)
    {
        $this->maxQty = $maxQty;
    }

    /**
     * @return int
     */
    public function getMaxQty()
    {
        return $this->maxQty;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $specialPrice
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * @return string
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $usStandard
     */
    public function setUsStandard($usStandard)
    {
        $this->usStandard = $usStandard;
    }

    /**
     * @return string
     */
    public function getUsStandard()
    {
        return $this->usStandard;
    }
} 