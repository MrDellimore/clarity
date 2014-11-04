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
        $dirt = $this->getDirty($oldData,$newData);

//        echo '<pre>';
//        var_dump($dirt);
//        var_dump($oldData['image_gallery']);
//        var_dump($newData['image_gallery']);
//        die();

        $dirtyEntity = new Form();
        $hydrator->hydrate($dirt,$dirtyEntity);
        $dirtyEntity->setId($oldData['id']);
//        echo '<pre>';
//        var_dump($dirtyEntity);
//        die();
        return $dirtyEntity;
    }

    public function getDirty($oldData,$newData){
        $dirtyArray = Array();
        $hydrator = new cHydrator;

        //loop through oldData for changes
        foreach($oldData as $key => $value){
            //begin comparison if key exists in new data
            if(array_key_exists($key,$newData)){


                //arrays and object comprison
                if(is_array($value)){
                    //option comparison
                    if(array_key_exists('option',$value)){
                        if($value['option'] != $newData[$key]['option'] && !(is_null($value['option'])) && $value['option'] != ""){
                            $dirtyArray[$key] = $newData[$key];
                        }
                    }

                    //object comparison
                    else{
                        //loop though collection of objects
                        $unsetnodeFlag=true;
                        foreach($value as $key2 => $value2){
                            //extract current object
                            if(is_object($value2)){
                                $value2 = $hydrator->extract($value2);
                                //$newData[$key][$key2] = $hydrator->extract($newData[$key][$key2]);
                            }

                                //loop through current object and find matching object in new data
                                foreach($newData[$key] as $value3){
                                    //extract new data object
                                    if(is_object($value3)){
                                        $value3 = $hydrator->extract($value3);

                                        //when matching object is found compare
                                        if($value2['id'] == $value3['id']){
                                            //set id of matched object
                                            $dirtyArray[$key][$key2]['id'] = $value2['id'];
                                            $unsetidFlag = true;
                                            //compare objects
                                            foreach($value2 as $key4 => $value4){
                                                if($value4 != $value3[$key4] && $key4 != 'id'){
                                                    $dirtyArray[$key][$key2][$key4] = $value3[$key4];
                                                    $unsetidFlag = false;
                                                }
                                            }

                                            //unset id if nothing is found
                                            if($unsetidFlag){
                                                unset($dirtyArray[$key][$key2]);
                                            }
                                            else{
                                                //found one id keep node
                                                $unsetnodeFlag=false;
                                            }

                                            //unset object node if nothing is found
                                            if($unsetnodeFlag){
                                                unset($dirtyArray[$key]);
                                            }
                                        }
                                    }
                                }

                        }
                    }
                }

                //standard comparison
                else if(strip_tags(html_entity_decode($value)) != strip_tags(html_entity_decode($newData[$key])) && !(is_null($newData[$key]) && $value != null )){
                    $dirtyArray[$key] = $newData[$key];
                }
            }
        }


        return $dirtyArray;
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

        //var_dump($newEntity->getSpecialPrice());
        //die();
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