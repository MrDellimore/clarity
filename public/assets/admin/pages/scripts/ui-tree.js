var UITree = function () {
    var cattree = function () {
//load tree
        var url = '/content/product/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);

                $('#cattree').jstree({
                    'plugins': ["checkbox", "types"],
                    'core': {
                        "themes" : {
                            "responsive": false
                        },
                        'data': data},
                    "types" : {
                        "default" : {
                            "icon" : "fa fa-folder icon-state-warning icon-lg"
                        },
                        "file" : {
                            "icon" : "fa fa-file icon-state-warning icon-lg"
                        }
                    }
                });
            });

//todo set diabled nodes based on website selected




//set categories
        $('#cattree').on('ready.jstree', function () {
            $("#categoriesForm input[name$='id]']").each(function() {
                if(!isNaN(this.value)) {
                    if(!($('#cattree').jstree('is_parent', this.value))){
                        $('#cattree').jstree('select_node', this.value);
                    }
                }
            });
        });


//handle checks
        $('#cattree').on('changed.jstree', function (e, data) {
            var wtf = data.selected.toString();
            var family = data.selected.toString();
            wtf = wtf.split(",");

            for (i = 0; i < wtf.length; i++) {
                function getparent (kid) {
                    if ($('#cattree').jstree('get_parent', kid) && $('#cattree').jstree('get_parent', kid) != "#" && family.indexOf($('#cattree').jstree('get_parent', kid)) == '-1') {

                        family += ',' + $('#cattree').jstree('get_parent', kid);
                        getparent($('#cattree').jstree('get_parent', kid));
                    }
                    else
                        return family;
                }

                getparent(wtf[i]);
            }

//get entityid
            var entityid = $("#generalForm input[name$='id]']").val();

            family = family.split(",");
            var inputString ='';

//set input boxes
            for(i=0; i<family.length; i++){
                inputString += '<input type="hidden" name = "categories['+i+'][id]" value ="'+ family[i] +'">';
                inputString += '<input type="hidden" name = "categories['+i+'][entityid]" value ="'+entityid+'">';
            }

            input = jQuery(inputString);
            $('#categoriesForm').empty().append(input);


        });
    }

    var ProductManagement = function () {
        var url = '/content/product/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);

                $('#cat_manage_tree').jstree({
                    'plugins': ["wholerow"],
                    'core': {
                        "themes" : {
                            "responsive": false
                        },
                        'data': data},
                    "types" : {
                        "default" : {
                            "icon" : "fa fa-folder icon-state-warning icon-lg"
                        },
                        "file" : {
                            "icon" : "fa fa-file icon-state-warning icon-lg"
                        }
                    }
                });
            });
        $('.category_add').attr('disabled',true);
        $('#cat_manage_tree').bind('select_node.jstree', function (e, data) {
            $("#categoryProductsForm input[name='id']").val(data.node.id);
            var table = $('#manageCats');
            $('#removeCatsForm input.ManageCategory').remove();
            $('#moveCatsForm input.ManageCategory').remove();
            $('.category_remove, .category_move').attr('disabled',true);
            $('.category_add').attr('disabled',false);
            table.dataTable();
            table.api().draw();
        });
    }

    var ProductManagementMove = function () {
        var url = '/content/product/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);

                $('#cat_manage_tree_move').jstree({
                    'plugins': ["wholerow"],
                    'core': {
                        "themes" : {
                            "responsive": false
                        },
                        'data': data},
                    "types" : {
                        "default" : {
                            "icon" : "fa fa-folder icon-state-warning icon-lg"
                        },
                        "file" : {
                            "icon" : "fa fa-file icon-state-warning icon-lg"
                        }
                    }
                });
            });
        $('.category_add').attr('disabled',true);
        $('#cat_manage_tree_move').bind('select_node.jstree', function (e, data) {
            var catId = $('input[name=newid]').val(data.node.id);
//            var hiddenNewCatID = $('<input>').attr({'type':'hidden','name':"manageCategory[newcatid]", 'id':'move-category', 'class':'ManageCategory', 'value':data.node.id});
//            console.log(data.node.id);
//            $('#move-category').remove();
//            hiddenNewCatID.appendTo('form#moveCatsForm');

//            $("#categoryProductsForm input[name='category']").val(data.node.id);
//            var table = $('#manageCats');
//            $('#removeCatsForm input.ManageCategory').remove();
//            $('.category_remove, .category_move').attr('disabled',true);
//            $('.category_add').attr('disabled',false);
//            table.dataTable();
//            table.api().draw();
        });
    }



    return {
        init: function () {

            cattree();
            ProductManagement();
            ProductManagementMove();

        }

    };

}();