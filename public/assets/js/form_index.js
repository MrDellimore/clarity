/**
 * Created by wsalazar on 6/18/14.
 */
$(function(){
    var image;
    var filename;

    var imgSrc;


    $(document).on('click','a.remove_queue_file' , function (i,e){
        $(this).closest('div').remove();
    });

    $(document).on('click','a.remove' , function (i,e){

        $(this).closest('tr').remove();
    });





//    var firstCnt = 0;
$( document ).ready(function() {
    $('body').bind('append_to_table', function(e, params){

//            e.preventDefault();
            var removeButton = "<a href='javascript:;' class='btn default btn-sm remove'><i class='fa fa-times'></i> Remove </a>";
//            e.preventDefault();
//            $('#tab_images_uploader_filelist > div ').each(function(i,e){
//                var fileUpload = getFileName($(e).text(),'(',true);
//                if(fileUpload){
                    var fields = {
//                        'image' :   "<td><img src=' "+ fullPathImageFile + " '/></td>",
//                        'image' :   "<td><img src='#'/></td>",
                        'labelField' : "<td><input type='text' class='file_name' name='image_title[]' value='" + params.filename +"'/></td>",
                        'fileSize' : "<td>"+  params.filesize  +"</td>",
                        'sortOrder' : "<td><input type='text' class='file_name' name='sort_order[]' /></td>",
                        'thumbNail' : "<td><input type='radio' name='thumbnail[]' /></td>",
                        'smallImage' : "<td><input type='radio' name='small_image[]' /></td>",
                        'baseImage' : "<td><input type='radio' name='base_image[]' /></td>",
                        'removeButton' : "<td>" + removeButton + "</td>"
                    };
                    var tableRow = "<tr><td></td>" + fields.labelField + fields.fileSize  + fields.sortOrder + fields.thumbNail + fields.smallImage + fields.baseImage + fields.removeButton + "</tr>";
//                    console.log($('.table.table-bordered.table-hover > tbody > tr').length);
//                    console.log($('.table.table-bordered.table-hover > tbody').has('tr'));
//                    console.log($('.table.table-bordered.table-hover > tbody > tr').length);
//                    if( $('.table.table-bordered.table-hover > tbody > tr').length == 1 ){
//                        console.log('length is 1');
//                        $('.table.table-bordered.table-hover > tbody > tr:first').after('<tr>tableRow</tr>')
//                    } else{
//                        console.log('length is more than 1');
                        $('.table.table-bordered.table-hover > tbody > tr:last').after(tableRow);
                        var pickFile = $('.pick_file');
                        pickFile.replaceWith( pickFile = pickFile.clone( true ) );
//                    }
//                }
//            });
    });

    $('.pick_file').bind('get_image',function(){
        var bind = $(this);
        $(this).on('change',function(){
            var fullPathImageFile = $(this).val();
            if($.browser.chrome === true){
                console.log($(this));
                var strippedFile = getFileName(fullPathImageFile, '\\');
                strippedFile = strippedFile.substr(1, fullPathImageFile.length );
                var filename = getFileName(strippedFile,'\\');
                filename = filename.substr(1,strippedFile.length );
                var filesize = getFileSize($(this));
                var params = {
                    'filename'  : filename,
                    'filesize'  : filesize
                };
                $('body').trigger('append_to_table',params);
            }

            if($.browser.mozilla == true) {
                console.log($(this));
//                console.log($(this).get(0).files[0]);
//                var file = $(this).get(0).files[0].size;
//                console.log(file);
//                var fullPathImageFile = $(this).val();
//                console.log(fullPathImageFile);
//            var strippedFile = getFileName(fullPathImageFile, '\\');
//            console.log(strippedFile);
//            strippedFile = strippedFile.substr(1, fullPathImageFile.length );
//            var filename = getFileName(strippedFile,'\\');
//            filename = filename.substr(1,strippedFile.length );
//            console.log(filename);
                var filename = $(this).val();
                console.log(fullPathImageFile);
                var filesize = getFileSize($(this));
                var params = {
                    'filename'  : filename,
                    'filesize'  : filesize
                };
                $('body').trigger('append_to_table',params);
            }

        });
    });




    $('.pick_file').trigger('get_image');//,function(e){









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
            var parent, checkedChildren;
            // Cache the parent
            parent = $(this).closest('ul.list-unstyled').siblings('.parent');

            // Collect siblings of child nodes that have been checked
            // This could possibly be refactored to something more elegant
            checkedChildren = parent.siblings('ul.list-unstyled')
                .children('li')
                .children('label')
                .children('.child:checked');
            console.log(parent);
            console.log(checkedChildren.length);
            if (checkedChildren.length > 0) {
                console.log('haha');
                // Set it to true
                parent.attr('checked', 'checked');
            } else {
                console.log('hoho');
                parent.prop('checked', false);
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