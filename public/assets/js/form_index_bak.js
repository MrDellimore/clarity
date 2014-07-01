/**
 * Created by wsalazar on 6/18/14.
 */
$(function(){
    var image;
    var filename;
    var fullPathImageFile;
    var imgSrc;
    var getFileName = function (haystack, needle, bool) {
        //  discuss at: http://phpjs.org/functions/strstr/
        // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // bugfixed by: Onno Marsman
        // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //   example 1: strstr('Kevin van Zonneveld', 'van');
        //   returns 1: 'van Zonneveld'
        //   example 2: strstr('Kevin van Zonneveld', 'van', true);
        //   returns 2: 'Kevin '
        //   example 3: strstr('name@example.com', '@');
        //   returns 3: '@example.com'
        //   example 4: strstr('name@example.com', '@', true);
        //   returns 4: 'name'
        var pos = 0;
        haystack += '';
        pos = haystack.indexOf(needle);
        if (pos == -1) {
            return false;
        } else {
            if (bool) {
                return haystack.substr(0, pos);
            } else {
                return haystack.slice(pos);
            }
        }
    }

    $(document).on('click','a.remove_queue_file' , function (i,e){
        $(this).closest('div').remove();
    });

    $(document).on('click','a.remove' , function (i,e){

        $(this).closest('tr').remove();
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

//    var firstCnt = 0;
$( document ).ready(function() {
    $('.pick_file').bind('get_image',function(){
        fullPathImageFile = $(this).val();
        console.log(fullPathImageFile);
        var stripedFile = getFileName(fullPathImageFile, '\\').substr(1, fullPathImageFile.length );
        filename = getFileName(stripedFile,'\\').substr(1,stripedFile.length);
        console.log('haha');

//                console.log(filename);
        var filesize = $(this)[0].files[0].size / 1024;
        if (filesize / 1024 > 1){
            if (((filesize / 1024) / 1024) > 1){
                filesize = (Math.round(((filesize / 1024) / 1024) * 100) / 100);
                filesize += ' GB';
//                        $("#lblSize").html( filesize + "Gb");
            }
            else {
                filesize = (Math.round((filesize / 1024) * 100) / 100)
                filesize += ' MB';
            }
        }else{
            filesize = (Math.round(filesize * 100) / 100)
            filesize += ' KB';
        }
        $('.row > #tab_images_uploader_filelist').append("<div class='alert alert-warning added-files'> " + "<img id='img_file' src='#' />"  + filename + " (" + filesize + ")" +" <span class='status label label-info'></span>&nbsp;<a href='javascript:;' style='margin-top:-5px' class='remove_queue_file pull-right btn btn-sm red'><i class='fa fa-times'></i> remove</a></div>");
        //        console.log('haha');
        $(this).each(function(){
            $(this).on('change',function(){
                console.log($(this)[0].files);
                console.log($(this)[0].files[0]);
                if($(this)[0].files && $(this)[0].files[0]){
                    var imgReader = new FileReader();
                    imgReader.onload = function (e){
                        console.log(e);
                        console.log('this is target');
                        console.log(e.target.result);

//                        $('.row > #tab_images_uploader_filelist > div > img#img_file').text(filename + ' ' + filesize);

                        console.log('this is srcElement');
                        console.log(e.srcElement.result);
//                        imgSrc = "<img src='" + e.target.result + "' />";
                        imgSrc = e.srcElement.result;
                        $('.row > #tab_images_uploader_filelist > div > img#img_file').attr('src',imgSrc);

                    }
                    imgReader.readAsDataURL($(this)[0].files[0]);
                }


            });
        });
//        $('.row > #tab_images_uploader_filelist').hide();

//        $('.pick_file').on('click', function(){
//            $('.row > #tab_images_uploader_filelist').show();
//        });

        $('.pick_file').trigger('get_image');//,function(e){
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
        $('a.upload_image').on('click',function(e){

            e.preventDefault();
            var removeButton = "<a href='javascript:;' class='btn default btn-sm remove'><i class='fa fa-times'></i> Remove </a>";
            e.preventDefault();
            $('#tab_images_uploader_filelist > div ').each(function(i,e){
                var fileUpload = getFileName($(e).text(),'(',true);
                if(fileUpload){
                    var fields = {
                        'image' :   "<td><img src=' "+ fullPathImageFile + " '/></td>",
                        'labelField' : "<td><input type='text' name='image_title[]' value='" + fileUpload +"'/></td>",
                        'sortOrder' : "<td><input type='text' name='sort_order[]' /></td>",
                        'thumbNail' : "<td><input type='radio' name='thumbnail[]' /></td>",
                        'smallImage' : "<td><input type='radio' name='small_image[]' /></td>",
                        'baseImage' : "<td><input type='radio' name='base_image[]' /></td>",
                        'removeButton' : "<td>" + removeButton + "</td>"
                    };
                    var tableRow = "<tr>" + fields.image +  fields.labelField + fields.sortOrder + fields.thumbNail + fields.smallImage + fields.baseImage + fields.removeButton + "</tr>";
                    console.log($('.table.table-bordered.table-hover > tbody > tr').length);
                    console.log($('.table.table-bordered.table-hover > tbody').has('tr'));
                    if( $('.table.table-bordered.table-hover > tbody > tr').length == 0 ){
                        $('.table.table-bordered.table-hover > tbody > tr:first').after('<tr>tableRow</tr>')
                    } else{
                        $('.table.table-bordered.table-hover > tbody > tr:last').after(tableRow);
                    }
                }
            });
        });







        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        QuickSidebar.init() // init quick sidebar
        EcommerceProductsEdit.init();
        FormFileUpload.init();
//        $('#fileupload').fileupload({
//
//            dataType: 'json',
//            done: function (e, data) {
//                console.log('haha');
//                $.each(data.result.files, function (index, file) {
//                    $('<p/>').text(file.name).appendTo(document.body);
//                });
//            }
////                progressall: function (e, data) {
////                    var progress = parseInt(data.loaded / data.total * 100, 10);
////                    $('#progress .bar').css(
////                        'width',
////                        progress + '%'
////                    );
////                }
//        });
        var checkParentListIterator = $("ul.list-unstyled > li");
        var checkListIterator = $("ul.list-unstyled > li > ul.list-unstyled > li");

        $('li input.child').on('change', function () {
            var $parent, $touchedChildren;
            // Cache the parent
            $parent = $(this).closest('ul.list-unstyled').siblings('.parent');

            // Collect siblings of child nodes that have been checked
            // This could possibly be refactored to something more elegant
            $touchedChildren = $parent.siblings('ul.list-unstyled')
                .children('li')
                .children('label')
                .children('.child:checked');
            console.log($parent);
            console.log($touchedChildren.length);
            if ($touchedChildren.length > 0) {
                console.log('haha');
                // Set it to true
                $parent.attr('checked', 'checked');
            } else {
                console.log('hoho');
                $parent.prop('checked', false);
            }
        });



//        checkParentListIterator.each(function(ind, ev){
        var cnt = 0;
        var outer = $(this);
//            checkListIterator.each(function(i,e){
//                var inner = $(this);
//                var inputClass = $(this).find('input').attr('class');
//                var classes = inputClass.split(' ');
//                var numInputChecks = $('input.'+classes[1]).length;
//                var parentDivId = $(this).closest('ul').prev().find('div.checker').attr('id');
//                $('.' + classes[0]).bind('change',function(){
//                    var input = $(this);
//                    if($.browser.mozilla === true){
//                        if ($(this).closest('span').attr('class') == 'checked' ){
//                            $("#"+ parentDivId + " > span").addClass('checked');
//                            $("#"+ parentDivId + " > span").find('input').attr('checked','checked');
//                            $(this).attr('checked','checked');
//                            cnt++;
//                            console.log('i ' + i);
//                        }
////                        console.log($(this).closest('span').hasClass('checked'));
//                        if ($(this).closest('span').attr('class') != 'checked'){
//                            cnt--;
//                            console.log('i ' + i);
//                        }
//                    }
//                    if( $.browser.webkit === true){
//                        console.log($('input').siblings('.'+classes[1]));
//                        if (!$(this).closest('span').hasClass('checked')){
//                            $("#"+ parentDivId + " > span").addClass('checked');
//                            $("#"+ parentDivId + " > span").find('input').attr('checked','checked');
//                            $(this).attr('checked','checked');
//                            cnt++;
////                            console.log('i ' + i);
//                        }
////                        console.log($(this).closest('span').hasClass('checked'));
//                        if ($(this).closest('span').hasClass('checked')){
//                            cnt--;
////                            console.log('i ' + i);
//                        }
//                    }
//                    if(cnt == 0){
//                        $("#"+ parentDivId + " > span").removeClass('checked');
//                        $("#"+ parentDivId + " > span").find('input').attr('checked','');
//                    }
//                });
////            });
//        });



        var nameInput = $('#name'),
            count = $('#name-count'),
            keyword = $('#meta_keyword'),
            metaDescription = $('#meta_description'),
            description = $('#description'),
            keywordTA = $("#meta_keyword_ta"),
            metaDescriptionTA = $("#meta_description_ta"),
            descriptionTA = $("#description_ta"),
            characterCount = $('#char-count');
        characterCount.hide();

        nameInput.keyup(function(){
            characterCount.show();
            var charCount = this.value.replace(/{.*?}/g, '').length;
            count.text(charCount);
            if( charCount > 30){
                count.css('color','#FF0000');
            } else{
                count.css('color','#000');
            }
        });


        $('#submit').on('click',function(){
//            console.log('haha');
            keywordTA.val(keyword.children().text());
            metaDescriptionTA.val(metaDescription.children().text());
            descriptionTA.val(description.children().text());
            console.log(keywordTA.val());
            console.log(metaDescriptionTA.val());
            console.log(descriptionTA.val());
            // initiate layout and plugins

//            $('#add-form').validate();
        });

    });


});