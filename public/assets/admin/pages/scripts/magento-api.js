/**
 * Created by wsalazar on 8/15/14.
 */

//Set up variables from DOM classes and ids.
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

//Hide datatables upon page rendering.
magentoItems.hide();
magentoImages.hide();
magentoNewItems.hide();
soapNewProducts.hide();
soapUpdates.hide();
soapImages.hide();

//Make an ajax call to obtain a count of how many updates there are for KPI
$.post('/api-feeds/mage-update-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-update').append(count.updateCount);
});

//Make an ajax call to obtain a count of how many new products/skus there are for KPI
$.post('/api-feeds/mage-new-product-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-new-products').append(count.newProdCount);
});

//Make an ajax call to obtain a count of how many new images there are for KPI
$.post('/api-feeds/mage-new-image-count', function(data){
    var count = jQuery.parseJSON(data);
    $('div#mage-image').append(count.imageCount);
});

//Totally forgot why I did this. This is why commenting is important when code is done the first time.
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

//Totally forgot why I did this. This is why commenting is important when code is done the first time.
$('tr #sku_item').on('change', '#skuItem' ,function(){
    var hidden = $('<input>').attr({type: 'hidden',name: $(this).attr('name'),value: $(this).val()});
    if( $(this).prop('checked') ) {
        hidden.appendTo('form#mageForm');
    }
    if( !$(this).is(':checked') ) {
        $('form#mageForm input[value='+ $(this).val() +']').remove();
    }
});

//When user clicks on link everything refreshes.
//A new ajax call is made.
//DataTable renders again.
//Depending on how many checkboxes have been selected it also register that and displays it in button.
$('.show-updates').on('click',function(e){
    e.preventDefault();
    soapUpdates.show();     //displays datatable
    magentoItems.show();    //displays button
    $.post('/api-feeds/mage-update-count', function(data){
        var count = jQuery.parseJSON(data);
        $('div#mage-update').empty().append(count.updateCount);
    });
    var updateItems = $('#kpiUpdates').DataTable();
    updateItems.draw();             //  redraws datatable
    var updateCategories = $('#kpiCategories').DataTable();
    updateCategories.draw();        //  redraws datatable
    var updateLinked = $('#kpiRelatedProducts').DataTable();
    updateLinked.draw();            //  redraws datatable
    TableManaged.datatableUpdateChecked();      //Keeps track of how many checkboxes have been selected
    magentoImages.hide();   //hides button to new images.
    soapImages.hide();      //hides datatable to new images.
    soapNewProducts.hide(); //hides datatable to new items.
    magentoNewItems.hide(); //hides button to new items.
});

//When user clicks on link everything refreshes.
//A new ajax call is made.
//DataTable renders again.
//Depending on how many checkboxes have been selected it also register that and displays it in button.
$('.show-images').on('click',function(e){
    e.preventDefault();
    magentoImages.show();   //displays button to new images.
    soapImages.show();      //displays datatable to new images.
    TableManaged.datatableImageChecked();   //Keeps track of how many checkboxes have been selected
    var newImages = $('#kpiImages').DataTable();
    newImages.draw();       //  redraws datatable
    $.post('/api-feeds/mage-new-image-count', function(data){
        var count = jQuery.parseJSON(data);
        $('div#mage-image').empty().append(count.imageCount);
    });
    soapUpdates.hide();     //hides datatable to updated items.
    magentoItems.hide();    //hides button to updated items.
    soapNewProducts.hide(); //hides datatable to new items.
    magentoNewItems.hide(); //hides button to new items.
});

//When user clicks on link everything refreshes.
//A new ajax call is made.
//DataTable renders again.
//Depending on how many checkboxes have been selected it also register that and displays it in button.
$('.new-items').on('click',function(e){
    e.preventDefault();
    magentoNewItems.show(); //displays button to new items.
    soapNewProducts.show(); //displays datatable to new items.
    TableManaged.datatableNewProductChecked();      //Keeps track of how many checkboxes have been selected
    var newProducts = $('#kpiNewProducts').dataTable();
    newProducts.api().draw();           //  redraws datatable
    $.post('/api-feeds/mage-new-product-count', function(data){
        var count = jQuery.parseJSON(data);
        $('div#mage-new-products').empty().append(count.newProdCount);
    });
    magentoItems.hide();    //hides button to updated items.
    magentoImages.hide();   //hides button to new images.
    soapImages.hide();      //hides datatable to new images.
    soapUpdates.hide();     //hides datatable to updated items.
});
