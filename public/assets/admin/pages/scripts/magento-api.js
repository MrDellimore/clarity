/**
 * Created by wsalazar on 8/15/14.
 */
var magentoItems = $('.magento-updates');
var magentoImages = $('.magento-images');
magentoItems.hide();
magentoImages.hide();
//var MagentoApi = function () {

//    var showUpdates = function (){
//        console.log('haha');
        $('.show-updates').on('click',function(e){
            e.preventDefault();
            console.log('haha');
            magentoItems.show();
            magentoImages.hide();
        });
//    };

//    var showImages = function (){
//        console.log('haha');
        $('.show-images').on('click',function(e){
            e.preventDefault();
            magentoItems.hide();
            magentoImages.show();
        });
//    };
//    return {
//        init: function () {
//            showUpdates();
//            showImages();
//        }
//    };
//}();