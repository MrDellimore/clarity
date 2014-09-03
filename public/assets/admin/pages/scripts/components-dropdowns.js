var ComponentsDropdowns = function () {

    var brandDropDown = function (){
        $('#brandDropdown').select2({
            placeholder: "Select an option",
            allowClear: true
        });

        var url = '/content/product/brandload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                var $el = $("#brandDropdown");
                //Save old option to set in list
                var brandSet = $("#manufacturerDropdown option:selected").text();

                $.each(data, function(key, value) {
                    //dont add if set
                    if (value.brand != brandSet){
                        $el.append($("<option></option>").attr("value", value.value).text(value.brand));
                    }
                });

            });

    }
    var skuHistoryUserDropDown = function (){
        $('#findUser').select2({
            placeholder: "Select an option",
            allowClear: true
        });
        var url = '/sku-history/user';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                var $el = $("#findUser");
                //Save old option to set in list
                $.each(data, function(key, value) {
                    var user = value.firstName + ' ' + value.lastName;
                    var userId = value.userId;
                        $el.append($("<option></option>").attr("value",userId).text(user));
                });
            });

    };

    var mfcDropDown = function () {

        $('#manufacturerDropdown').select2({
            placeholder: "Select an option",
            allowClear: true
        });

        //ajax route
        var url = "/content/product/manufacturerload";
        $.ajax({
            url: url,
            dataType: "json"})
        .done(function( data ) {
            //console.log(data);
            var $el = $("#manufacturerDropdown");
            //Save old option to set in list
            var mfcset = $("#manufacturerDropdown option:selected").text();

            $.each(data, function(key, value) {
                //dont add if set
                if (value.mfc != mfcset){
                    $el.append($("<option></option>").attr("value", value.value).text(value.mfc));
                }
            });

        });


    }

    var mfcDropDownMulti = function () {

        $('#manufacturerDropdownmulti').select2({
            placeholder: "Select a manufacturer",
            allowClear: true
        });

        //ajax route
        var url = "/content/product/manufacturerload";
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                var $el = $("#manufacturerDropdownmulti");
                //Save old option to set in list
                //var mfcset = $("#manufacturerDropdownmulti option:selected").text();

                $.each(data, function(key, value) {
                    //dont add if set
                    if (true){
                        $el.append($("<option></option>").attr("value", value.value).text(value.mfc));
                    }
                });

            });


    }

    var contentDrop = function () {

        $('#manufacturerDropdownmulti').select2({
            placeholder: "Select a manufacturer",
            allowClear: true
        });

        //ajax route
        var url = "/content/product/contentload";
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                var $el = $("#manufacturerDropdownmulti");

                $.each(data, function(key, value) {
                    //dont add if set
                    if (true){
                        $el.append($("<option></option>").attr("value", value.value).text(value.mfc));
                    }
                });

            });


    }





    return {
        //main function to initiate the module
        init: function () {
            mfcDropDown();
            brandDropDown();
            skuHistoryUserDropDown();
            mfcDropDownMulti();
            contentDrop();

        }
    };

}();