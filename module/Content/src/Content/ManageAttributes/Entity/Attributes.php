<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/29/14
 * Time: 11:23 AM
 */

namespace Content\ManageAttributes\Entity;


class Attributes {

    protected $_attributes;

    protected $_options = array();

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }



} 