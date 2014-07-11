<?php
/**
 * User: wsalazar
 * Date: 7/8/14
 * Time: 7:32 PM
 */

namespace Search\Model;

class SearchCleaner {

//    public function isDirty($query){
//        echo 'this is url key ' . $query->getUrlKey();
//    }

    public function determineQueryStatement($queriedData, $postedData){
        $dirtyFields = new DirtyFields();
        $cleanFields = new CleanFields();
        $newFields = new NewFields();
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
    }
}