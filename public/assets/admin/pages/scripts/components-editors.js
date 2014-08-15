var ComponentsEditors = function () {

    var description = function () {
        if (!jQuery().wysihtml5) {
            return;
        }
        var currentValue = $('textarea#descriptionwys').text();
        if ($('#descriptionwys').size() > 0) {
            $('#descriptionwys').wysihtml5({
               // "placeholderText": currentValue,
                "stylesheets": ["../../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }

        $('textarea#descriptionwys').text(currentValue);

    }

    var shortDescription = function () {
        if (!jQuery().wysihtml5) {
            return;
        }
        var currentValue = $('textarea#shortdescriptionwys').text();
        if ($('#shortdescriptionwys').size() > 0) {
            $('#shortdescriptionwys').wysihtml5({
                "placeholderText": currentValue,
                "stylesheets": ["../../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]

            });
        }//
    }

    var inBox = function () {
        if (!jQuery().wysihtml5) {
            return;
        }
        var currentValue = $('textarea#inboxwys').text();
        if ($('#inboxwys').size() > 0) {
            $('#inboxwys').wysihtml5({
                "placeholderText": currentValue,
                "stylesheets": ["../../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    }

    var includesfree = function () {
        if (!jQuery().wysihtml5) {
            return;
        }
        var currentValue = $('textarea#includesfreewys').text();
        if ($('#includesfreewys').size() > 0) {
            $('#includesfreewys').wysihtml5({
                "placeholderText": currentValue,
                "stylesheets": ["../../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    }




    var metadescription = function () {
        if (!jQuery().wysihtml5) {
            return;
        }
        var currentValue = $('textarea#metadescriptionwys').text();
        if ($('#metadescriptionwys').size() > 0) {
            $('#metadescriptionwys').wysihtml5({
                "placeholderText": currentValue,
                "stylesheets": ["../../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }



    }



    return {
        //main function to initiate the module
        init: function () {
            description();
            inBox();
            includesfree();
            shortDescription();
            metadescription();
        }
    };

}();