<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/18/14
 * Time: 11:59 AM
 */
include 'config/autoload/local.php';
include 'init_autoloader.php';
$products = [
    ['sku'=>'1501'	,'name'=>'Nikon D5200 24.1 MP CMOS Digital SLR Camera Body (Black)'],
//    ['sku'=>'13293','name'=>'Nikon D7100 DX-format Digital SLR w/ 18-140 & 55-300 VR lenses (black)'],
//    ['sku'=>'13303'	,'name'=>'Nikon D5300 24.2 MP CMOS Digital SLR Camera with 18-140mm f/3.5-5.6G ED VR AF-S DX NIKKOR Zoom Lens (Black)'],
//    ['sku'=>'13304-NIKON','name'=>'	D610 FX-format Digital SLR Kit w/ 28-300mm VR Lens'],
//    ['sku'=>'13306-NIKON','name'=>'	D610 FX-format Digital SLR Kit w/ 24-85mm & 70-300mm VR Lenses'],
//    ['sku'=>'13311-nikon','name'=>'	Nikon D5200 24.1 MP DX-Format CMOS Digital SLR Camera with 18-140mm VR NIKKOR Zoom Lens'],
//    ['sku'=>'1519'	,'name'=>'Nikon D5300 24.2 MP CMOS Digital SLR Camera with Built-in Wi-Fi and GPS Body Only (Black)'],
//    ['sku'=>'1520','name'=>'	Nikon D5300 24.2 MP CMOS Digital SLR Camera with Built-in Wi-Fi and GPS Body Only (Red)'],
//    ['sku'=>'1521'	,'name'=>'Nikon D5300 DX-format Digital SLR body (Grey)'],
//    ['sku'=>'1543-nikon'	,'name'=>'Nikon D750 FX-format Digital SLR Camera Body'],
//    ['sku'=>'1549'	,'name'=>'Nikon D750 FX-format Digital SLR Camera w/ 24-120mm f/4G ED VR AF-S NIKKOR Lens'],
//    ['sku'=>'20051'	,'name'=>'Nikon AF-S NIKKOR 20mm f/1.8G ED Lens'],
//    ['sku'=>'2179'	,'name'=>'Nikon 18-105MM F/3.5-5.6 AF-S DX VR ED Nikkor Lens for Nikon Digital SLR Cameras'],
//    ['sku'=>'2210-NIKON'	,'name'=>'Nikon 58mm f/1.4G AF-S NIKKOR Lens for Nikon Digital SLR Cameras'],
//    ['sku'=>'2213'	,'name'=>'NIkon AF-S NIKKOR 18-140mm f/3.5-5.6G ED VR'],
//    ['sku'=>'2510A003'	,'name'=>'Canon EF 28mm f/1.8 USM Wide Angle Lens for Canon SLR Cameras'],
//    ['sku'=>'26424-NIKON'	,'name'=>'Nikon COOLPIX A 16.2 MP Digital Camera with 28mm f/2.8 Lens (Silver)'],
//    ['sku'=>'26425'	,'name'=>'Nikon COOLPIX L620 18.1 MP CMOS Digital Camera with 14x Zoom Lens and Full 1080p HD Video (Black)'],
//    ['sku'=>'26427'	,'name'=>'Nikon COOLPIX P7800 12.2 MP Digital Camera with 7.1x Optical Zoom Lens and 3-inch Vari-Angle LCD'],
//    ['sku'=>'26431'	,'name'=>'Nikon COOLPIX S02 13.2 MP Digital Camera with 3x Zoom Lens and 1080p HD Video(Silver)'],
//    ['sku'=>'26432'	,'name'=>'Nikon COOLPIX S02 13.2 MP Digital Camera with 3x Zoom Lens and Full 1080p HD Video (White)'],
//    ['sku'=>'26433'	,'name'=>'Nikon COOLPIX S02 13.2 MP Digital Camera with 3x Zoom Lens and 1080p HD Video(Pink)'],
//    ['sku'=>'26434'	,'name'=>'Nikon COOLPIX S02 13.2 MP Digital Camera with 3x Zoom Lens and 1080p HD Video(Blue)'],
//    ['sku'=>'27602'	,'name'=>'Nikon 1 V2 Digital Camera Body (Black)'],
//    ['sku'=>'27603'	,'name'=>'Nikon 1 V2 Digital Camera Body (White)'],
//    ['sku'=>'27604'	,'name'=>'Nikon 1 V2 Digital Camera w/ 10-30mm VR (Black)'],
//    ['sku'=>'27605'	,'name'=>'Nikon 1 V2 Digital Camera w/ 10-30mm VR (White)'],
//    ['sku'=>'27606'	,'name'=>'Nikon 1 V2 Digital Camera w/ 10-30mm & 30-110mm VR Lenses (Black)'],
//    ['sku'=>'27607'	,'name'=>'Nikon 1 V2 Digital Camera w/ 10-30mm &30-110mm VR Lenses (White)'],
//    ['sku'=>'27665'	,'name'=>'Nikon 1 AW1 Water, Shock, Dust & Freezeproof Mirrorless Digital Camera with 11-27.5mm Lens (Black)'],
//    ['sku'=>'27666'	,'name'=>'Nikon 1 AW1 14.2 MP Digital Camera with AW 11-27.5mm f/3.5-5.6 1 NIKKOR Lens (Silver)'],
//    ['sku'=>'27667'	,'name'=>'Nikon 1 AW1 14.2 MP Digital Camera with AW 11-27.5mm f/3.5-5.6 and AW 10mm 1 Lenses (Black)'],
//    ['sku'=>'27669'	,'name'=>'Nikon 1 AW1 14.2 MP Digital Camera w/ 11-27.5mm f/3.5-5.6 1 NIKKOR Lens (White)'],
//    ['sku'=>'3324'	,'name'=>'1 Nikkor 18.5mm f/1.8 (White)'],
//    ['sku'=>'3327'	,'name'=>'1 NIKKOR 10-100mm f/4.0-5.6 VR (White)'],
//    ['sku'=>'3328-NIKON'	,'name'=>'1 NIKKOR 10-100mm f/4.0-5.6 VR (Silver)'],
//    ['sku'=>'8404B002'	,'name'=>'EF400mm F4 DO IS II USM'],
//    ['sku'=>'8406B001'	,'name'=>'Canon PowerShot G16 12.1 MP CMOS Digital Camera with 5x Optical Zoom and 1080p Full-HD Video'],
//    ['sku'=>'8407B001'	,'name'=>'Canon PowerShot S120 12.1 MP CMOS Digital Camera with 5x Optical Zoom and 1080p Full-HD Video'],
//    ['sku'=>'8409B001'	,'name'=>'Canon PowerShot SX510 HS 12.1 MP CMOS Digital Camera with 30x Optical Zoom and 1080p Full-HD Video (black)'],
//    ['sku'=>'8410B001'	,'name'=>'Canon PowerShot SX170 IS 16.0 MP Digital Camera with 16x Optical Zoom and 720p HD Video Black'],
//    ['sku'=>'8546B002'	,'name'=>'Canon EF-S 55-250mm f/4-5.6 IS STM'],
//    ['sku'=>'8676B001'	,'name'=>'Canon PowerShot SX170 IS 16.0 MP Digital Camera with 16x Optical Zoom and 720p HD Video Red'],
//    ['sku'=>'9128B002'	,'name'=>'EOS 7D MARK II Body'],
//    ['sku'=>'9128B016'	,'name'=>'EOS 7D EF-S Mark II 18-135mm IS STM KIT'],
//    ['sku'=>'9518B002'	,'name'=>'Canon EF 16-35mm f/4L IS USM Lens'],
//    ['sku'=>'9521B002'	,'name'=>'EF24-105mm F3.5-5.6 IS STM'],
//    ['sku'=>'9522B002'	,'name'=>'EF-S24mm F2.8 STM'],
//    ['sku'=>'9543B001'	,'name'=>'PowerShot SX60 HS'],
//    ['sku'=>'9546B001'	,'name'=>'PowerShot G7 X'],
//    ['sku'=>'9547B001'	,'name'=>'PowerShot N2 Black'],
//    ['sku'=>'9770B001'	,'name'=>'PowerShot N2 White'],
//    ['sku'=>'DSCQX10'	,'name'=>'Sony QX-10 Cyber-shot Lens Camera 1/2.3" Exmor sensor with One touch Remote For Smartphones'],
//    ['sku'=>'DSCQX100','name'=>'Sony QX-100 Cyber-shot Lens Camera 1" Exmor sensor with One Touch Remote For Smartphones'],
//    ['sku'=>'DSCQX10W','name'=>'Sony DSC-QX10/W Smartphone Attachable Lens-Style Camera 4.45-44.5mm Interchangeable Lens for Other Cameras'],
//    ['sku'=>'DSCRX10'	,'name'=>'Sony DSC-RX10/B Cybershot 20.2 MP Digital Still Camera with 3-Inch LCD Screen (Black)'],
//    ['sku'=>'ILCE3000KB'	,'name'=>'Sony ILCE-3000 A3000 20.1MP Exmor APS-C Sensor Digital SLR E-Mount Camera Kit with 18-55mm Zoom Lens  with 3-Inch LCD Screen (Black)'],
//    ['sku'=>'ILCE7B'	,'name'=>'Sony ILCE7/B 24.3 MP a7 Full-Frame Interchangeable Digital Lens Camera - Body Only'],
//    ['sku'=>'NEX5TB'	,'name'=>'Sony NEX-5T 16 MP Compact Interchangeable Lens Digital Camera with NFC and Wifi sharing, Black (Body Only)'],
//    ['sku'=>'SAL70200G2'	,'name'=>'Sony SAL70200G2 Camera Lenses'],
//    ['sku'=>'SEL1670Z'	,'name'=>'Sony SEL1670Z E-mount Vario-Tessar 16-70mm F4 ZA OSS'],
//    ['sku'=>'SEL2470Z'	,'name'=>'Sony Vario-Tessar T* FE 24-70mm f/4 ZA OSS Lens SEL2470Z'],
//    ['sku'=>'SEL50F18B'	,'name'=>'Sony SEL50F18B 50mm f/1.8 Mid-Range Lens for Sony E Mount Nex Cameras (black)'],
//    ['sku'=>'SEL55F18Z'	,'name'=>'Sony Sonnar T* FE 55mm f/1.8 ZA Lens SEL55F18Z'],
//    ['sku'=>'SELP18105G'	,'name'=>'Sony SELP18105G E PZ 18-105mm F4 G OSS'],
];

