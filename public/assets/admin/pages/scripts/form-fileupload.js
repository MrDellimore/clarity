var FormFileUpload = function () {


    return {
        //main function to initiate the module
        init: function () {

             // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload({
                disableImageResize: false,
                autoUpload: false,
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
                maxFileSize: 5000000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
            });
            //console.log('loaded successfully');
            // Enable iframe cross-domain access via redirect option:
            $('#fileupload').fileupload(
                'option',
                'redirect',
                window.location.href.replace(
                    /\/[^\/]*$/,
                    '/cors/result.html?%s'
                )
            );

            // Upload server status check for browsers with CORS support:
            if ($.support.cors) {
                $.ajax({
                    type: 'HEAD'
                }).fail(function () {
                    $('<div class="alert alert-danger"/>')
                        .text('Upload server currently unavailable - ' +
                                new Date())
                        .appendTo('#fileupload');
                });
            }

            // Load & display existing files:
            $('#fileupload').addClass('fileupload-processing');
            $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: $('#fileupload').attr("action"),
                dataType: 'json',
                context: $('#fileupload')[0],
                type: 'POST'
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {

                $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});

            });


            //remove rows from table
            var buttonSelector= $('.imageremove');
/*
            $('#currentImages').on('click', buttonSelector, function(){
                console.log("hi");
                //$(this).closest('tr').remove();
            });
*/
            buttonSelector.on('click', function(){
                //console.log("hi");
                $(this).closest('tr').remove();
            });

            //function to check if item is uploaded
            $('#fileupload').bind('fileuploaddone', function (e, data) {
                //grab URL from reponseJSON
                var url = data.jqXHR.responseJSON.files[0].url;
                var name = data.jqXHR.responseJSON.files[0].name;

                //create string to append to table
                var newImage = '<tr><td><img class="img-responsive" src="'+url+'" alt="'+name+'"></td>';
                newImage += '<input type="hidden" name="imageGallery[][][filename]" value ="'+url+'">';
                newImage += '<td><input type="text" class="form-control" name="imageGallery[][][label]" value="'+name+'"></td>';
                newImage += '<td><input type="text" class="form-control" name="imageGallery[][][position]" value=""></td>';
                newImage += '<td><label><input type="radio" name="imageGallery[][][thumbnail]" value="1"></label></td>';
                newImage += '<td><label><input type="radio" name="imageGallery[][][small_image]" value="1"></label></td>';
                newImage += '<td><label><input type="radio" name="imageGallery[][][image]" value="1"></label></td>';
                newImage += '<td><a href="javascript:;" class="imageremove btn default btn-sm"><i class="fa fa-times"></i> Remove </a></td></tr>';




                //append to table/form
                $('#currentImages').append(newImage);

            });

        }




    };

}();


