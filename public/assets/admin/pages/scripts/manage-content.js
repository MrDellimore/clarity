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
                        var table = $('#kpiUpdates').dataTable();
//                        table.api().draw();
//                        $.post('/api-feeds/mage-update-count', function(data){
//                            var count = jQuery.parseJSON(data);
//                            $('div#mage-update').append(count.updateCount);
//                        });

//                        toastr.success(data);
                       console.log(data);
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
        }
    };

}();