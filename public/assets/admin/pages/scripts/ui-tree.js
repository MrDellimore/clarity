var UITree = function () {


/*
    var focustree = function () {
        $('#tree_2').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'data': testSite},

    }

    var asavetree = function () {
        $('#asave').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'data': oldsitedata},
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "file" : {
                    "icon" : "fa fa-file icon-state-warning icon-lg"
                }
            }
        });
   =


    */
    var focusnewtree = function () {
//load tree
        var url = '/content/product/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                //oldsitedata = data;
                $('#newfocus').jstree({
                    'plugins': ["wholerow", "checkbox", "types"],
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

//set categories
        $('#newfocus').on('ready.jstree', function () {
            $('#categoriesForm input[type=hidden]').each(function() {
                if(!isNaN(this.value)) {
                    if(!($('#newfocus').jstree('is_parent', this.value))){
                        $('#newfocus').jstree('select_node', this.value);
                    }

                }
            });
        });

//handle checks

        $('#newfocus').on('changed.jstree', function (e, data) {
            var wtf = data.selected.toString();
            var family = data.selected.toString();
            wtf = wtf.split(",");

            for (i = 0; i < wtf.length; i++) {
                getparent(wtf[i]);

                function getparent (kid) {
                    if ($('#newfocus').jstree('get_parent', kid) && $('#newfocus').jstree('get_parent', kid) != "#" && family.indexOf($('#newfocus').jstree('get_parent', kid)) == '-1') {

                        family += ',' + $('#newfocus').jstree('get_parent', kid);
                        getparent($('#newfocus').jstree('get_parent', kid));
                    }
                    else
                        return family;
                }
            }

//set input boxes
            family = family.split(",");
            var inputString ='';

            for(i=0; i<family.length; i++){
                inputString += '<input type="hidden" name = "categories[]" value ="'+family[i]+'">';
            }

            input = jQuery(inputString);
            $('#categoriesForm').empty().append(input);
        });
    }



    var asavenewtree = function () {
//load tree
        var url = '/content/product/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                //oldsitedata = data;
                $('#newasave').jstree({
                    'plugins': ["wholerow", "checkbox", "types"],
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
//set diabled nodes based on website selected

//set categories
        $('#newasave').on('ready.jstree', function () {
            $("input[name$='categoryid]']").each(function() {
                if(!isNaN(this.value)) {
                    if(!($('#newasave').jstree('is_parent', this.value))){
                        $('#newasave').jstree('select_node', this.value);

                    }

                }
            });

        });

//handle checks

        $('#newasave').on('changed.jstree', function (e, data) {
            var wtf = data.selected.toString();
            var family = data.selected.toString();
            wtf = wtf.split(",");

            for (i = 0; i < wtf.length; i++) {
                getparent(wtf[i]);

                function getparent (kid) {
                    if ($('#newasave').jstree('get_parent', kid) && $('#newasave').jstree('get_parent', kid) != "#" && family.indexOf($('#newasave').jstree('get_parent', kid)) == '-1') {

                        family += ',' + $('#newasave').jstree('get_parent', kid);
                        getparent($('#newasave').jstree('get_parent', kid));
                    }
                    else
                        return family;
                }
            }
//get entityid
          //  var entityid = $("input[name*='man']");
           // console.log(entityid);
//set input boxes
            family = family.split(",");
            var inputString ='';

            for(i=0; i<family.length; i++){
                inputString += '<input type="hidden" name = "categories['+i+'][categoryid]" value ="'+ family[i] +'">';
                inputString += '<input type="hidden" name = "categories['+i+'][id]" value ="">';
            }

            input = jQuery(inputString);
            $('#categoriesForm').empty().append(input);


        });

    }








    return {

        //main function to initiate the module
        init: function () {

            //handleSample1();
            //focustree();
           // asavetree();
            //focusnewtree();
            asavenewtree();
            //contextualMenuSample();
            //ajaxTreeSample();

        }

    };

}();