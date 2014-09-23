/**
 * Created by wsalazar on 8/15/14.
 */
var magentoItems = $('.magento-updates');
var magentoImages = $('.magento-images');
var magentoNewItems = $('.magento-new-items');
var kpiUpdates = $('#kpiUpdates');
magentoItems.hide();
magentoImages.hide();
magentoNewItems.hide();
kpiUpdates.hide();
var skuItem = $('#sku_item');
var groupSku = $('#skuItems');

groupSku.on('change',function(){
    if( $(this).prop("checked") ) {
        $('tr #sku_item #skuItem').each(function(){
            $(this).prop('checked','checked');
            $('<input>').attr({
                type: 'hidden',
                name: $(this).attr('name'),
                value: $(this).val()
            }).appendTo('form#mageForm');
        });
    } else {
        $('tr #sku_item #skuItem').each(function(){
            $(this).prop('checked', '');
            $('form#mageForm').empty();
        });
    }
});
$('tr #sku_item').on('change', '#skuItem' ,function(){
    if( $(this).prop('checked') ) {
        $('<input>').attr({
            type: 'hidden',
            name: $(this).attr('name'),
            value: $(this).val()
        }).appendTo('form#mageForm');
    } else {
//        $(this)
        $('<input>').attr({type:'',name:'',value:''}).appendTo('form#mageForm');
    }
});
//$('tr #sku_item').each(function(){
//    if ( $(this, '#skuItem').prop() ) {
//        console.log('haha');
//    }
//});

//skuItem.css('cursor','pointer');
//skuItem.on('change',function(){
//skuItem.attr("checked", !skuItem.attr("checked"));
//});
        $('.show-updates').on('click',function(e){
            e.preventDefault();
            magentoItems.show();
            kpiUpdates.show();
            magentoImages.hide();
            magentoNewItems.hide();
        });
        $('.show-images').on('click',function(e){
            e.preventDefault();
            magentoItems.hide();
            kpiUpdates.hide();
            magentoImages.show();
            magentoNewItems.hide();
        });
        $('.new-items').on('click',function(e){
            e.preventDefault();
            magentoItems.hide();
            kpiUpdates.hide();
            magentoImages.hide();
            magentoNewItems.show();
        });