//var_dump($products);
//die();

$conn = mysqli_connect(HOST, USER, PASS, DB);
foreach( $products as $key => $prods ) {
    $select = "SELECT entity_id from spex.productattribute_option where productid = '" . $prods['sku'] . "'";
    if( !mysqli_query($conn,$select) ){
        die(mysqli_error($conn));
    } else {
        $prd = mysqli_query($conn,$select);
        $fetchedPrd = mysqli_fetch_row($prd);
        echo $fetchedPrd[0] . "\n" ;
        $insert = "INSERT INTO spex.productattribute_varchar (entity_id, attribute_id, value, dataState, lastModifiedDate, changedby)
                    values($fetchedPrd[0], 96, '".$prods['name'] ."' , 0,". date('Y-m-j') .", 27)";
//                    SELECT * from spex.product where productid = '" . $prods['sku'] . "'";
//        $update = "UPDATE productattribute_varchar set value = '" .$prods['name']. "' and dataState = 0 and entity_id = " . $fetchedPrd[0] .
//                                            " where entity_id = " . $fetchedPrd[0] . " and attribute_id = 96";
        if( !mysqli_query($conn,$insert) ){
            die(mysqli_error($conn));
        }
    }
}
//    foreach( $prods as $name => $value ) {
//        echo $name . ' ' . $value . "\n";
//    }
//if( !mysqli_query($conn,$sql) ){
//    die(mysqli_error($conn));
//}
