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
