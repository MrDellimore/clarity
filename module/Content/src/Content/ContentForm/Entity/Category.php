<?php


namespace Content\ContentForm\Entity;


class Category{

    protected $entityid;

    protected $id;

    /**
     * @param mixed $entityid
     */
    public function setEntityid($entityid)
    {
        $this->entityid = $entityid;
    }

    /**
     * @return mixed
     */
    public function getEntityid()
    {
        return $this->entityid;
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