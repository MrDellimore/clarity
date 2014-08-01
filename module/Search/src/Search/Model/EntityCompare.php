<?php
/**
 * User: wsalazar
 * Date: 7/8/14
 * Time: 7:32 PM
 */

namespace Search\Model;

use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;


class EntityCompare {

    public function dirtCheck($oldData, $newData){
        //dehydate objects to arrays
        $hydrator = new cHydrator;
        $oldData = $hydrator->extract($oldData);
        $newData = $hydrator->extract($newData);
        $dirt = Array();

        foreach($oldData as $key => $value){
            if($value != $newData[$key] && $value != null && $value != ''){
                $dirt[$key] = $newData[$key];
            }
            if(is_array($newData[$key])){
                if($value != $newData[$key][0] && $value != null && $value != ''){
                    $dirt[$key] = $newData[$key][0];
                }
            }
        }

        $dirtyEntity = new Form();
        $hydrator->hydrate($dirt,$dirtyEntity);
        $dirtyEntity->setId($oldData['id']);

        return $dirtyEntity;

    }

    public function newCheck($old, $newData){
        //dehydate objects to arrays
        $hydrator = new cHydrator;
        $old = $hydrator->extract($old);
        $newData = $hydrator->extract($newData);
        $newArray = Array();

        foreach($old as $key => $value){
            if(($newData[$key] !='' || $newData[$key] !='') && ($value == null || $value !='')){
                $newArray[$key] = $newData[$key];
            }
        }

        $newEntity = new Form();
        $hydrator->hydrate($newArray,$newEntity);
        $newEntity->setId($old['id']);
        return $newEntity;

    }
    public function determineQueryStatement($queriedData, $postedData){
        $dirtyFields = new Form();
        $cleanFields = new Form();
        $newFields = new Form();
        $queryMethods = $postMethods = array();
//        echo "----query------------<pre>";
//        var_dump($queriedData);
//        echo "---------post-------------<pre>";
//        var_dump($postedData);
        if(is_object($queriedData) || is_object($postedData)){
//            echo gettype($queriedData) . ' ' . gettype($postedData) . "<br />";
            $queryMethods = get_class_methods(get_class($queriedData));
//            echo get_class($queriedData) . "<br />";
            $postMethods = get_class_methods(get_class($postedData));
            for($i = 0; $i < count($queryMethods); $i++){
//                echo $queryMethods[$i] . ' ' . $postMethods[$i]. "<br />";
                if( preg_match('/get/',$queryMethods[$i]) ){
                    if(is_object($queriedData->$queryMethods[$i]()) || is_object($postedData->$postMethods[$i]()) ){
                        $queryObject = $queriedData->$queryMethods[$i]();
                        $postObject = $postedData->$postMethods[$i]();
//                            var_dump($queryObject);
//                            var_dump($postObject);
//                            $this->determineQueryStatement($queryObject, $postObject);
                    } else{
                        $queryData = $queriedData->$queryMethods[$i]();
                        $postData = $postedData->$postMethods[$i]();
                        if($queryData == $postData){
                            if(is_array($queriedData->$queryMethods[$i]()) || is_array($postedData->$postMethods[$i]()) ){
                                //if($queriedData->$queryMethods[$i]() ==
                                continue;
                            } else{
                                $setCleanMethod = str_replace('get', 'set', $queryMethods[$i]);
                                $cleanFields->$setCleanMethod($queriedData->$queryMethods[$i]());
//                                echo 'Clean ' . ' ' . $queriedData->$queryMethods[$i]() . ' ' .$postedData->$postMethods[$i]() . "<br />";
                         }
                        } else{
                            if(is_array($queriedData->$queryMethods[$i]()) || is_array($postedData->$postMethods[$i]()) ){
                                //if($queriedData->$queryMethods[$i]() ==
                                continue;
                            } else{
//                                     echo lcfirst(substr($queryMethods[$i],3));
//                                    echo 'Dirty' . ' ' . $queriedData->$queryMethods[$i]() . ' ' .$postedData->$postMethods[$i]() . "<br />";
                                $setDirtyMethod = str_replace('get', 'set', $queryMethods[$i]);
                                $dirtyFields->$setDirtyMethod($postedData, $postedData->$postMethods[$i]());
//                                    echo 'this is the get method ' . $postMethods[$i] . ' ' . $dirtyFields->$postMethods[$i]() . "<br />";
                            }
                        }
                        if($queryData == null){
                            $setNewMethod = str_replace('get', 'set', $queryMethods[$i]);
//                                echo $setNewMethod . "<br />";
//                                echo "this is the new posted data " . $postMethods[$i] . " " . $postedData->$postMethods[$i]() . "<br />";
//                                echo "This is the setter " . $setNewMethod . "<br />";
                            $newFields->$setNewMethod($postedData, $postedData->$postMethods[$i]());
                        }
                    }
                }
            }
        }

        //set id for dirty
        $dirtyFields->setID($queriedData->getID());

        return $dirtyFields;
    }
}