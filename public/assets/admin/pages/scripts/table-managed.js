var TableManaged = function () {

    var initTable1 = function () {

        var table = $('#sample_1');

        // begin first table
        table.dataTable({


            "processing": true,
            "serverSide": true,

            "ajax": {
                "url": "/content/search/quicksearch",
                "type": 'POST',
                "data": function ( d ) {
                    d.myKey = "10";
                    // d.custom = $('#myInput').val();
                    // etc
                }
            },

            "columns": [
                { "data": "id" },
                { "data": "sku" },
                { "data": "title" },
                { "data": "price" },
                { "data": "quantity" },
                { "data": "site" },
                { "data": "status" },
                { "data": "visibility" }
            ],

            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "lengthMenu": "_MENU_ records",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }/*,
             "columnDefs": [{  // set default column settings
             'orderable': false,
             'targets': [0]
             }, {
             "searchable": false,
             "targets": [0]
             }]
             /*
             "order": [
             [1, "asc"]
             ] */// set first column as a default sort by asc
        });

        var tableWrapper = jQuery('#sample_1_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).attr("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });

        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };

    var attributesPopulate = function () {

        var table = $('#attributeTable');

        // begin first table
        table.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/content/attributemanagement/attributes/quicksearch",
                type: 'POST'

            },
            "columns": [
//                { "data": "id" },
                {
                    "class": "frontend",
                    "data": "frontend"
                },
                {
                    "class": "type",
                    "data": "input"
                },
                { "data": "dateModified" },
//                { "data": "user" }
                { "data": "fullname" },
                {
                    "defaultContent": "<a class='btn green-haze attEdit' data-toggle='modal' href='#attributeModal'>Edit <i class='fa fa-plus'></i></a>"
                },
                {
                    "class": "hidden attribute_id",
                    "data": "attId"
                }
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "lengthMenu": "_MENU_ records",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }/*,
             "columnDefs": [{  // set default column settings
             'orderable': false,
             'targets': [0]
             }, {
             "searchable": false,
             "targets": [0]
             }]
             /*
             "order": [
             [1, "asc"]
             ] */// set first column as a default sort by asc
        });


        $('#attributeTable tbody').on('click', 'a.attEdit',function (e) {
            e.preventDefault();
            var edit = $(this);
            var type = edit.closest('td').siblings('td.type').text();
            var frontend = edit.closest('td').siblings('td.frontend').text();
            var attributeId = edit.closest('td').siblings('td.attribute_id').text();
            var params = {
                "attributeId": attributeId
            };
            $('#frontend_label').val(frontend);
            console.log('haha');

            if(type != 'select'){
                $('.options').hide();
            } else{
                console.log('hoho');
                $('input.attributeId').val(attributeId);
                $('.options').show();
                $.post('/content/attributemanagement/options/quicksearch', params, function(data){
                    //                console.log('clicked');
                    table.api().draw();
                });
            }
            var Type = type.replace(/^(.)|\s(.)/g, function($1){ return $1.toUpperCase( ); });
            $('select.type option').each(function(i,e){
                var option = $(this);
                if (Type == option.text() ){
                    option.prop('selected',true);
                }
            });
        });

    };
    var optionsPopulate = function () {
        var table = $('#optionsTable');

        // begin first table
        table.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/content/attributemanagement/options/quicksearch",
                type: 'POST'

            },
            "columns": [
//                { "data": "id" },
                { "data": "options" },
                { "data": "dateModified" },
                { "data": "fullname" },
                {
                    "class": "hidden att_id",
                    "data": "attId"
                },
                {
                    "defaultContent": "<a class='btn green-haze options_delete' data-toggle='modal' href='#optionsModal'>Delete<i class='fa fa-plus'></i></a>"
                }
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "lengthMenu": "_MENU_ records",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }/*,
             "columnDefs": [{  // set default column settings
             'orderable': false,
             'targets': [0]
             }, {
             "searchable": false,
             "targets": [0]
             }]
             /*
             "order": [
             [1, "asc"]
             ] */// set first column as a default sort by asc
        });


        $('input[aria-controls=optionsTable]').on('keyup', 'td:nth-child(1)', function(){
            var opt = $(this);
            console.log(opt);
            var attributeId = opt.closest('td').siblings('td.att_id').text();
            var params = {
                "attributeId": attributeId
            };
            $.post('/content/attributemanagement/options/quicksearch', params, function(data){
                table.api().draw();
            });
        });
        $('a.options').on('click',function (e) {
            console.log('works');
            var params = {
                "attributeId" :  $('input.attributeId').val()
            };
            console.log($('input.attributeId').val());
//            $.post('/content/attributemanagement/options/quicksearch', params, function(data){
//            $.post('/content/attributemanagement/options/quicksearch', params, function(data){
//                //                console.log('clicked');
//                table.api().draw();
//            });
        });

        table.on('click', '.options_delete', function (e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            nRow.remove();
        });

    };

    var populateSkuHistory = function () {

        var table = $('#skuHistoryDisplay').dataTable({

            "processing": true,
            "serverSide": true,

            ajax: {
                "url": "/sku-history",
                "type": 'POST',
                "data": function (d){
                    d.filterDateRange =  $('#filterDateRange').val()
                }
            },

            "columns": [
                {
                    "class": 'hidden entityId',
                    "data": 'id'
                },
                {
                    "class": "hidden entity_id",
                    "data": "entityID"
                },
                {
                    "class": "sku",
                    "data": "sku"
                },
                {
//                    "width": "5%",
                    "class":"old_value ",
                    "data": "oldValue"
                },
                {
//                    "width": "5%",
                    "class":"new_value",
                    "data": "newValue"
                },
//                {
//                    "class": "hidden manId",
//                    "data": "manufacturerID"
//                },

                { "data": "manufacturer" },
                {
                    "data": "user"
                },
                { "data": "dataChanged" },
                {
                    "class": 'property_name',
                    "data": "property"
                },
                {
                    "class":"revert",
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td><a href='#'>Revert</a></td>"
                }

            ],
            "columnDefs": [
                {
                    "width": "5%",
                    "targets": 2
                },{
                    "width": "30%",
                    "targets": 3
                },{
                    "width": "30%",
                    "targets": 4
                }
            ],

            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "full_numbers",
            "language": {
                "lengthMenu": "_MENU_ records",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }
        });

        $('#skuHistoryDisplay tbody').on('click', 'a.more_old',function (e) {
            e.preventDefault();
            var more = $(this);
            var position = more.closest('tr').index();
            console.log(position);
            var params = {
                'more_old': position,
                'moreold': 'moreold'
            };
            $.post('/sku-history', params, function(data){
                //nothing should happen except redraw the table.
                table.api().draw();
            });
        });

        $('#skuHistoryDisplay tbody').on('click', 'td.revert',function (e) {
            e.preventDefault();
            var revert= $(this);
            var oldValue = revert.siblings('td.old_value').text();
            var newValue = revert.siblings('td.new_value').text();
            var entityID = revert.siblings('td.entity_id').text();
            var property = revert.siblings('td.property_name').text();
            var sku = revert.siblings('td.sku').text();
            var pk = revert.siblings('td.entityId').text();
//            var manOptionID = revert.siblings('td.manId').text();
            var params = {
                'old'   :   oldValue,
                'new'   :   newValue,
                'eid'   :   entityID,
                'pk'    :   pk,
                'property': property,
                'sku':  sku
//                'manOpId': manOptionID
            };

            $.post('/sku-history/revert', params, function(data){
                //nothing should happen except redraw the table.
                table.api().draw();
            });
        });
    };

    var populateMageHistory = function () {
        var table = $('#mageHistoryDisplay');


        var otable = table.dataTable({

            "processing": true,
            "serverSide": true,

            ajax: {
                "url": "/mage-push-history",
                "type": 'POST'
//                "data": function (d){
//                    d.filterDateRange =  $('#filterDateRange').val()
//                }
            },

            "columns": [//
                {
                    "data": "sku"
                }, {
                    "data": 'resource'
                }, {
                    "data": "speed"
                }, {
                    "data": "fullname"
                }, {
                    "data": "datepushed"
                }, {
                    "data": "status"
                }
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "lengthMenu": "_MENU_ records",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }
        });
    };

    var initAcessoryDisplay = function () {
        var dtable = $('#accessoriesDisplay').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/content/product/accessories",
                type: 'POST',
                "data": function (d){
                    d.related = $("#accessoriesForm input[name*='linkedSku]']").serializeArray();
                    d.position = $("#accessoriesForm input[name*='position]']").serializeArray();
                }
            },
            "columns": [
                { "data": "sort", "orderable": false },
                { "data": "Sku", "orderable": false },
                { "data": "title", "orderable": false },
                { "data": "status", "orderable": false  },
                { "data": "price", "orderable": false },
                { "data": "quantity", "orderable": false },
                { "data": "edit", "orderable": false }


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        //add acessories
        $('#accessoriesDisplay tbody').on('click', '#addCross', function(){
            var entityid = $("#generalForm input[name*='oldData[id]']").val();
            var linkedsku = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var id = Math.floor((Math.random() * 1000000000) + 1000000);
            var formsize = $("#accessoriesForm input[name*='linkedSku]']").serializeArray().length;
            //grab length of form

            var newAccessory = '<input type="hidden" name="accessories['+formsize+'][id]" value = "'+id+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][entityid]" value = "'+entityid+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][linkedSku]" value = "'+linkedsku+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][position]" value = "0">';

            $('#accessoriesForm').append(newAccessory);
            dtable.draw();
        });

        //remove acessories
        $('#accessoriesDisplay tbody').on('click', '#removeCross', function(){
            var linkedForm = $("#accessoriesForm input[name*='[linkedSku]']").serializeArray();
            var linkedId = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var form = $("#accessoriesForm").serializeArray();

            //find position to delete
            var i;
            for(i=0;i<linkedForm.length;i++){
                if(linkedForm[i]['value'] == linkedId){
                    var positionToDelete = "["+i+"]";
                }
            }

            //loop though form and remove position
            var newForm='';
            for(i=0;i<form.length;i++){
                //Position to ignore in new form
                if(form[i]['name'].indexOf(positionToDelete) != '-1'){

                    //deduct from future itirations
                    //set flag to begin deductions
                }
                //else add to form
                else{
                    newForm += '<input type="hidden" name="'+form[i]['name']+'" value="'+form[i]['value']+'">'
                }
            }

            //replace form
            $('#accessoriesForm').empty().append(newForm);

            //update form order
            var x=0;
            i=0;
            $('#accessoriesForm *').filter(':input').each(function(){
                if(x<4){
                    x++;
                }
                else{
                    x=0;
                    i++;
                }

                var tempname = $(this).attr('name');
                tempname.replace(new RegExp(/[0-9]*/),i);
                $(this).attr(tempname);
            });

            dtable.draw();

        });




