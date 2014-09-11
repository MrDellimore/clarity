<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 6/24/14
 * Time: 4:51 PM
 */

namespace Content\ContentForm\Entity;


use Zend\Stdlib\Hydrator\ClassMethods;


class Products {

    protected $id;

    protected $sku;

    protected $title;

    protected $inventory;

    protected $urlKey;

    protected $status;

    protected $manufacturer = array();

    protected $visibility;

    protected $condition;

    protected $taxClass;

    protected $stockStatus;

    protected $price;

    protected $msrp;

    protected $mapDisplay;

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

    protected $inBox;

    protected $shortDescription;

    protected $includesFree;

    protected $color;

    protected $packs;

    protected $capacity;

    protected $network;

    protected $sizeCloths;

    protected $sizeSunglasses;

    protected $resolution;

    protected $sensorSize;

    protected $opticalDesign;

    protected $flashType;

    protected $power;

    protected $objectiveDiameter;

    protected $useBinoculars;

    protected $size;

    protected $cameraStyle = array();

    protected $videoResolution;

    protected $zoomPrime;

    protected $primeFocalLength = array();

    protected $zoomFocalLength = array();

    protected $cineLens;

    protected $aperture = array();

    protected $legLockTypeTripod;

    protected $maxHeightTripod;

    protected $foldedLengthTripod;

    protected $materialTripod;

    protected $typeOfBag;

    protected $headType;

    protected $supportWeightTripod;

    protected $prismType;

    protected $brand = array();

    protected $website;

    protected $contentReviewed;

    protected $originalContent;

    protected $thumbnail;

    protected $thumbnail_label;

    protected $small_image;

    protected $small_image_label;

    protected $image;

    protected $image_label;

    protected $categories = array();

    protected $imageGallery = array();

    protected $accessories = array();

    protected $crossSells = array();

    /**
     * @param array $acessories
     */
    public function setAcessories($accessories)
    {
        $hydrator = new ClassMethods();
        foreach ($accessories as  $value) {
            $this->accessories[] = $hydrator->hydrate($value, new Accessories());
        }

    }

    /**
     * @return array
     */
    public function getAccessories()
    {
        return $this->accessories;
    }

    /**
     * @param array $aperture
     */
    public function setAperture($aperture)
    {
        $this->aperture = $aperture;
    }

    /**
     * @return array
     */
    public function getAperture()
    {
        return $this->aperture;
    }

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
     * @param array $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return array
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param array $cameraStyle
     */
    public function setCameraStyle($cameraStyle)
    {
        $this->cameraStyle = $cameraStyle;
    }

