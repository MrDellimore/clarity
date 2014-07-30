<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/9/14
 * Time: 12:50 PM
 */

namespace Search\Model;


class Images {

    protected $label = array();

    protected $fileSize = array();

    protected $sortOrder = array();

    protected $thumb = array();

    protected $smallImage = array();

    protected $baseImage = array();

    /**
     * @param mixed $baseImage
     */
    public function setBaseImage($baseImage)
    {
        $this->baseImage = $baseImage;
    }

    /**
     * @return mixed
     */
    public function getBaseImage()
    {
        return $this->baseImage;
    }

    /**
     * @param mixed $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
//        echo "<pre>";
//        var_dump($label);
//        echo 'hahahahaha';
//        echo "<pre>";
//        var_dump($label);
//        echo 'helloworld';

//        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $smallImage
     */
    public function setSmallImage($smallImage)
    {
//        echo "<pre>";
//        var_dump($smallImage);
        $this->smallImage = $smallImage;
    }

    /**
     * @return mixed
     */
    public function getSmallImage()
    {
        return $this->smallImage;
    }

    /**
     * @param mixed $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return mixed
     */
    public function getThumb()
    {
        return $this->thumb;
    }

} 