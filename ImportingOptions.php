<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/18/14
 * Time: 2:49 PM
 */

include 'config/autoload/local.php';
include 'init_autoloader.php';
$options = [
    ['zoom'=>'10-24mm'],
    ['zoom'=>'10-30mm'],
    ['zoom'=>'11-27.5mm'],
    ['zoom'=>'14-24mm'],
    ['zoom'=>'16-80mm'],
    ['zoom'=>'16-105mm'],
    ['zoom'=>'18-35mm'],
    ['zoom'=>'18-105mm'],
    ['zoom'=>'18-140mm'],
    ['zoom'=>'18-200mm'],
    ['zoom'=>'18-250mm'],
    ['zoom'=>'18-300mm'],
    ['zoom'=>'19-300mm'],
    ['zoom'=>'24-77mm'],
    ['zoom'=>'24-85mm'],
    ['zoom'=>'28-70mm'],
    ['zoom'=>'28-75mm'],
    ['zoom'=>'28-135mm'],
    ['zoom'=>'28-300mm'],
    ['zoom'=>'30-110mm'],
    ['zoom'=>'55-210mm'],
    ['zoom'=>'70-400mm'],
    ['zoom'=>'85-250mm'],
    ['prime'=>'10mm'],
    ['prime'=>'16mm'],
    ['prime'=>'20mm'],
    ['prime'=>'30mm'],
    ['prime'=>'105mm'],
    ['prime'=>'135mm'],
    ['prime'=>'18.5mm'],
    ['prime'=>'55mm'],
];

//var_dump($options);
//die();
//$dsn = 'mysql:dbname='.DB.';host='.HOST;
//$dbh = new PDO($dsn, USER, PASS, array( PDO::ATTR_PERSISTENT => false));
//
//try{
//    foreach( $options as $attribute => $option ) {
//        foreach ( $option as $att => $opVal ) {
//            if( $att == 'zoom') {
//                $select = "select * from spex.productattribute_option where attribute_id = 1731 and value = '" . $opVal . "'";
//                foreach ( $dbh->query($select) as $row ) {
//                    if ( $row['value'] == $opVal ) {
//                        echo "zoom exists\n";
//                    } else {
//                        echo "zoom not exists\n";
//                        $insert = "insert into productattribute_option (attribute_id, value, dataState,lastModifiedDate,changedby)
//                                                    value(:attribute_id, :value, :dataState, :lastModDate, :changedby)";
////                                                    value('1731'," . $opVal . ",'0','".date('Y-m-j')."',0)";
////                        $query = $dbh->prepare($insert);
////                        $query->execute([
////                            ':attribute_id'=>1731,
////                            ':value'=>$opVal,
////                            ':dataState'=>'0',
////                            ':lastModDate'=>date('Y-m-j'),
////                            ':changedby'=>'0',
////                        ]);
//                    }
//                }
//            }
//            if( $att == 'prime') {
//                $select = "select * from spex.productattribute_option where attribute_id = 1713 and value = '" . $opVal . "'";
//                foreach ( $dbh->query($select) as $row ){
//                    if ( $row['value'] == $opVal ) {
//                        echo "prime exists\n";
//                    } else {
//                        $insert = "insert into productattribute_option (attribute_id, value, dataState,lastModifiedDate,changedby)
//                                                    value(:attribute_id, :value, :dataState, :lastModDate, :changedby)";
//                        echo "prime not exists\n";
////                        $query = $dbh->prepare($insert);
////                        $query->execute([
////                            ':attribute_id'=>1713,
////                            ':value'=>$opVal,
////                            ':dataState'=>'0',
////                            ':lastModDate'=>date('Y-m-j'),
////                            ':changedby'=>'0',
////                        ]);
//                    }
//                }
//            }
//        }
//    }
//} catch (PDOException $e) {
//    echo 'Connection failed: ' . $e->getMessage();
//}
$conn = mysqli_connect(HOST, USER, PASS, DB);
foreach( $options as $attribute => $option ) {
    foreach ( $option as $att => $opVal ) {
        if( $att == 'zoom' ) {
//            $insert = "INSERT INTO productattribute_option (attribute_id, value, dataState,lastModifiedDate,changedby)
//                            SELECT 1731, '" . $opVal . "', 0, '" . date('Y-m-j') . "', 0 from productattribute_option
//                            where value <> '" . $opVal . "'";
            $select = "SELECT 1731, '" . $opVal . "', 0, '" . date('Y-m-j') . "', 0 from productattribute_option
                            where value <> '" . $opVal . "'";
//            $select = "select * from spex.productattribute_option where attribute_id = 1731 and value <> '" . $opVal . "'";
            $query = mysqli_query($conn,$select);
            $fetchedOption = mysqli_fetch_row($query);
            var_dump($fetchedOption);

        }
//        if( $att == 'prime' ) {
//
//        }
//        $select = "SELECT entity_id from spex.productattribute_option where productid = '" . $option['prime'] . "'";
//        if( !mysqli_query($conn,$select) ){
//            die(mysqli_error($conn));
//        } else {
//            $prd = mysqli_query($conn,$select);
//            $fetchedPrd = mysqli_fetch_row($prd);
//            echo $fetchedPrd[0] . "\n" ;
//            $insert = "INSERT INTO spex.productattribute_varchar (entity_id, attribute_id, value, dataState, lastModifiedDate, changedby)
//                        values($fetchedPrd[0], 96, '".$prods['name'] ."' , 0,". date('Y-m-j') .", 27)";
//            if( !mysqli_query($conn,$insert) ){
//                die(mysqli_error($conn));
//            }
//        }
    }
}