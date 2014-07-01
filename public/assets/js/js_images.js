//          console.log($(this).val());
var bind = $(this);
//console.log('haha');
//        $('.row > #tab_images_uploader_filelist').append("<div class='alert alert-warning added-files'> " + "<img id='img_file' src='#' />"  + filename + " (" + filesize + ")" +" <span class='status label label-info'></span>&nbsp;<a href='javascript:;' style='margin-top:-5px' class='remove_queue_file pull-right btn btn-sm red'><i class='fa fa-times'></i> remove</a></div>");
//        console.log('haha');

//            console.log(each.val());
//            console.log(that.attr('name'));
//            var fullPathImageFile = that.val();
//            console.log(fullPathImageFile);
bind.on('change',function(){
    var change = $(this);
    var fullPath = change.val();
    console.log(fullPath);
    change.each(function(){

        var each = $(this);
        var fullPathImageFile = each.val();
//                console.log('haha');
        console.log(fullPathImageFile);
//                var stripedFile = getFileName(fullPathImageFile, '\\').substr(1, fullPathImageFile.length );
//                filename = getFileName(stripedFile,'\\').substr(1,stripedFile.length);
//                var filesize = $(this)[0].files[0].size / 1024;
//                if (filesize / 1024 > 1){
//                    if (((filesize / 1024) / 1024) > 1){
//                        filesize = (Math.round(((filesize / 1024) / 1024) * 100) / 100);
//                        filesize += ' GB';
//                        $("#lblSize").html( filesize + "Gb");
//                    }
//                    else {
//                        filesize = (Math.round((filesize / 1024) * 100) / 100)
//                        filesize += ' MB';
//                    }
//                }else{
//                    filesize = (Math.round(filesize * 100) / 100)
//                    filesize += ' KB';
//                }
//                console.log($(this)[0].files);
//                console.log($(this)[0].files[0]);
//                if($(this)[0].files && $(this)[0].files[0]){
//                    var imgReader = new FileReader();
//                    imgReader.onload = function (e){
//                        console.log(e);
//                        console.log('this is target');
//                        console.log(e.target.result);
//
//                        $('.row > #tab_images_uploader_filelist > div > img#img_file').text(filename + ' ' + filesize);
//
//                        console.log('this is srcElement');
//                        console.log(e.srcElement.result);
//                        imgSrc = "<img src='" + e.target.result + "' />";
//                        imgSrc = e.srcElement.result;
//                        $('.row > #tab_images_uploader_filelist > div > img#img_file').attr('src',imgSrc);

//                    }
//                    imgReader.readAsDataURL($(this)[0].files[0]);
//                }


    });




    //        var pick = $(this);
//            var attributes = input.attributes;
//        alert(attributes[0].name);
//        alert(attributes[0].value);
//            console.log(attributes);
//            $(attributes).each(function()
//            {
//                var atts = $(this);
//                console.log( atts );// Loop over each attribute
//                console.log('test');
//
//            });

//            console.log('haha');
//            console.log(input.file);
//        console.log(this.file[0]);
//            if( input.file && input.file[0] ){
//                console.log('haha');
//                var imageFile = FileReader();
//                imageFile.onload = function(e){
//                    image = e.target.result;
//                    console.log(image);
//                }
//                imageFile.readAsDataURL(input.file[0]);
//            }

});

//        $('.row > #tab_images_uploader_filelist').hide();

//        $('.pick_file').on('click', function(){
//            $('.row > #tab_images_uploader_filelist').show();
//        });



//            $('#pick_file').trigger('get_image');
//            e.preventDefault();
//            console.log('haha');
//            console.log($(this).val());
//            console.log($(this).next().file);
//            if( $(this).next().file && $(this).next().file[0] ){
//                console.log('haha');
//                var imageFile = FileReader();
//                imageFile.onload = function(e){
//                    image = e.target.result;
//                    console.log(image);
//                }
//                imageFile.readAsDataURL($(this).next().file[0]);
//            }

//        });