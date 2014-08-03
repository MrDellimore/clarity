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
}