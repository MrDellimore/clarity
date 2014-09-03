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
        /*
        var cats = $('.selectedcat');

        for(i=0; i<cats.length; i++){
            if ($(cats[i]).text().trim() == 'Focus' || $(cats[i]).text().trim() == 'aSavings')
            console.log($(cats[i]).text().trim());
        }
        */
        //case1 focus not in array
            //disable focus
        //case2 asavings not in array
            //disable asavings



//set categories
        $('#cattree').on('ready.jstree', function () {
            $("input[name$='id]']").each(function() {
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
                getparent(wtf[i]);

                function getparent (kid) {
                    if ($('#cattree').jstree('get_parent', kid) && $('#cattree').jstree('get_parent', kid) != "#" && family.indexOf($('#cattree').jstree('get_parent', kid)) == '-1') {

                        family += ',' + $('#cattree').jstree('get_parent', kid);
                        getparent($('#cattree').jstree('get_parent', kid));
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
                inputString += '<input type="hidden" name = "categories['+i+'][id]" value ="'+ family[i] +'">';
                inputString += '<input type="hidden" name = "categories['+i+'][entityid]" value ="">';
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
            cattree();
            //contextualMenuSample();
            //ajaxTreeSample();

        }

    };

}();