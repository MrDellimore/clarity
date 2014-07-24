var ComponentsDropdowns = function () {

    var mfcDropDown = function () {

        $('#manufacturerDropdown').select2({
            placeholder: "Select an option",
            allowClear: true
        });


        //ajax route
        var url = "/form/manufacturerload";
        $.ajax({
            url: url,
            dataType: "json"})
        .done(function( data ) {
            //console.log(data);
            var $el = $("#manufacturerDropdown");
            //Save old option to set in list
            var mfcset = $("#manufacturerDropdown option:selected").text();

            //$el.empty(); // remove old options

            $.each(data, function(key, value) {
               // console.log(value.mfc);
                //dont add if set
                if (value.mfc != mfcset){

                    $el.append($("<option></option>").attr("value", value.mfc).text(value.mfc));
                }
            });

        });


    }





    return {
        //main function to initiate the module
        init: function () {
            mfcDropDown();
        }
    };

}();