//Position Changes
        $('#accessoriesDisplay tbody').on('keyup', '.pos', function(){
            var linkedSku = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var formSkus = $("#accessoriesForm input[name*='linkedSku]']").serializeArray();
            var position = '';

            var i;
            for(i=0; i<formSkus.length; i++){
                if(formSkus[i]['value'] == linkedSku){
                    position = "accessories["+i+"][position]";
                    $("#accessoriesForm input[name*='"+position+"']").val($(this).val());
                }
            }
            dtable.draw();
        });



    };

    var initCrossSellDisplay = function () {
        //var table = $('#crossSellDisplay');
        var dtable = $('#crossSellDisplay').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/content/product/accessories",
                type: 'POST',
                "data": function (d){
                    d.related = $("#crossSellForm input[name*='linkedSku]']").serializeArray();
                    d.position = $("#crossSellForm input[name*='position]']").serializeArray();
                }
            },
            "columns": [
                { "data": "sort", "orderable": false },
                { "data": "Sku", "orderable": false },
                { "data": "title", "orderable": false },
                { "data": "status", "orderable": false  },
                { "data": "price", "orderable": false },
                { "data": "quantity", "orderable": false },
                { "data": "edit", "orderable": false }


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        //add acessories
        $('#crossSellDisplay tbody').on('click', '#addCross', function(){
            var entityid = $("#generalForm input[name*='oldData[id]']").val();
            var linkedsku = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var id = Math.floor((Math.random() * 1000000000) + 1000000);
            var formsize = $("#crossSellForm input[name*='linkedSku]']").serializeArray().length;
            //grab length of form

            var newAccessory = '<input type="hidden" name="accessories['+formsize+'][id]" value = "'+id+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][entityid]" value = "'+entityid+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][linkedSku]" value = "'+linkedsku+'">';
            newAccessory += '<input type="hidden" name="accessories['+formsize+'][position]" value = "0">';

            $('#crossSellForm').append(newAccessory);
            dtable.draw();
        });

        //remove acessories
        $('#crossSellDisplay tbody').on('click', '#removeCross', function(){
            var linkedForm = $("#crossSellForm input[name*='[linkedSku]']").serializeArray();
            var linkedId = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var form = $("#crossSellForm").serializeArray();

//find position to delete
            var i;
            for(i=0;i<linkedForm.length;i++){
                if(linkedForm[i]['value'] == linkedId){
                    var positionToDelete = "["+i+"]";
                }
            }

//loop though form and remove position
            var newForm='';
            for(i=0;i<form.length;i++){
                //Position to ignore in new form
                if(form[i]['name'].indexOf(positionToDelete) != '-1'){

                    //deduct from future itirations
                    //set flag to begin deductions
                }
                //else add to form
                else{
                    newForm += '<input type="hidden" name="'+form[i]['name']+'" value="'+form[i]['value']+'">'
                }
            }

//replace form
            $('#crossSellForm').empty().append(newForm);

//update form order
            var x=0;
            i=0;
            $('#crossSellForm *').filter(':input').each(function(){
                if(x<4){
                    x++;
                }
                else{
                    x=0;
                    i++;
                }

                var tempname = $(this).attr('name');
                tempname.replace(new RegExp(/[0-9]*/),i);
                $(this).attr(tempname);

                console.log(tempname);
            });

            dtable.draw();

        });




        //Position Changes
        $('#crossSellDisplay tbody').on('keyup', '.pos', function(){
            var linkedSku = $(this).closest('tr').find('td').eq(1).find('h6').text();
            var formSkus = $("#crossSellForm input[name*='linkedSku]']").serializeArray();
            var position;
            var i;

            for(i=0; i<formSkus.length; i++){
                if(formSkus[i]['value'] == linkedSku){
                    position = "accessories["+i+"][position]";
                    $("#crossSellForm input[name*='"+position+"']").val($(this).val());
                }
            }
            dtable.draw();

            //redraw table to sort based on first column
            //or
            //call ajax again and sort by position values in PHP

        });
    };

    function checkedUpdatedDataTable()
    {
        //            This is for updated products
        var uncheckedItemLength = $('tbody input.skuItem:checkbox:not(":checked")').length;
        var checkedItemLength = $('tbody input.skuItem:checkbox(":checked")').length;
        var checkedItems = checkedItemLength - uncheckedItemLength;

        //            This is for new or deleted categories
        var uncheckedCategoryLength = $('tbody input.skuCategory:checkbox:not(":checked")').length;
        var checkboxCategoryLength = $('tbody input.skuCategory:checkbox(":checked")').length;
        var checkedCategories = checkboxCategoryLength - uncheckedCategoryLength;

        //            This is for new or deleted related products
        var uncheckedLinkedLength = $('tbody input.skuLink:checkbox:not(":checked")').length;
        var checkboxLinkedLength = $('tbody input.skuLink:checkbox(":checked")').length;
        var checkedLinked = checkboxLinkedLength - uncheckedLinkedLength;

        var checked = checkedItems + checkedCategories + checkedLinked;
        if ( checked ) {
            $('.pushItemsBtn').empty().append("Push " + checked + " Items");
        } else {
            $('.pushItemsBtn').empty().append("Push Items");
        }
        if ( checked > 0 ) {
            $('.pushItemsBtn').attr('disabled',false);
        }
        if ( checked == 0 ) {
            $('.pushItemsBtn').attr('disabled',true);
        }
    }

    var updateMageItems = function () {
        var updateItems = $('#kpiUpdates');
        var uItems = updateItems.DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds/update-items",
                type: 'POST'
            },
            "columns": [

                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='sku_item'>"+
                        "<label for='skuItem'></label>"+
                        "<input type='checkbox' class='skuItem' id='skuItem' name='skuItem[][id]' value=''/></td>"
                },
