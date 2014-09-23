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