    /**
     * @return array
     */
    public function getCameraStyle()
    {
        return $this->cameraStyle;
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
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $hydrator = new ClassMethods();
        foreach ($categories as  $value) {
            $this->categories[] = $hydrator->hydrate($value, new Category());
        }

    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $cineLens
     */
    public function setCineLens($cineLens)
    {
        $this->cineLens = $cineLens;
    }

    /**
     * @return mixed
     */
    public function getCineLens()
    {
        return $this->cineLens;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
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
     * @param mixed $contentReviewed
     */
    public function setContentReviewed($contentReviewed)
    {
        $this->contentReviewed = $contentReviewed;
    }

    /**
     * @return mixed
     */
    public function getContentReviewed()
    {
        return $this->contentReviewed;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param array $crossSells
     */
    public function setCrossSells($crossSells)
    {
        $hydrator = new ClassMethods();
        foreach ($crossSells as  $value) {
            $this->crossSells[] = $hydrator->hydrate($value, new CrossSell());
        }
    }

    /**
     * @return array
     */
    public function getCrossSells()
    {
        return $this->crossSells;
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
     * @param mixed $flashType
     */
    public function setFlashType($flashType)
    {
        $this->flashType = $flashType;
    }

    /**
     * @return mixed
     */
    public function getFlashType()
    {
        return $this->flashType;
    }

    /**
     * @param mixed $foldedLengthTripod
     */
    public function setFoldedLengthTripod($foldedLengthTripod)
    {
        $this->foldedLengthTripod = $foldedLengthTripod;
    }

    /**
     * @return mixed
     */
    public function getFoldedLengthTripod()
    {
        return $this->foldedLengthTripod;
    }

    /**
     * @param mixed $headType
     */
    public function setHeadType($headType)
    {
        $this->headType = $headType;
    }

    /**
     * @return mixed
     */
    public function getHeadType()
    {
        return $this->headType;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param array $imageGallery
     */
    public function setImageGallery($imageGallery)
    {
        $hydrator = new ClassMethods();
        foreach ($imageGallery as  $value) {
            $this->imageGallery[] = $hydrator->hydrate($value, new Images());
        }

    }

    /**
     * @return array
     */
    public function getImageGallery()
    {
        return $this->imageGallery;
    }

    /**
     * @param mixed $image_label
     */
    public function setImageLabel($image_label)
    {
        $this->image_label = $image_label;
    }

    /**
     * @return mixed
     */
    public function getImageLabel()
    {
        return $this->image_label;
    }

    /**
     * @param mixed $inBox
     */
    public function setInBox($inBox)
    {
        $this->inBox = $inBox;
    }

    /**
     * @return mixed
     */
    public function getInBox()
    {
        return $this->inBox;
    }

    /**
     * @param mixed $includesFree
     */
    public function setIncludesFree($includesFree)
    {
        $this->includesFree = $includesFree;
    }

    /**
     * @return mixed
     */
    public function getIncludesFree()
    {
        return $this->includesFree;
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
     * @param mixed $legLockTypeTripod
     */
    public function setLegLockTypeTripod($legLockTypeTripod)
    {
        $this->legLockTypeTripod = $legLockTypeTripod;
    }

    /**
     * @return mixed
     */
    public function getLegLockTypeTripod()
    {
        return $this->legLockTypeTripod;
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
     * @param array $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return array
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $mapDisplay
     */
    public function setMapDisplay($mapDisplay)
    {
        $this->mapDisplay = $mapDisplay;
    }

    /**
     * @return mixed
     */
    public function getMapDisplay()
    {
        return $this->mapDisplay;
    }

    /**
     * @param mixed $materialTripod
     */
    public function setMaterialTripod($materialTripod)
    {
        $this->materialTripod = $materialTripod;
    }

    /**
     * @return mixed
     */
    public function getMaterialTripod()
    {
        return $this->materialTripod;
    }

    /**
     * @param mixed $maxHeightTripod
     */
    public function setMaxHeightTripod($maxHeightTripod)
    {
        $this->maxHeightTripod = $maxHeightTripod;
    }

    /**
     * @return mixed
     */
    public function getMaxHeightTripod()
    {
        return $this->maxHeightTripod;
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
     * @param mixed $msrp
     */
    public function setMsrp($msrp)
    {
        $this->msrp = $msrp;
    }

    /**
     * @return mixed
     */
    public function getMsrp()
    {
        return $this->msrp;
    }

    /**
     * @param mixed $network
     */
    public function setNetwork($network)
    {
        $this->network = $network;
    }

    /**
     * @return mixed
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @param mixed $objectiveDiameter
     */
    public function setObjectiveDiameter($objectiveDiameter)
    {
        $this->objectiveDiameter = $objectiveDiameter;
    }

    /**
     * @return mixed
     */
    public function getObjectiveDiameter()
    {
        return $this->objectiveDiameter;
    }

    /**
     * @param mixed $opticalDesign
     */
    public function setOpticalDesign($opticalDesign)
    {
        $this->opticalDesign = $opticalDesign;
    }

    /**
     * @return mixed
     */
    public function getOpticalDesign()
    {
        return $this->opticalDesign;
    }

    /**
     * @param mixed $originalContent
     */
    public function setOriginalContent($originalContent)
    {
        $this->originalContent = $originalContent;
    }

    /**
     * @return mixed
     */
    public function getOriginalContent()
    {
        return $this->originalContent;
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
     * @param mixed $packs
     */
    public function setPacks($packs)
    {
        $this->packs = $packs;
    }

    /**
     * @return mixed
     */
    public function getPacks()
    {
        return $this->packs;
    }

    /**
     * @param mixed $power
     */
    public function setPower($power)
    {
        $this->power = $power;
    }

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
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
     * @param array $primeFocalLength
     */
    public function setPrimeFocalLength($primeFocalLength)
    {
        $this->primeFocalLength = $primeFocalLength;
    }

    /**
     * @return array
     */
    public function getPrimeFocalLength()
    {
        return $this->primeFocalLength;
    }

    /**
     * @param mixed $prismType
     */
    public function setPrismType($prismType)
    {
        $this->prismType = $prismType;
    }

    /**
     * @return mixed
     */
    public function getPrismType()
    {
        return $this->prismType;
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
     * @param mixed $resolution
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
    }

    /**
     * @return mixed
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @param mixed $sensorSize
     */
    public function setSensorSize($sensorSize)
    {
        $this->sensorSize = $sensorSize;
    }

    /**
     * @return mixed
     */
    public function getSensorSize()
    {
        return $this->sensorSize;
    }

    /**
     * @param mixed $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $sizeCloths
     */
    public function setSizeCloths($sizeCloths)
    {
        $this->sizeCloths = $sizeCloths;
    }

    /**
     * @return mixed
     */
    public function getSizeCloths()
    {
        return $this->sizeCloths;
    }

    /**
     * @param mixed $sizeSunglasses
     */
    public function setSizeSunglasses($sizeSunglasses)
    {
        $this->sizeSunglasses = $sizeSunglasses;
    }

    /**
     * @return mixed
     */
    public function getSizeSunglasses()
    {
        return $this->sizeSunglasses;
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
     * @param mixed $small_image
     */
    public function setSmallImage($small_image)
    {
        $this->small_image = $small_image;
    }

    /**
     * @return mixed
     */
    public function getSmallImage()
    {
        return $this->small_image;
    }

    /**
     * @param mixed $small_image_label
     */
    public function setSmallImageLabel($small_image_label)
    {
        $this->small_image_label = $small_image_label;
    }

    /**
     * @return mixed
     */
    public function getSmallImageLabel()
    {
        return $this->small_image_label;
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
     * @param mixed $supportWeightTripod
     */
    public function setSupportWeightTripod($supportWeightTripod)
    {
        $this->supportWeightTripod = $supportWeightTripod;
    }

    /**
     * @return mixed
     */
    public function getSupportWeightTripod()
    {
        return $this->supportWeightTripod;
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
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $thumbnail_label
     */
    public function setThumbnailLabel($thumbnail_label)
    {
        $this->thumbnail_label = $thumbnail_label;
    }

    /**
     * @return mixed
     */
    public function getThumbnailLabel()
    {
        return $this->thumbnail_label;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $typeOfBag
     */
    public function setTypeOfBag($typeOfBag)
    {
        $this->typeOfBag = $typeOfBag;
    }

    /**
     * @return mixed
     */
    public function getTypeOfBag()
    {
        return $this->typeOfBag;
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
     * @param mixed $useBinoculars
     */
    public function setUseBinoculars($useBinoculars)
    {
        $this->useBinoculars = $useBinoculars;
    }

    /**
     * @return mixed
     */
    public function getUseBinoculars()
    {
        return $this->useBinoculars;
    }

    /**
     * @param mixed $videoResolution
     */
    public function setVideoResolution($videoResolution)
    {
        $this->videoResolution = $videoResolution;
    }

    /**
     * @return mixed
     */
    public function getVideoResolution()
    {
        return $this->videoResolution;
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
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
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

    /**
     * @param array $zoomFocalLength
     */
    public function setZoomFocalLength($zoomFocalLength)
    {
        $this->zoomFocalLength = $zoomFocalLength;
    }

    /**
     * @return array
     */
    public function getZoomFocalLength()
    {
        return $this->zoomFocalLength;
    }

    /**
     * @param mixed $zoomPrime
     */
    public function setZoomPrime($zoomPrime)
    {
        $this->zoomPrime = $zoomPrime;
    }

    /**
     * @return mixed
     */
    public function getZoomPrime()
    {
        return $this->zoomPrime;
    }





} 
