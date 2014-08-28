<?php
/**
 * User: wsalazar
 * Date: 7/8/14
 * Time: 7:32 PM
 */

namespace Search\Model;

use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Search\Entity\Form;



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

        //loop though image gallery
        //if image entity



        return $dirtyEntity;
    }




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
            //set items to array if object
            /*
            if(is_object($value)){
                $value = $hydrator->extract($value);
                $oldData[$key] = $hydrator->extract($oldData[$key]);
            }
*/
            if(is_array($value)){
                if(array_key_exists($key,$oldData) && !(empty($oldData[$key])) ){
                    foreach($value as $key2 => $newValue){
                        $newFlag = true;
                        if(is_object($newValue)){
                            $newValue = $hydrator->extract($newValue);
                        }

                        foreach($oldData[$key] as $oldValue){
                            if(is_object($oldValue)){
                                $oldValue = $hydrator->extract($oldValue);
                            }

                            //var_dump($newValue['id']);
                            if($newValue['id'] == $oldValue['id']){
                                $newFlag = false;
                            }
                        }
                        if($newFlag){
                            $newArray[$key][$key2] = $newValue;
                        }
                    }
                }
                else
                    $newArray[$key] = $value;
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