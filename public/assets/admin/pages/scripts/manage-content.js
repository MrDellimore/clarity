var ManageContent = function () {

    var submitHandle = function () {
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
        toastr.options.onHidden = function() { window.location = '/search'; };



        $( "#generalForm" ).submit(function( event ) {
            event.preventDefault();

            //forms from content page
            var generalForm = $('#generalForm').serializeArray();
            var imageForm = $('#imageForm').serializeArray();

            var formData
            formData = generalForm.concat(imageForm);

            //console.log(formData);
            var goodData = [];

           // var badIndex = new Array(crossSellDisplay_length]);
            for(var i = 0; i < formData.length; i++) {
                if(formData[i].name.slice(0,7) !== 'product' && formData[i].name !=='_wysihtml5_mode' && formData[i].name !=='acessoriesDisplay_length' && formData[i].name !=='crossSellDisplay_length' ){
                    //console.log(formData[i]);
                    goodData.push(formData[i]);
                }
            };


            var url = '/form/submit';
            $.ajax({
                url: url,
                type: "POST",
                data: goodData})
                .done(function( data ) {
                    //expty content div and display results
                    //$('#contentdiv').empty().append(data);

                   //toastr.success(data);

                   console.log(data);
                });
        });
    }





    return {
        //main function to initiate the module
        init: function () {
            submitHandle();
        }
    };

}();