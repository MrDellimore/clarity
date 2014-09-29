/**
 * Created by wsalazar on 8/15/14.
 */
var magentoItems = $('.magento-updates');
var magentoImages = $('.magento-images');
var magentoNewItems = $('.magento-new-items');
var soapUpdates = $('#soapUpdates');
var kpiUpdates = $('#kpiUpdates');
var soapImages = $('#soapImages');
var kpiImages = $('#kpiImages');
magentoItems.hide();
magentoImages.hide();
magentoNewItems.hide();
//kpiUpdates.hide();
soapUpdates.hide();
//kpiImages.hide();
soapImages.hide();
var skuItem = $('#sku_item');
var groupSku = $('#skuItems');

$.post('/api-feeds/mage-update-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-update').append(count.updateCount + count.categoryCount);
});


$.post('/api-feeds/mage-new-image-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-image').append(count.imageCount);
});

groupSku.on('change',function(){
    var item = $('tr #sku_item #skuItem');

    if( $(this).prop("checked") ) {
        item.each(function(){
            var input = $('<input>').attr({
                type: 'hidden',
                name: $(this).attr('name'),
                value: $(this).val()
            });
            $(this).prop('checked','checked');
            input.appendTo('form#mageForm');
        });
    } else {
        item.each(function(){
            var input = $('<input>').attr({
                type: 'hidden',
                name: item.attr('name'),
                value: item.val()
            });
            $(this).prop('checked', '');
            $('form#mageForm input').remove();
        });
    }
});
$('tr #sku_item').on('change', '#skuItem' ,function(){
    var hidden = $('<input>').attr({
        type: 'hidden',
        name: $(this).attr('name'),
        value: $(this).val()
    });
    if( $(this).prop('checked') ) {
        hidden.appendTo('form#mageForm');
    }
    if( !$(this).is(':checked') ) {
        $('form#mageForm input[value='+ $(this).val() +']').remove();
    }
});

        $('.show-updates').on('click',function(e){
            e.preventDefault();
            magentoItems.show();
//            kpiUpdates.show();
//            kpiImages.hide();
            soapImages.hide();
            soapUpdates.show();
            magentoImages.hide();
            magentoNewItems.hide();
        });
        $('.show-images').on('click',function(e){
            e.preventDefault();
            magentoItems.hide();
//            kpiUpdates.hide();
//            kpiImages.show();
            soapImages.show();
            soapUpdates.hide();
            magentoImages.show();
            magentoNewItems.hide();
        });
        $('.new-items').on('click',function(e){
            e.preventDefault();
            magentoItems.hide();
            kpiUpdates.hide();
            kpiImages.hide();
            magentoImages.hide();
            magentoNewItems.show();
        });
