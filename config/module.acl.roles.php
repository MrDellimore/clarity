<?php

$homePage = array(
    'home',
    'update-count',
    'new-product-count',
    'image-count',
    'mfcload',
    'brand',
    'listUsers',
    'form',
    'categoryload',
);

$manage_search = array(
    'search',
    'quicksearch',
);
$manage_attribute = array(
    'manageattributes',
    'manageattributesquicksearch',
    'manageoptionsquicksearch'
);
$manage_website = array(
    'webassignment',

);
$api_magento = array(
    'apis',
    'apis-update-items',
    'api-magento-categories',
    'api-magento-related',
    'api-images',
    'api-new-products'
);
$history_sku = array(
    'logging'

);
$history_soap = array(
    'mage-soap-logging'
);

return array(
    'it' => array_merge(
        $homePage,
        $manage_attribute,
        $manage_search,
        $manage_website,
        $api_magento,
        $history_sku,
        $history_soap
    ),
    'guest' => array(
        'auth',
        'login'
    )
);

/*
return array(
    'it'=> array(
        //'*'
        'home',

        'update-count',

        'search',
        'manageattributes',
        'webassignment',
        'apis',
    ),
    'content'=> array(
    ),
);
*/