//                {
//                    "class": "hidden count",
//                    "data": "count"
//                },
                {
                    "class": "eid",
                    "data": "id"
                },
                {
                    "class": "sku",
                    "data": "item"},
                {
                    "class": "hidden prty",
                    "data": "oproperty"
                },
                {
                    "data": "property"
                },
                {
                    "class": "newval",
                    "data": "newValue"
                },
                { "data": "ldate"},
                { "data": "fullName"}


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        var groupSku = $('#skuItems');

        $('#kpiUpdates tbody').on('change', '#skuItem',function (e) {
            e.preventDefault();
            var idChange = $(this);
            var entityId = idChange.closest('td').siblings('td.eid').text();
            var property = idChange.closest('td').siblings('td.prty').text();
            var newValue = idChange.closest('td').siblings('td.newval').html();
            var sku = idChange.closest('td').siblings('td.sku').text();
            var position = idChange.closest('tr').index();
            var uncheckedItemLength = $('tbody input.skuItem:checkbox:not(":checked")').length;
            var checkedItemLength = $('tbody input.skuItem:checkbox(":checked")').length;
            var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuItem['+position+'][id]',class: 'SkuItem',value: entityId});
            var hiddenProperty = $('<input>').attr({type: 'hidden',name: 'skuItem['+position+'][property]',class: 'SkuItem',value: property});
            var hiddenNewValue = $('<input>').attr({type: 'hidden',name: 'skuItem['+position+'][newValue]',class: 'SkuItem',value: newValue});
            var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuItem['+position+'][sku]',class: 'SkuItem',value: sku});
            checkedUpdatedDataTable();

            if ( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenProperty.appendTo('form#mageForm');
                hiddenNewValue.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
                if( uncheckedItemLength == 0) {
                    groupSku.prop('checked','checked');
                }
            } else {
                $("form#mageForm input[name='skuItem["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][property]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][newValue]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][sku]']").remove();
                if( uncheckedItemLength < checkedItemLength ) {
                    groupSku.prop('checked',false);
                }
            }
        });

        groupSku.on('change',function(){
            var uncheckedItemLength = $('tbody input.skuItem:checkbox:not(":checked")').length;
            var checkedItemLength = $('tbody input.skuItem:checkbox(":checked")').length;
            var item = $('#kpiUpdates tbody #skuItem');

            if( groupSku.prop("checked") ) {
                item.each(function(i) {
                    var entityId = item.closest('td').siblings('td.eid').eq(i).text();
                    var property = item.closest('td').siblings('td.prty').eq(i).text();
                    var newValue = item.closest('td').siblings('td.newval').eq(i).html();
                    var sku = item.closest('td').siblings('td.sku').eq(i).text();

                    var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][id]',class: 'SkuItem',value: entityId});
                    var hiddenProperty = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][property]',class: 'SkuItem',value: property});
                    var hiddenNewValue = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][newValue]',class: 'SkuItem',value: newValue});
                    var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][sku]',class: 'SkuItem',value: sku});
                    hiddenId.appendTo('form#mageForm');
                    hiddenProperty.appendTo('form#mageForm');
                    hiddenNewValue.appendTo('form#mageForm');
                    hiddenSku.appendTo('form#mageForm');
                });
                if ( uncheckedItemLength < checkedItemLength  ) {
                    $('form#mageForm input.SkuItem').remove();
                    item.each(function(i) {
                        var entityId = item.closest('td').siblings('td.eid').eq(i).text();
                        var property = item.closest('td').siblings('td.prty').eq(i).text();
                        var newValue = item.closest('td').siblings('td.newval').eq(i).html();
                        var sku = item.closest('td').siblings('td.sku').eq(i).text();

                        var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][id]',class: 'SkuItem',value: entityId});
                        var hiddenProperty = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][property]',class: 'SkuItem',value: property});
                        var hiddenNewValue = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][newValue]',class: 'SkuItem',value: newValue});
                        var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuItem['+i+'][sku]',class: 'SkuItem',value: sku});
                        hiddenId.appendTo('form#mageForm');
                        hiddenProperty.appendTo('form#mageForm');
                        hiddenNewValue.appendTo('form#mageForm');
                        hiddenSku.appendTo('form#mageForm');
                    });
                }
                $('.skuItem').prop('checked',true);
