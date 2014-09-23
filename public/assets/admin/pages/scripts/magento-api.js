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

$('tr #sku_item').on('change', '#skuItem' ,function(){
    console.log($(this).val());
    $('<input>').attr({
        type: 'hidden',
        name: $(this).attr('name'),
        value: $(this).val()
    }).appendTo('form#mageForm');
//    $('#mageForm').html('<input type="hidden" name="mageSku['+ $(this).val()+ "]");
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
