var ManageContent = function () {

    var contentFormHandle = function () {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-full-width",
            "showDuration": "2000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        toastr.options.onHidden = function() { window.location = '/content/search'; };


//submitting content form
        $( "#generalForm" ).submit(function( event ) {
            event.preventDefault();

            //forms from content page
            var generalForm = $('#generalForm').serializeArray();
            var imageForm = $('#imageForm').serializeArray();
            var categoryForm = $('#categoriesForm').serializeArray();

            var formData;
            formData = generalForm.concat(imageForm).concat(categoryForm);

            //console.log(formData);
            var goodData = [];

           // var badIndex = new Array(crossSellDisplay_length]);
            for(var i = 0; i < formData.length; i++) {
                if(formData[i].name.slice(0,7) !== 'product' && formData[i].name !=='_wysihtml5_mode' && formData[i].name !=='acessoriesDisplay_length' && formData[i].name !=='crossSellDisplay_length' ){
                    //console.log(formData[i]);
                    goodData.push(formData[i]);
                }
            }


            var url = '/content/product/submit';
            $.ajax({
                url: url,
                type: "POST",
                data: goodData})
                .done(function( data ) {
                    //expty content div and display results
                    //$('#contentdiv').empty().append(data);

//                   toastr.success(data);
//
                   console.log(data);
                });
        });
    }

    var websiteAssignmentHandle = function () {
        $( "#websiteassignmentform" ).submit(function( event ) {
            event.preventDefault();

            var form = $('#websiteassignmentform').serializeArray();
            console.log(form);



            var url = '/content/websiteassignment/submit';
            $.ajax({
                url: url,
                type: "POST",
                data: form})
                .done(function( data ) {
                    toastr.success(data);

//                   console.log(data);
                });

             toastr.options = {
             "closeButton": true,
             "debug": false,
             "positionClass": "toast-top-full-width",
             "showDuration": "2000",
             "hideDuration": "1000",
             "timeOut": "5000",
             "extendedTimeOut": "1000",
             "showEasing": "swing",
             "hideEasing": "linear",
             "showMethod": "fadeIn",
             "hideMethod": "fadeOut"
             };
             toastr.options.onHidden = function() { window.location = '/content/webassignment'; };
        });

    };
    var attributeManagementHandle = function () {
        $( "#attributeForm" ).submit(function( event ) {
            event.preventDefault();

            var form = $('#attributeForm').serializeArray();
            console.log(form);



            var url = '/content/websiteassignment/submit';
            $.ajax({
                url: url,
                type: "POST",
                data: form})
                .done(function( data ) {
                    toastr.success(data);

//                   console.log(data);
                });

             toastr.options = {
             "closeButton": true,
             "debug": false,
             "positionClass": "toast-top-full-width",
             "showDuration": "2000",
             "hideDuration": "1000",
             "timeOut": "5000",
             "extendedTimeOut": "1000",
             "showEasing": "swing",
             "hideEasing": "linear",
             "showMethod": "fadeIn",
             "hideMethod": "fadeOut"
             };
             toastr.options.onHidden = function() { window.location = '/content/webassignment'; };
        });

    };

    var mageSkuHandle = function () {
//        $('#all_items').on('click',function(){
//            e.preventDefault();
            $( "#mageForm" ).submit(function( event ) {
//                var spinner = new Spinner({
//                    lines: 12, // The number of lines to draw
//                    length: 7, // The length of each line
//                    width: 5, // The line thickness
//                    radius: 10, // The radius of the inner circle
//                    color: '#000', // #rbg or #rrggbb
//                    speed: 1, // Rounds per second
//                    trail: 100, // Afterglow percentage
//                    shadow: true // Whether to render a shadow
//                }).spin($('.page-content-wrapper')); // Place in DOM node called "ajaxContentHolder"
                //            console.log('hoho');
                event.preventDefault();
                //            $('tr #sku_item').each(function(){
                //                if ( $(this, '#skuItem').prop() ) {
                //
                //                }
                //            });

                //console.log($('#mageForm').serializeArray());
                var form = $('#mageForm').serializeArray();
                //            console.log(form);
                //            var form = 'haha';
                var url = '/api-feeds/magento/items';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
//                       console.log(data);
                        var update = $('#kpiUpdates').dataTable();
                        var cat = $('#kpiCategories').dataTable();
                        var link = $('#kpiRelatedProducts').dataTable();
                        update.api().draw();
                        cat.api().draw();
                        link.api().draw();
                        $.post('/api-feeds/mage-update-count', function(data){
                            var count = jQuery.parseJSON(data);
                            $('div#mage-update').empty().append(count.updateCount + count.categoryCount + count.linkedCount);
//                            $('div#mage-update').empty().append(count.updateCount);
                        });
                    });

                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-full-width",
                    "showDuration": "2000",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                //             toastr.options.onHidden = function() { window.location = '/content/webassignment'; };
            });
