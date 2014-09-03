<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/29/14
 * Time: 11:23 AM
 */

namespace Content\ManageAttributes\Entity;


class Options {

    protected $_options = array();

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