//                $('.pushItemsBtn').attr('disabled',false);
            }
            if( !groupSku.prop("checked") ) {
                $('.skuItem').prop('checked',false);
                $('form#mageForm input.SkuItem').remove();
            }

            checkedUpdatedDataTable();

        });
    };

    var updateMageCategories = function () {
        var dTable = $('#kpiCategories');
        var dtable = dTable.DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds/update-categories",
                type: 'POST'
            },
            "columns": [

                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='sku_category'>"+
                        "<label for='skuCategory'></label>"+
                        "<input type='checkbox' class='skuCategory' id='skuCategory' name='skuCategory[][id]' value=''/></td>"
                },
                {
                    "class": "sku",
                    "data": "sku"
                },
                {
                    "class": "eid",
                    "data": "id"
                },
                {
                    "class": "hidden catid",
                    "data": "categoryId"
                },
                {
                    "data": "category"
                },
                {
                    "class": "hidden dataState",
                    "data": "dataState"
                },
                {
                    "data": "state"
                },
                { "data": "fullname"}


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        var groupCategory = $('#skuCategories');

        $('#kpiCategories tbody').on('change', '#skuCategory',function (e) {
            e.preventDefault();
            var categories = $(this);
            var entityId = categories.closest('td').siblings('td.eid').text();
            var categoryId = categories.closest('td').siblings('td.catid').text();
            var sku = categories.closest('td').siblings('td.sku').text();
            var dataState = categories.closest('td').siblings('td.dataState').text();
            var position = categories.closest('tr').index();
            var uncheckedLength = $('tbody input.skuCategory:checkbox:not(":checked")').length;
            var checkboxLength = $('tbody input.skuCategory:checkbox(":checked")').length;

            var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+position+'][id]',class: 'SkuCategory',value: entityId});
            var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuCategory['+position+'][sku]',class: 'SkuCategory',value: sku});
            var hiddenCategoryId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+position+'][categoryId]',class: 'SkuCategory',value: categoryId});
            var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuCategory['+position+'][dataState]',class: 'SkuCategory',value: dataState});
            checkedUpdatedDataTable();

            if ( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenCategoryId.appendTo('form#mageForm');
                hiddenDataState.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
                if( uncheckedLength == 0) {
                    groupCategory.prop('checked','checked');
                }
            } else {
                $("form#mageForm input[name='skuCategory["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][categoryId]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][dataState]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][sku]']").remove();

                if( uncheckedLength < checkboxLength ) {
                    groupCategory.prop('checked',false);
                }
            }
        });

        groupCategory.prop('checked',false);
        groupCategory.on('change',function(){
            var category = $('#kpiCategories tbody #skuCategory');
            var uncheckedLength = $('tbody input.skuCategory:checkbox:not(":checked")').length;
            var checkboxLength = $('tbody input.skuCategory:checkbox(":checked")').length;
            if( groupCategory.prop("checked") ) {
                category.each(function(i) {
                    var entityId = category.closest('td').siblings('td.eid').eq(i).text();
                    var categoryId = category.closest('td').siblings('td.catid').eq(i).text();
                    var dataState = category.closest('td').siblings('td.dataState').eq(i).text();
                    var sku = category.closest('td').siblings('td.sku').eq(i).text();

                    var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][id]',class: 'SkuCategory',value: entityId});
                    var hiddenCategoryId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][categoryId]',class: 'SkuCategory',value: categoryId});
                    var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][dataState]',class: 'SkuCategory',value: dataState});
                    var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][sku]',class: 'SkuCategory',value: sku});

                    hiddenId.appendTo('form#mageForm');
                    hiddenCategoryId.appendTo('form#mageForm');
                    hiddenDataState.appendTo('form#mageForm');
                    hiddenSku.appendTo('form#mageForm');
                });
                if ( uncheckedLength < checkboxLength  ) {
                    $("form#mageForm input.SkuCategory").remove();
                    category.each(function(i) {
                        var entityId = category.closest('td').siblings('td.eid').eq(i).text();
                        var categoryId = category.closest('td').siblings('td.catid').eq(i).text();
                        var dataState = category.closest('td').siblings('td.dataState').eq(i).text();
                        var sku = category.closest('td').siblings('td.sku').eq(i).text();

                        var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][id]',class: 'SkuCategory',value: entityId});
                        var hiddenCategoryId = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][categoryId]',class: 'SkuCategory',value: categoryId});
                        var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][dataState]',class: 'SkuCategory',value: dataState});
                        var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuCategory['+i+'][sku]',class: 'SkuCategory',value: sku});

                        hiddenId.appendTo('form#mageForm');
                        hiddenCategoryId.appendTo('form#mageForm');
                        hiddenDataState.appendTo('form#mageForm');
                        hiddenSku.appendTo('form#mageForm');
                    });
                }
                $('.skuCategory').prop('checked',true);
            }
            if( !groupCategory.prop("checked") ) {
                $('.skuCategory').prop('checked',false);
                $('form#mageForm input.SkuCategory').remove();
            }
            checkedUpdatedDataTable();
        });
    };

    var updateMageRelatedProducts = function () {
        var dTable = $('#kpiRelatedProducts');
        var dtable = dTable.DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds/update-related",
                type: 'POST'
            },
            "columns": [

                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='sku_related'>"+
                        "<label for='skuLink'></label>"+
                        "<input type='checkbox' class='skuLink' id='skuLink' name='skuLink[][id]' value=''/></td>"
                },
                {
                    "class": "eid",
                    "data": "id"
                },
                {
                    "class": "sku",
                    "data": "sku"
                },

                {
                    "class": "linkedId",
                    "data": "linkedId"
                },
                {
                    "class": "linkedSku",
                    "data": "linkedSku"
                },
                {
                    "class": "hidden dataState",
                    "data": "dataState"
                },
                {
                    "data": "state"
                },
                {
                    "class": "type",
                    "data": "type"
                },
                { "data": "fullname"}


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        var groupLink = $('#skuLinks');

        $('#kpiRelatedProducts tbody').on('change', '#skuLink',function (e) {
            e.preventDefault();
            var link = $(this);
            var entityId = link.closest('td').siblings('td.eid').text();
            var sku = link.closest('td').siblings('td.sku').text();
            var linkedId = link.closest('td').siblings('td.linkedId').text();
            var dataState = link.closest('td').siblings('td.dataState').text();
            var type = link.closest('td').siblings('td.type').text();
            var position = link.closest('tr').index();
            var uncheckedLinkedLength = $('tbody input.skuLink:checkbox:not(":checked")').length;
            var checkboxLinkedLength = $('tbody input.skuLink:checkbox(":checked")').length;

            var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuLink['+position+'][id]',class: 'SkuLink',value: entityId});
            var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuLink['+position+'][sku]',class: 'SkuLink',value: sku});
            var hiddenLinkedId = $('<input>').attr({type: 'hidden',name: 'skuLink['+position+'][linkedId]',class: 'SkuLink',value: linkedId});
            var hiddenType = $('<input>').attr({type: 'hidden',name: 'skuLink['+position+'][type]',class: 'SkuLink',value: type});
            var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuLink['+position+'][dataState]',class: 'SkuLink',value: dataState});

            if ( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
                hiddenLinkedId.appendTo('form#mageForm');
                hiddenType.appendTo('form#mageForm');
                hiddenDataState.appendTo('form#mageForm');
                if( uncheckedLinkedLength == 0) {
                    groupLink.prop('checked','checked');
                }
            } else {
                $("form#mageForm input[name='skuLink["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][sku]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][linkedId]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][type]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][dataState]']").remove()
                if( uncheckedLinkedLength < checkboxLinkedLength ) {
                    groupLink.prop('checked',false);
                }
            }
            checkedUpdatedDataTable();
        });

        groupLink.prop('checked',false);
        groupLink.on('change',function(){
            var link = $('#kpiRelatedProducts tbody #skuLink');
            var uncheckedLinkedLength = $('tbody input.skuLink:checkbox:not(":checked")').length;
            var checkboxLinkedLength = $('tbody input.skuLink:checkbox(":checked")').length;

            if( groupLink.prop("checked") ) {
                link.each(function(i) {
                    var entityId = link.closest('td').siblings('td.eid').eq(i).text();
                    var sku = link.closest('td').siblings('td.sku').eq(i).text();
                    var linkedId = link.closest('td').siblings('td.linkedId').eq(i).text();
                    var dataState = link.closest('td').siblings('td.dataState').eq(i).text();
                    var type = link.closest('td').siblings('td.type').eq(i).text();

                    var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][id]',class: 'SkuLink',value: entityId});
                    var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][sku]',class: 'SkuLink',value: sku});
                    var hiddenLinkedId = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][linkedId]',class: 'SkuLink',value: linkedId});
                    var hiddenType = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][type]',class: 'SkuLink',value: type});
                    var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][dataState]',class: 'SkuLink',value: dataState});

                    hiddenId.appendTo('form#mageForm');
                    hiddenDataState.appendTo('form#mageForm');
                    hiddenSku.appendTo('form#mageForm');
                    hiddenLinkedId.appendTo('form#mageForm');
                    hiddenType.appendTo('form#mageForm');
                });
                if ( uncheckedLinkedLength < checkboxLinkedLength  ) {
                    $('form#mageForm input.SkuLink').remove();
                    link.each(function(i) {
                        var entityId = link.closest('td').siblings('td.eid').eq(i).text();
                        var sku = link.closest('td').siblings('td.sku').eq(i).text();
                        var linkedId = link.closest('td').siblings('td.linkedId').eq(i).text();
                        var dataState = link.closest('td').siblings('td.dataState').eq(i).text();
                        var type = link.closest('td').siblings('td.type').eq(i).text();

                        var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][id]',class: 'SkuLink',value: entityId});
                        var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][sku]',class: 'SkuLink',value: sku});
                        var hiddenLinkedId = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][linkedId]',class: 'SkuLink',value: linkedId});
                        var hiddenType = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][type]',class: 'SkuLink',value: type});
                        var hiddenDataState = $('<input>').attr({type: 'hidden',name: 'skuLink['+i+'][dataState]',class: 'SkuLink',value: dataState});

                        hiddenId.appendTo('form#mageForm');
                        hiddenDataState.appendTo('form#mageForm');
                        hiddenSku.appendTo('form#mageForm');
                        hiddenLinkedId.appendTo('form#mageForm');
                        hiddenType.appendTo('form#mageForm');
                    });
                }
                $('.skuLink').prop('checked',true);
            }
            if( !groupLink.prop("checked") ) {
                $('.skuLink').prop('checked',false);
                $('form#mageForm input.SkuLink').remove();
            }
            checkedUpdatedDataTable();
        });
    };

    function checkedImagesDataTable()
    {
        var uncheckedImageLength = $('tbody input.skuImage:checkbox:not(":checked")').length;
        var checkedImageLength = $('tbody input.skuImage:checkbox(":checked")').length;

        var checkedImages = checkedImageLength - uncheckedImageLength;

        if ( checkedImages ) {
            $('.pushImagesBtn').empty().append("Push " + checkedImages + " New Images");
        } else {
            $('.pushImagesBtn').empty().append("Push New Images");
        }

        if ( checkedImages > 0 ) {
            $('.pushImagesBtn').attr('disabled',false);
        }

        if ( checkedImages == 0 ) {
            $('.pushImagesBtn').attr('disabled',true);
        }
    }

    var newSkuImages = function () {
        var newImages = $('#kpiImages');
        var nImages = newImages.DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds/new-images",
                type: 'POST'
            },
            "columns": [
                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='sku_image'>"+
                                            "<label for='skuImage'></label>"+
                                            "<input type='checkbox' class='skuImage' id='skuImage' name='skuImage[][id]' value=''/>" +
                                        "</td>"
                },
                {
                    "class": "hidden imageid",
                    "data": "valueid"
                },
                {
                    "class": "hidden position",
                    "data": "position"
                },
                {
                    "class": "eid",
                    "data": "entityId"
                },

                {
                    "class": "sku",
                    "data": "sku"
                },
                {
                    "class": "lbl",
                    "data": "label"
                },
                {
                    "class": "filename",
                    "data": "filename"
                },
                { "data": "creation"},
                { "data": "fullname"}


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});
        var groupImage = $('#skuImages');

        $('#kpiImages tbody').on('change', '#skuImage',function (e) {
            e.preventDefault();
            var newImage = $(this);
            var imageId = newImage.closest('td').siblings('td.imageid').text();
            var entityId = newImage.closest('td').siblings('td.eid').text();
            var label = newImage.closest('td').siblings('td.lbl').text();
            var sku = newImage.closest('td').siblings('td.sku').text();
            var imgPos = newImage.closest('td').siblings('td.position').text();
            var filename = newImage.closest('td').siblings('td.filename').children('img').attr('src');
            var position = newImage.closest('tr').index();

            var uncheckedImageLength = $('tbody input.skuImage:checkbox:not(":checked")').length;
            var checkedImageLength = $('tbody input.skuImage:checkbox(":checked")').length;
            var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][imageid]',class: 'SkuImage',value: imageId});
            var hiddenEntityId = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][id]',class: 'SkuImage',value: entityId});
            var hiddenLabel = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][label]',class: 'SkuImage',value: label});
            var hiddenFilename = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][filename]',class: 'SkuImage',value: filename});
            var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][sku]',class: 'SkuImage',value: sku});
            var hiddenPosition = $('<input>').attr({type: 'hidden',name: 'skuImage['+position+'][position]',class: 'SkuImage',value: imgPos});
            checkedImagesDataTable();

            if ( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageImages');
                hiddenEntityId.appendTo('form#mageImages');
                hiddenFilename.appendTo('form#mageImages');
                hiddenSku.appendTo('form#mageImages');
                hiddenPosition.appendTo('form#mageImages');
                hiddenLabel.appendTo('form#mageImages');
                if( uncheckedImageLength == 0) {
                    groupImage.prop('checked','checked');
                }
            } else {
                $("form#mageImages input[name='skuImage["+ position +"][imageid]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][id]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][filename]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][sku]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][position]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][label]']").remove();

                if( uncheckedImageLength < checkedImageLength ) {
                    groupImage.prop('checked',false);
                }
            }
        });
        groupImage.prop('checked',false);
        groupImage.on('change',function(){
            var uncheckedImageLength = $('tbody input.skuImage:checkbox:not(":checked")').length;
            var checkedImageLength = $('tbody input.skuImage:checkbox(":checked")').length;
            var image = $('#kpiImages tbody #skuImage');

            if( groupImage.prop("checked") ) {
                image.each(function(i) {
                    var imageId = image.closest('td').siblings('td.imageid').eq(i).text();
                    var entityId = image.closest('td').siblings('td.eid').eq(i).text();
                    var sku = image.closest('td').siblings('td.sku').eq(i).text();
                    var label = image.closest('td').siblings('td.lbl').eq(i).text();
                    var imgPos = image.closest('td').siblings('td.position').eq(i).text();
                    var filename = image.closest('td').siblings('td.filename').eq(i).children('img').attr('src');

                    var hiddenId = $('<input>').attr({type: 'hidden',class:  'SkuImage',name: 'skuImage['+i+'][imageid]', value: imageId});
                    var hiddenEntityId = $('<input>').attr({type: 'hidden',class:  'SkuImage',name: 'skuImage['+i+'][id]',value: entityId});
                    var hiddenFilename = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][filename]',class:  'SkuImage',value: filename});
                    var hiddenLabel = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][label]',class: 'SkuImage',value: label});
                    var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][sku]',class:  'SkuImage',value: sku});
                    var hiddenPosition = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][position]',class:  'SkuImage',value: imgPos});

                    hiddenId.appendTo('form#mageImages');
                    hiddenEntityId.appendTo('form#mageImages');
                    hiddenFilename.appendTo('form#mageImages');
                    hiddenSku.appendTo('form#mageImages');
                    hiddenPosition.appendTo('form#mageImages');
                    hiddenLabel.appendTo('form#mageImages');
                });
                if( uncheckedImageLength < checkedImageLength ) {
                    $('form#mageImages input.SkuImage').remove();

                    image.each(function(i) {
                        var imageId = image.closest('td').siblings('td.imageid').eq(i).text();
                        var entityId = image.closest('td').siblings('td.eid').eq(i).text();
                        var sku = image.closest('td').siblings('td.sku').eq(i).text();
                        var label = image.closest('td').siblings('td.lbl').eq(i).text();
                        var imgPos = image.closest('td').siblings('td.position').eq(i).text();
                        var filename = image.closest('td').siblings('td.filename').eq(i).children('img').attr('src');

                        var hiddenId = $('<input>').attr({type: 'hidden',class:  'SkuImage',name: 'skuImage['+i+'][imageid]', value: imageId});
                        var hiddenEntityId = $('<input>').attr({type: 'hidden',class:  'SkuImage',name: 'skuImage['+i+'][id]',value: entityId});
                        var hiddenFilename = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][filename]',class:  'SkuImage',value: filename});
                        var hiddenLabel = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][label]',class: 'SkuImage',value: label});
                        var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][sku]',class:  'SkuImage',value: sku});
                        var hiddenPosition = $('<input>').attr({type: 'hidden',name: 'skuImage['+i+'][position]',class:  'SkuImage',value: imgPos});

                        hiddenId.appendTo('form#mageImages');
                        hiddenEntityId.appendTo('form#mageImages');
                        hiddenFilename.appendTo('form#mageImages');
                        hiddenSku.appendTo('form#mageImages');
                        hiddenPosition.appendTo('form#mageImages');
                        hiddenLabel.appendTo('form#mageImages');
                    });
                }
                $('.skuImage').prop('checked',true);
            }
            if( !groupImage.prop("checked") ) {
                $('.skuImage').prop('checked',false);
                $('form#mageImages input.SkuImage').remove();
            }
            checkedImagesDataTable();
        });
    };

    function checkedNewProductsDataTable()
    {
        var uncheckedLength = $('tbody input.skuNewProduct:checkbox:not(":checked")').length;
        var checkedLength = $('tbody input.skuNewProduct:checkbox(":checked")').length;

        var checkedProducts = checkedLength - uncheckedLength;

        if ( checkedProducts ) {
            $('.pushNewProducts').empty().append("Push " + checkedProducts + " New Products");
        } else {
            $('.pushNewProducts').empty().append("Push New Products");
        }


        if ( checkedProducts == 0 ) {
            $('.pushNewProducts').attr('disabled',true);
        }
        if ( checkedProducts > 0 ) {
            $('.pushNewProducts').attr('disabled', false);
        }
    }


    var newProducts = function () {
        var newProductTable = $('#kpiNewProducts');
        var npTable = newProductTable.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds/new-products",
                type: 'POST'
            },
            "columns": [
                {
                    "orderable":    false,
//                    "class":          'details-control',
                    "data": null,
                    "defaultContent":   "<td id='sku_new_prod'>"+
                                            "<label for='skuNewProduct'></label>"+
                                            "<input type='checkbox' class='skuNewProduct' id='skuNewProduct' name='skuNewProduct[][id]' value=''/>" +
                                        "</td>"
                },
//                {
//                    "class":          'details-control',
//                    "orderable":      false,
//                    "data":           null
//                },
                {
                    "class": "eid",
                    "data": "id"
                },
//                {
//                    "class": "hidden website",
//                    "data": "website"
//                },

                {
                    "class": "sku",
                    "data": "sku"
                },
//                {
//                    "class": "property hidden",
//                    "data": function(data){
//                        if (typeof data == 'object') {
//                            if( typeof data.property == 'object' ) {
//                                return data.property.stock_data;
//                            } else {
//                                return data.property;
//                            }
//                        }
//                    }
//                },
//                {
//                    "class": "value hidden",
//                    "data": "value"
//                },
                { "data": "creation"},
                { "data": "fullname"}


            ],
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "paging": true,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});
        var groupNewSku = $('#skuNewProducts');

        //The JS on here is the same here as it is for:
        //Categories, Related Products (Linked Products), New Images, and New Products
