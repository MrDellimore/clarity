var FormFileUpload = function () {


    return {
        //main function to initiate the module
        init: function () {

             // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload({
                disableImageResize: false,
                autoUpload: false,
                imageMinWidth: 500,
                imageMinHeight: 500,
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


            //remove rows from table and set input field as disabled
            var buttonSelector= $('.imageremove');
            buttonSelector.on('click', function(){
                var enabled = $(this).closest('tr').css('opacity');

                if(enabled == 1){
                    $(this).prev('input').val(1);
                    $(this).closest('tr').fadeTo( "slow", 0.33 );
                    $(this).text('Undo');
                }
                else{
                    $(this).prev('input').val(0);
                    $(this).closest('tr').fadeTo( "slow", 1 );
                    $(this).text('Remove');
                }

            });

            //handle radio buttons
            $('#currentImages').on('click', '.defaultRadio', function() {
                var name = this.getAttribute('name');


                $( '.defaultRadio' ).each(function() {
                    if(this.getAttribute('name') != name){
                        $(this).prop("checked",false);
                    }
                });
            });

            //function to check if item is uploaded
            $('#fileupload').bind('fileuploaddone', function (e, data) {
                //grab URL from reponseJSON
                var url = data.jqXHR.responseJSON.files[0].url;
                var name = data.jqXHR.responseJSON.files[0].name;

                var entityid = $("#generalForm input[name$='id]']").val();
                var rowcount = $('#currentImages tr').length;
                var position = rowcount;
                rowcount--;

                //create string to append to table
                var newImage = '<tr><td><img class="img-responsive" src="'+url+'" alt="'+name+'"></td>';
                newImage += '<input type="hidden" name="imageGallery['+rowcount+'][filename]" value ="'+url+'">';
                newImage += '<input type="hidden" name="imageGallery['+rowcount+'][entityid]" value ="'+entityid+'">';
                newImage += '<td><input type="text" class="form-control" name="imageGallery['+rowcount+'][label]" value="'+name+'"></td>';
                newImage += '<td><input type="text" class="form-control" name="imageGallery['+rowcount+'][position]" value="'+position+'"></td>';
                newImage += '<td><input type="radio" name="imageGallery['+rowcount+'][default]" value="1" class="defaultRadio"></td>';
                newImage += '<td><input type="hidden" name="imageGallery['+rowcount+'][disabled]" value="0">';
                newImage += '<a href="javascript:;" class="imageremove btn default btn-sm"><i class="fa fa-times"></i> Remove </a></td></tr>';

                //append to table/form
                $('#currentImages').append(newImage);

            });

        }




    };

}();


