/**
 * Created by wsalazar on 8/15/14.
 */
var magentoItems = $('.magento-updates');       //button for updated items.
var magentoImages = $('.magento-images');       //button for new images.
var magentoNewItems = $('.magento-new-items');  //button for new items.

var soapUpdates = $('#soapUpdates');                //div for datatable.
var soapImages = $('#soapImages');                  //div for datatable.
var soapNewProducts = $('#soapNewProducts');        //div for datatable.

var kpiUpdates = $('#kpiUpdates');
var kpiImages = $('#kpiImages');
var skuItem = $('#sku_item');
var groupSku = $('#skuItems');
magentoItems.hide();
magentoImages.hide();
magentoNewItems.hide();
soapNewProducts.hide();
soapUpdates.hide();
soapImages.hide();
$.post('/api-feeds/mage-update-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-update').append(count.updateCount);
});
$.post('/api-feeds/mage-new-product-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-new-products').append(count.newProdCount);
});
$.post('/api-feeds/mage-new-image-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-image').append(count.imageCount);
});

groupSku.on('change',function(){
    var item = $('tr #sku_item #skuItem');

    if( $(this).prop("checked") ) {
        item.each(function(){
            var input = $('<input>').attr({type: 'hidden',name: $(this).attr('name'),value: $(this).val()});
            $(this).prop('checked','checked');
            input.appendTo('form#mageForm');
        });
    } else {
        item.each(function(){
            var input = $('<input>').attr({type: 'hidden',name: item.attr('name'),value: item.val()});
            $(this).prop('checked', '');
            $('form#mageForm input').remove();
        });
    }
});
$('tr #sku_item').on('change', '#skuItem' ,function(){
    var hidden = $('<input>').attr({type: 'hidden',name: $(this).attr('name'),value: $(this).val()});
    if( $(this).prop('checked') ) {
        hidden.appendTo('form#mageForm');
    }
    if( !$(this).is(':checked') ) {
        $('form#mageForm input[value='+ $(this).val() +']').remove();
    }
});

        $('.show-updates').on('click',function(e){
            e.preventDefault();
            soapUpdates.show();     //displays datatable
            magentoItems.show();    //displays button
            var updateItems = $('#kpiUpdates').DataTable();
            updateItems.draw();
            var updateCategories = $('#kpiCategories').DataTable();
            updateCategories.draw();
            var updateLinked = $('#kpiRelatedProducts').DataTable();
            updateLinked.draw();
            TableManaged.datatableUpdateChecked();
            magentoImages.hide();   //hides button to new images.
            soapImages.hide();      //hides datatable to new images.
            soapNewProducts.hide(); //hides datatable to new items.
            magentoNewItems.hide(); //hides button to new items.
        });
        $('.show-images').on('click',function(e){
            e.preventDefault();
            magentoImages.show();   //displays button to new images.
            soapImages.show();      //displays datatable to new images.
            TableManaged.datatableImageChecked();
            var newImages = $('#kpiImages').DataTable();
            newImages.draw();
            soapUpdates.hide();     //hides datatable to updated items.
            magentoItems.hide();    //hides button to updated items.
            soapNewProducts.hide(); //hides datatable to new items.
            magentoNewItems.hide(); //hides button to new items.
        });
        $('.new-items').on('click',function(e){
            e.preventDefault();
            magentoNewItems.show(); //displays button to new items.
            soapNewProducts.show(); //displays datatable to new items.
            TableManaged.datatableNewProductChecked();
            var newProducts = $('#kpiNewProducts').dataTable();
            newProducts.api().draw();
            magentoItems.hide();    //hides button to updated items.
            magentoImages.hide();   //hides button to new images.
            soapImages.hide();      //hides datatable to new images.
            soapUpdates.hide();     //hides datatable to updated items.
        });