//        For individual checkboxes
        $('#kpiNewProducts tbody').on('change', '#skuNewProduct',function (e) {
            e.preventDefault();
            var newProds = $(this);
            //Grab the entity_id and Sku from the DOM and cache them.
            var id = newProds.closest('td').siblings('td.eid').text();
            var sku = newProds.closest('td').siblings('td.sku').text();
            //Grab the position of checkbox that was just checked.
            var position = newProds.closest('tr').index();
            //Grab the checkboxes that not have not been checked and that have been checked, respectively.
            var uncheckedLength = $('tbody input.skuNewProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuNewProduct:checkbox(":checked")').length;
            //This function is to display how many checkboxes have been selected and displays them in the button in the UI.
            checkedNewProductsDataTable();
            //Prepares the entity_id and Sku for a hidden element in the UI button.
            var hiddenId = $('<input>').attr({type: 'hidden',name: 'skuNewProduct['+position+'][id]',class: 'SkuNewProds',value: id});
            var hiddenSku = $('<input>').attr({type: 'hidden',name: 'skuNewProduct['+position+'][sku]',class: 'SkuNewProds',value: sku});

            //If x-position checkbox has been selected place it in the hidden button. Or if it is unchecked remove from the DOM.
            if ( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageNewProds');
                hiddenSku.appendTo('form#mageNewProds');
                 //If the last of the checkboxes are selected while the group checkbox is not, select the group checkbox.
                if( uncheckedLength == 0) {
                    groupNewSku.prop('checked','checked');
                }
            } else {
                $("form#mageNewProds input[name='skuNewProduct["+ position +"][id]']").remove();
                $("form#mageNewProds input[name='skuNewProduct["+ position +"][sku]']").remove();
                //If all checkboxes have been selected and then one of them is unselected uncheck the main checkbox for the group
                if( uncheckedLength < checkedLength ) {
                    groupNewSku.prop('checked',false);
                }
            }
        });
        //Upon DOM rendering when the API has been called. There obviously should be nothing checked.
        groupNewSku.prop('checked',false);