//        });
    };

    var mageImageHandle = function () {
//        $('#all_items').on('click',function(){
//            e.preventDefault();
            $( "#mageImages" ).submit(function( event ) {
//                var spinner = new Spinner({
//                    lines: 12, // The number of lines to draw
//                    length: 7, // The length of each line
//                    width: 5, // The line thickness
//                    radius: 10, // The radius of the inner circle
//                    color: '#000', // #rbg or #rrggbb
//                    speed: 1, // Rounds per second
//                    trail: 100, // Afterglow percentage
//                    shadow: true // Whether to render a shadow
//                }).spin($('.page-content-wrapper')); // Place in DOM node called "ajaxContentHolder"
                //            console.log('hoho');
                event.preventDefault();
                //            $('tr #sku_item').each(function(){
                //                if ( $(this, '#skuItem').prop() ) {
                //
                //                }
                //            });

                //console.log($('#mageForm').serializeArray());
                var form = $('#mageImages').serializeArray();
                //            console.log(form);
                //            var form = 'haha';
                var url = '/api-feeds/magento/new-images';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
                        var table = $('#kpiImages').dataTable();
                        table.api().draw();
                        /*keeps count of new images*/
                        $.post('/api-feeds/mage-new-image-count', function(data){
                            var count = jQuery.parseJSON(data);
                            $('div#mage-image').empty().append(count.imageCount);
                        });
                    });

                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-full-width",
                    "showDuration": "2000",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                //             toastr.options.onHidden = function() { window.location = '/content/webassignment'; };
            });
//        });
    };

    var mageNewProductHandle = function () {
//        $('#all_items').on('click',function(){
//            e.preventDefault();
            $( "#mageNewProds" ).submit(function( event ) {
//                var spinner = new Spinner({
//                    lines: 12, // The number of lines to draw
//                    length: 7, // The length of each line
//                    width: 5, // The line thickness
//                    radius: 10, // The radius of the inner circle
//                    color: '#000', // #rbg or #rrggbb
//                    speed: 1, // Rounds per second
//                    trail: 100, // Afterglow percentage
//                    shadow: true // Whether to render a shadow
//                }).spin($('.page-content-wrapper')); // Place in DOM node called "ajaxContentHolder"
                //            console.log('hoho');
                event.preventDefault();
                //            $('tr #sku_item').each(function(){
                //                if ( $(this, '#skuItem').prop() ) {
                //
                //                }
                //            });

                //console.log($('#mageForm').serializeArray());
                var form = $('#mageNewProds').serializeArray();
                //            console.log(form);
                //            var form = 'haha';
                var url = '/api-feeds/magento/new-items';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
                        var table = $('#kpiNewProducts').dataTable();
                        table.api().draw();
                        /*keeps count of new images*/
                        $.post('/api-feeds/mage-new-image-count', function(data){
                            var count = jQuery.parseJSON(data);
                            if( typeof count.newProdCount == 'undefined') {
                                count.newProdCount = 0;
                            }
                            $('div#mage-new-products').empty().append(count.newProdCount);
                        });
                    });

                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-full-width",
                    "showDuration": "2000",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                //             toastr.options.onHidden = function() { window.location = '/content/webassignment'; };
            });
//        });
    };






//submitting website form






    return {
        //main function to initiate the module
        init: function () {
            contentFormHandle();
            websiteAssignmentHandle();
            attributeManagementHandle();
            mageSkuHandle();
            mageImageHandle();
            mageNewProductHandle();
        }
    };

}();