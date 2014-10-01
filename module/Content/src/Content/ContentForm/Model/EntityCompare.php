<?php
/**
 * User: wsalazar
 * Date: 7/8/14
 * Time: 7:32 PM
 */

namespace Content\ContentForm\Model;

use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Content\ContentForm\Entity\Products as Form;



class EntityCompare {



    /*
     * returns Form entity with properties that need to be updated
     */
    public function dirtCheck($oldData, $newData){
        //dehydate objects to arrays
        $hydrator = new cHydrator;
        if(is_object($oldData)){
            $oldData = $hydrator->extract($oldData);
        }
        if(is_object($newData)){
            $newData = $hydrator->extract($newData);
        }

        //compare arrays
        $dirt = $this->getDirtArray($oldData,$newData);


        $dirtyEntity = new Form();
        $hydrator->hydrate($dirt,$dirtyEntity);

        $dirtyEntity->setId($oldData['id']);
        //var_dump($oldData['zoom_focal_length']);
        //var_dump($newData['color']);
//        var_dump($dirt);
//        die();



        return $dirtyEntity;
    }


/*
 * todo make dirtcomparison iterative
 */

    public function getDirtArray($oldData,$newData){
        $dirt = Array();
        $hydrator = new cHydrator;
        foreach($oldData as $key => $value){
             if(array_key_exists($key,$newData)){
                //recursively if item is an object
                if(is_object($newData[$key])){
                    //hydrate and call again
                    $value = $hydrator->extract($value);
                    $newData[$key] = $hydrator->extract($newData[$key]);

                    $recursiveresult = $this->getDirtArray($value,$newData[$key]);

                    if(!(empty($recursiveresult))){
                        $dirt[$key] = $recursiveresult;
                    }
                }

                //recursively call if array
                if(is_array($newData[$key]) && !(is_object($newData[$key]))  ){
                    $recursiveresult = $this->getDirtArray($value,$newData[$key]);

                    if(!(empty($recursiveresult))){
                        $dirt[$key] = $recursiveresult;
                    }
                }

                //actual dirty comparison
                if(($value != $newData[$key] && $value != null && $value != '' && !(is_array($newData[$key]))) ||($key == 'id' && !(is_array($newData[$key])))) {
                    $dirt[$key] = $newData[$key];
                }
            }
        }
        return $dirt;
    }




    /*
     * returns and entity with properties that need to be inserted
     */
    public function newCheck($old, $newData){
        //dehydate objects to arrays
        $hydrator = new cHydrator;
        $old = $hydrator->extract($old);
        $newData = $hydrator->extract($newData);

        $newArray = $this->getNewArray($old,$newData);

        $newEntity = new Form();
        $hydrator->hydrate($newArray,$newEntity);

        $newEntity->setId($old['id']);
        return $newEntity;

    }

    public function getNewArray($oldData,$newData){
        $newArray = Array();
        $hydrator = new cHydrator;
        //loop through newArray
        foreach($newData as $key => $value){

            if(is_array($value)){
                if(array_key_exists($key,$oldData) && !(empty($oldData[$key])) ){
                    foreach($value as $key2 => $newValue){
                        $newFlag = true;
                        if(is_object($newValue)){
                            $newValue = $hydrator->extract($newValue);
                        }



                        foreach($oldData[$key] as $keyold => $oldValue){
                            if(is_object($oldValue)){
                                $oldValue = $hydrator->extract($oldValue);
                            }

                            //find ids of objects.. If not there then set flag
                            if(is_array($oldValue) && is_array($newValue)){
                                //check for new objects
                                if($newValue['id'] == $oldValue['id']){
                                    $newFlag = false;
                                }
                            }

                            if(array_key_exists('option',$value)){
                                if($keyold == 'option' && ($value['option'] == $oldValue || $oldValue != "" || $value['option'] == "0")){
                                        $newFlag = false;
                                }
                            }
                        }
                        if($newFlag){
                            $newArray[$key][$key2] = $newValue;
                        }
                    }
                }
                else{
                    //subEntity/array doesnt exist in other array at all
                    foreach($value as $nonExistValue){
                        if(is_object($nonExistValue)){
                            $nonExistValue = $hydrator->extract($nonExistValue);
                        }
                        $newArray[$key][] = $nonExistValue;
                    }
                }
            }

            //not an array or object
            else{
                if(($value != null && $value != '' && !(is_array($oldData[$key])) && $oldData[$key] == null) || ( !(array_key_exists($key,$oldData)) ) ){

                    $newArray[$key] = $value;
                }
            }
        }
        return $newArray;

    }

    public function rinseCheck($oldData,$newData){

        return $this->newCheck($newData,$oldData);

    }

}