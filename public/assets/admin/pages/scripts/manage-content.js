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
            var accessoriesForm = $('#accessoriesForm').serializeArray();
            var attributesForm = $('#attributesForm').serializeArray();

            var formData;
            formData = generalForm.concat(imageForm).concat(categoryForm).concat(accessoriesForm).concat(attributesForm);


            var goodData = [];

            //set default checkbox values if not in form
            var contentInForm = true;
            var originalInForm = true;

            for(var i = 0; i < formData.length; i++) {
                if(formData[i].name == 'contentReviewed[option]'){
                    contentInForm = false;
                }
                if(formData[i].name == 'originalContent[option]'){
                    originalInForm = false;
                }
            }

            if(originalInForm){
                formData.push({name: "originalContent[option]",value: "0"});
            }
            if(contentInForm){
                formData.push({name: "contentReviewed[option]",value: "0"});
            }




            for(var i = 0; i < formData.length; i++) {
                //set checkbox value from "on" to 1
                if((formData[i].name == 'originalContent[option]' || formData[i].name == 'contentReviewed[option]') && formData[i].value =="on"){
                    formData[i].value = '1';
                }

                //clean form
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

                    toastr.success(data);

                    //$('#contentdiv').empty().append(data);

                    //console.log(data);
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

//                  console.log(data);
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
            $( "#mageForm" ).submit(function( event ) {
                event.preventDefault();
                var form = $('#mageForm').serializeArray();
                var url = '/api-feeds/magento/items';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
                        $('#skuItems').prop('checked',false);
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
            });
    };

    var mageImageHandle = function () {
            $( "#mageImages" ).submit(function( event ) {
                event.preventDefault();
                var form = $('#mageImages').serializeArray();
                var url = '/api-feeds/magento/new-images';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
                        var table = $('#kpiImages').dataTable();
                        table.api().draw();
                        $('#skuImages').prop('checked',false);
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
            });
    };

    var mageNewProductHandle = function () {
            $( "#mageNewProds" ).submit(function( event ) {
                event.preventDefault();
                var form = $('#mageNewProds').serializeArray();
                var url = '/api-feeds/magento/new-items';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form})
                    .done(function( data ) {
                        toastr.success(data);
                        $('#skuNewProducts').prop('checked',false);
                        var table = $('#kpiNewProducts').dataTable();
                        table.api().draw();
                        /*keeps count of new images*/
                        $.post('/api-feeds/mage-new-product-count', function(data){
                            var count = jQuery.parseJSON(data);
                            if( typeof count.newProdCount == 'undefined') {
                                count.newProdCount = 0;
                            }
//                            console.log(count.newProdCount);
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
            });
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