<?php


namespace Content\ContentForm\Entity;


class Category{

    protected $id;

    protected $categoryid;

    /**
     * @param mixed $categoryid
     */
    public function setCategoryid($categoryid)
    {
        $this->categoryid = $categoryid;
    }

    /**
     * @return mixed
     */
    public function getCategoryid()
    {
        return $this->categoryid;
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


}