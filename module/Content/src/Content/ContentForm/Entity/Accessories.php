<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/9/14
 * Time: 12:46 PM
 */

namespace Content\ContentForm\Entity;


class Accessories {

    protected $id;

    protected $entityid;

    protected $linkedSku;

    protected $position;

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

    /**
     * @param mixed $linkedSku
     */
    public function setLinkedSku($linkedSku)
    {
        $this->linkedSku = $linkedSku;
    }

    /**
     * @return mixed
     */
    public function getLinkedSku()
    {
        return $this->linkedSku;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }




} 