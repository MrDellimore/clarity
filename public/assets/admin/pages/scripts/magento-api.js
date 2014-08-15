/**
 * Created by wsalazar on 8/15/14.
 */
//jQuery(document).ready(function() {
var magentoItems = $('.magento-updates');
var magentoImages = $('.magento-images');
magentoItems.hide();
magentoImages.hide();
var MagentoApi = function () {

//    return {
        //main function to initiate the module
//        init: function () {

            $('.show-updates').on('click',function(e){
                e.preventDefault();
                magentoItems.show();
                magentoImages.hide();
            });
            $('.show-images').on('click',function(e){
                e.preventDefault();
                magentoItems.hide();
                magentoImages.show();
            });


//        }

//    };

};

//});
