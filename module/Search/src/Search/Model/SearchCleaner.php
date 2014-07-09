<?php
/**
 * User: wsalazar
 * Date: 7/8/14
 * Time: 7:32 PM
 */

namespace Search\Model;

use Search\Model\Form;

class SearchCleaner {

//    public function isDirty($query){
//        echo 'this is url key ' . $query->getUrlKey();
//    }

    public function dirty($queriedData, $postedData){
        $queryMethods = $postMethods = array();
//        echo "----query------------<pre>";
//        var_dump($queriedData);
//        echo "---------post-------------<pre>";
//        var_dump($postedData);
        if(is_object($queriedData) || is_object($postedData)){
            echo gettype($queriedData) . ' ' . gettype($postedData) . "<br />";
            $queryMethods = get_class_methods(get_class($queriedData));
            $postMethods = get_class_methods(get_class($postedData));
            for($i = 0; $i < count($queryMethods); $i++){
//                echo $queryMethods[$i] . ' ' . $postMethods[$i]. "<br />";
                if( preg_match('/get/',$queryMethods[$i]) ){
                        if(is_object($queriedData->$queryMethods[$i]()) || is_object($postedData->$postMethods[$i]()) ){
                            $queryObject = $queriedData->$queryMethods[$i]();
                            $postObject = $postedData->$postMethods[$i]();
//                            var_dump($queryObject);
//                            var_dump($postObject);
                            $this->dirty($queryObject, $postObject);
                        } else{
                            $queryData = $queriedData->$queryMethods[$i]();
                            $postData = $postedData->$postMethods[$i]();
                            if($queryData == $postData){
                                if(is_array($queriedData->$queryMethods[$i]()) || is_array($postedData->$postMethods[$i]()) ){
                                    //if($queriedData->$queryMethods[$i]() ==
                                    continue;
                                } else{
                                echo 'Clean ' . ' ' . $queriedData->$queryMethods[$i]() . ' ' .$postedData->$postMethods[$i]() . "<br />";
                             }
                            } else{
                                if(is_array($queriedData->$queryMethods[$i]()) || is_array($postedData->$postMethods[$i]()) ){
                                    //if($queriedData->$queryMethods[$i]() ==
                                    continue;
                                } else{
                                    echo 'Dirty' . ' ' . $queriedData->$queryMethods[$i]() . ' ' .$postedData->$postMethods[$i]() . "<br />";
                                }
//                                echo 'Dirty';
                        }
                    }
                }
            }
//            echo "<pre>";
//            var_dump($queryMethods);
//            echo "<pre>";
//            var_dump($postMethods);

        }
        if($queriedData ==  $postedData){
            echo 'haha';
        }


//        $changedData = array();
//        $changedData = array_diff_assoc($postedData,$queriedData);
    }

    public function clean($queriedData, $postedData){
//        $newData = array();
//        $newData = array_diff($postedData,$queriedData);
//        echo "--new--<br /><pre>";
//        var_dump($newData);
//        echo "--new--<br />";
//        echo "--query--<br /><pre>";
//        var_dump($queriedData);
//        echo "--query--<br />";
//        echo "--post--<br /><pre>";
//        var_dump($postedData);
//        echo "--post--<br />";

    }



}