//        For all checkboxes
        groupNewSku.on('change',function(){
            //Cache variable.
            var uncheckedLength = $('tbody input.skuNewProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuNewProduct:checkbox(":checked")').length;
            var newProds = $('#kpiNewProducts tbody #skuNewProduct');
            //If the group checkbox has been selected iterate the DOM and go through each checkbox and grab entity_id and SKU and cache them.
            //Then prepare the hidden element
            //Then place in the DOM.
            if( groupNewSku.prop("checked") ) {
                newProds.each(function(i) {
                    var id = newProds.closest('td').siblings('td.eid').eq(i).text();
                    var sku = newProds.closest('td').siblings('td.sku').eq(i).text();

                    var hiddenId = $('<input>').attr({type:'hidden',class:'SkuNewProds',name:'skuNewProduct['+i+'][id]',value:id});
                    var hiddenSku = $('<input>').attr({type:'hidden',class:'SkuNewProds',name:'skuNewProduct['+i+'][sku]',value:sku});

                    hiddenId.appendTo('form#mageNewProds');
                    hiddenSku.appendTo('form#mageNewProds');
                });
                //If the group checkbox has been selected and there are already checkboxes selected.
                //Remove them.
                //Then iterate the DOM and go through each checkbox and grab entity_id and SKU and cache them.
                //Then prepare the hidden element
                //Then place in the DOM.
                //Subsequently select all checkboxes.
                if( uncheckedLength < checkedLength ) {
                    $('form#mageNewProds input.SkuNewProds').remove();
                    newProds.each(function(i) {
                        var id = newProds.closest('td').siblings('td.eid').eq(i).text();
                        var sku = newProds.closest('td').siblings('td.sku').eq(i).text();

                        var hiddenId = $('<input>').attr({type:'hidden',class:'SkuNewProds',name:'skuNewProduct['+i+'][id]',value:id});
                        var hiddenSku = $('<input>').attr({type:'hidden',class:'SkuNewProds',name:'skuNewProduct['+i+'][sku]',value:sku});

                        hiddenId.appendTo('form#mageNewProds');
                        hiddenSku.appendTo('form#mageNewProds');
                    });
                }
                $('.skuNewProduct').prop('checked',true);
            }
            //If the group checkbox is unchecked remove all hidden elements from the DOM and unselect all checkboxes.
            if( !groupNewSku.prop("checked") ) {
                $('.skuNewProduct').prop('checked',false);
                $('form#mageNewProds input.SkuNewProds').remove();
            }
            //This function is to display how many checkboxes have been selected and displays them in the button in the UI.
            checkedNewProductsDataTable();
        });
    };

    var webassignmentTable = function () {

        var table = $('#webassignmenttable');

        table.dataTable({
            "lengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "language": {
                "emptyTable":     "No data available in table",
                "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "_MENU_ records",
                "zeroRecords":    "No matching records found",
                "processing":     "Processing...",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            }});

        //populate edit popup
        $('#webassignmenttable tbody').on('click', 'tr', function(){
            var manufacturer = $(this).closest('tr').find('td').eq(0).text();
            var site = $(this).closest('tr').find('td').eq(1).text();
            $('#mfcLabel').text(manufacturer);
            $('#mfcInput').val(manufacturer);


            var sitecode;
            switch (site){
                case "Focus": sitecode = 1;
                    break;
                case "aSavings": sitecode = 0;
                    break;
                case "Focus / aSavings": sitecode = 2;
                    break;
            }
            $('#webID').val(sitecode);
        });
    };

    return {

        datatableUpdateChecked: function () {
            checkedUpdatedDataTable();
        },
        datatableImageChecked: function() {
            checkedImagesDataTable();
        },
        datatableNewProductChecked: function() {
            checkedNewProductsDataTable();
        },

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            initTable1();
            initAcessoryDisplay();
            initCrossSellDisplay();
            populateSkuHistory();
            populateMageHistory();
            webassignmentTable();
            attributesPopulate();
            optionsPopulate();
            updateMageItems();
            updateMageCategories();
            updateMageRelatedProducts();
            newSkuImages();
            newProducts();
        }

    };

}();
