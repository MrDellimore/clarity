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

            if(type != 'select'){
                $('.options').hide();
            } else{
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

//        alert( 'Data source: '+ table.api().ajax.url() );
//        table.columns[0].attr('class','entityId');
//        table.columns[4].attr('class','manId');
        $('#skuHistoryDisplay tbody').on('click', 'a.more_old',function (e) {
            e.preventDefault();
            var more = $(this);
            var position = more.closest('tr').index();
            console.log(position);
            var params = {
                'more_old': position,
                'moreold': 'moreold'
            };
//            var oldValue = revert.siblings('td.old_value').text();
//            var newValue = revert.siblings('td.new_value').text();
//            var entityID = revert.siblings('td.entity_id').text();
//            var property = revert.siblings('td.property_name').text();
//            var sku = revert.siblings('td.sku').text();
//            var pk = revert.siblings('td.entityId').text();
//            var manOptionID = revert.siblings('td.manId').text();
//            var params = {
//                'old'   :   oldValue,
//                'new'   :   newValue,
//                'eid'   :   entityID,
//                'pk'    :   pk,
//                'property': property,
//                'sku':  sku
////                'manOpId': manOptionID
//            };
//
//
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
//            if ( oldValue.length > 10 ) {
//                var shortOldValue = oldValue.substr(0,7) + $('td.old_value').html("<a href='more' id='more_old_value' >... </a>");
//            }

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

    var updateMageItems = function () {
        var dTable = $('#kpiUpdates');
        var dtable = dTable.DataTable({
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
            var newValue = idChange.closest('td').siblings('td.newval').text();
            var sku = idChange.closest('td').siblings('td.sku').text();
            var position = idChange.closest('tr').index();
//            var position = idChange.closest('td').siblings('td.count').text();
            var uncheckedLength = $('tbody input.skuItem:checkbox:not(":checked")').length;
            var checkboxLength = $('tbody input.skuItem:checkbox(":checked")').length;


            if ( $(this).prop('checked') ) {
                if( uncheckedLength == 0) {
                    groupSku.prop('checked','checked');
                }
            } else {
                if( checkboxLength < 3 ) {
                    groupSku.prop('checked',false);
                }
            }
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][id]',
                class: 'SkuItem',
                value: entityId
            });
            var hiddenProperty = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][property]',
                class: 'SkuItem',
                value: property
            });

            var hiddenNewValue = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][newValue]',
                class: 'SkuItem',
                value: newValue
            });
            var hiddenSku = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][sku]',
                class: 'SkuItem',
                value: sku
            });
            if( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenProperty.appendTo('form#mageForm');
                hiddenNewValue.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
            }
            if( !$(this).is(':checked') ) {
                $("form#mageForm input[name='skuItem["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][property]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][newValue]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][sku]']").remove();
            }
        });

        groupSku.on('change',function(){
            $('form#mageForm button').append('<div class="skuitem"></div>');
            if( $(this).prop("checked") ) {
                $('.skuItem').prop('checked',true);
            } else {
                $('form#mageForm input.SkuItem').remove();
                $('.skuItem').prop('checked',false);
            }
            var item = $('#kpiUpdates tbody #skuItem');
            var uncheckedLength = $('tbody input.skuItem:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuItem:checkbox(":checked")').length;
            if ( uncheckedLength < checkedLength  ) {
                groupSku.prop('checked',false);
            }

            if( !groupSku.prop('checked') ) {
                if( uncheckedLength < checkedLength ) {
                    $('form#mageForm input.SkuItem').remove();
                }
            }

            if( uncheckedLength == 0) {
                groupSku.prop('checked',true);
            }

            if( !$(this).prop('checked') ) {
                $("form#mageForm div.skuitem").remove();
            }
            if ( uncheckedLength < checkedLength  ) {
                if ( $(this).prop('checked') ) {
                    $("form#mageForm div.skuitem").remove();
                    $('form#mageForm button').append('<div class="skuitem"></div>');
                }
            }


            item.each(function(i) {
                var entityId = item.closest('td').siblings('td.eid').eq(i).text();
                var property = item.closest('td').siblings('td.prty').eq(i).text();
                var newValue = item.closest('td').siblings('td.newval').eq(i).text();
                var sku = item.closest('td').siblings('td.sku').eq(i).text();
                var position = item.closest('tr').index();

                var hiddenId = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuItem['+i+'][id]',
                    class: 'SkuItem',
                    value: entityId
                });
                var hiddenProperty = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuItem['+i+'][property]',
                    class: 'SkuItem',
                    value: property
                });
                var hiddenNewValue = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuItem['+i+'][newValue]',
                    class: 'SkuItem',
                    value: newValue
                });
                var hiddenSku = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuItem['+i+'][sku]',
                    class: 'SkuItem',
                    value: sku
                });
                hiddenId.appendTo('form#mageForm div.skuitem');
                hiddenProperty.appendTo('form#mageForm div.skuitem');
                hiddenNewValue.appendTo('form#mageForm div.skuitem');
                hiddenSku.appendTo('form#mageForm div.skuitem');
            });
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
                    "data": "categortyId"
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
            var checkboxLength = $('tbody input.skuCategory:checkbox:not(":checked")').length;
            if ( $(this).prop('checked') ) {
                if( checkboxLength == 0) {
                    groupCategory.prop('checked','checked');
                }
            } else {
                if( checkboxLength < 3 ) {
                    groupCategory.prop('checked',false);
                }
            }
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuCategory['+position+'][id]',
                class: 'SkuCategory',
                value: entityId
            });
            var hiddenSku = $('<input>').attr({
                type: 'hidden',
                name: 'skuCategory['+position+'][sku]',
                class: 'SkuCategory',
                value: sku
            });
            var hiddenCategoryId = $('<input>').attr({
                type: 'hidden',
                name: 'skuCategory['+position+'][categoryId]',
                class: 'SkuCategory',
                value: categoryId
            });

            var hiddenDataState = $('<input>').attr({
                type: 'hidden',
                name: 'skuCategory['+position+'][dataState]',
                class: 'SkuCategory',
                value: dataState
            });
            if( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenCategoryId.appendTo('form#mageForm');
                hiddenDataState.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
            }
            if( !$(this).is(':checked') ) {
                $("form#mageForm input[name='skuCategory["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][categoryId]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][dataState]']").remove();
                $("form#mageForm input[name='skuCategory["+ position +"][sku]']").remove();
            }
        });

        groupCategory.prop('checked',false);
        groupCategory.on('change',function(){
            $('form#mageForm button').append('<div class="skucategory"></div>');
            if( $(this).prop("checked") ) {
                $('.skuCategory').prop('checked',true);
            } else {
                $('form#mageForm input.SkuCategory').remove();
                $('.skuCategory').prop('checked',false);
            }
            var category = $('#kpiCategories tbody #skuCategory');
            var uncheckedLength = $('tbody input.skuCategory:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuCategory:checkbox(":checked")').length;

            if( groupCategory.prop('checked') ) {
                if( uncheckedLength < checkedLength ) {
                    $('form#mageForm input.SkuCategory').remove();
                }
            }

            if ( uncheckedLength < checkedLength  ) {
                groupCategory.prop('checked',false);
            }

            if( uncheckedLength == 0) {
                groupCategory.prop('checked',true);
            }

            if( !$(this).prop('checked') ) {
                $("form#mageForm div.skucategory").remove();
            }
            if ( uncheckedLength < checkedLength  ) {
                if ( $(this).prop('checked') ) {
                    $("form#mageForm div.skucategory").remove();
                    $('form#mageForm button').append('<div class="skucategory"></div>');
                }
            }

            category.each(function(i) {
                var entityId = category.closest('td').siblings('td.eid').eq(i).text();
                var categoryId = category.closest('td').siblings('td.catid').eq(i).text();
                var dataState = category.closest('td').siblings('td.dataState').eq(i).text();
                var sku = category.closest('td').siblings('td.sku').eq(i).text();
                var position = category.closest('tr').index();

                var hiddenId = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuCategory['+i+'][id]',
                    class: 'SkuCategory',
                    value: entityId
                });
                var hiddenCategoryId = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuCategory['+i+'][categoryId]',
                    class: 'SkuCategory',
                    value: categoryId
                });

                var hiddenDataState = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuCategory['+i+'][dataState]',
                    class: 'SkuCategory',
                    value: dataState
                });

                var hiddenSku = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuCategory['+i+'][sku]',
                    class: 'SkuCategory',
                    value: sku
                });

                hiddenId.appendTo('form#mageForm div.skucategory');
                hiddenCategoryId.appendTo('form#mageForm div.skucategory');
                hiddenDataState.appendTo('form#mageForm div.skucategory');
                hiddenSku.appendTo('form#mageForm div.skucategory');
            });
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
            var checkboxLength = $('tbody input.skuLink:checkbox:not(":checked")').length;
            if ( $(this).prop('checked') ) {
                if( checkboxLength == 0) {
                    groupLink.prop('checked','checked');
                }
            } else {
                if( checkboxLength < 3 ) {
                    groupLink.prop('checked',false);
                }
            }
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuLink['+position+'][id]',
                class: 'SkuLink',
                value: entityId
            });
            var hiddenSku = $('<input>').attr({
                type: 'hidden',
                name: 'skuLink['+position+'][sku]',
                class: 'SkuLink',
                value: sku
            });
            var hiddenLinkedId = $('<input>').attr({
                type: 'hidden',
                name: 'skuLink['+position+'][linkedId]',
                class: 'SkuLink',
                value: linkedId
            });

            var hiddenType = $('<input>').attr({
                type: 'hidden',
                name: 'skuLink['+position+'][type]',
                class: 'SkuLink',
                value: type
            });

            var hiddenDataState = $('<input>').attr({
                type: 'hidden',
                name: 'skuLink['+position+'][dataState]',
                class: 'SkuLink',
                value: dataState
            });
            if( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenSku.appendTo('form#mageForm');
                hiddenLinkedId.appendTo('form#mageForm');
                hiddenType.appendTo('form#mageForm');
                hiddenDataState.appendTo('form#mageForm');
            }
            if( !$(this).is(':checked') ) {
                $("form#mageForm input[name='skuLink["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][sku]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][linkedId]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][type]']").remove();
                $("form#mageForm input[name='skuLink["+ position +"][dataState]']").remove();
            }
        });

        groupLink.prop('checked',false);
        groupLink.on('change',function(){
            $('form#mageForm button').append('<div class="skulink"></div>');
            if( $(this).prop("checked") ) {
                $('.skuLink').prop('checked',true);
            } else {
                $('form#mageForm input.SkuLink').remove();
                $('.skuLink').prop('checked',false);
            }
            var link = $('#kpiRelatedProducts tbody #skuLink');
            var uncheckedLength = $('tbody input.skuLink:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuLink:checkbox(":checked")').length;

            if( groupLink.prop('checked') ) {
                if( uncheckedLength < checkedLength ) {
                    $('form#mageForm input.SkuLink').remove();
                }
            }

            if ( uncheckedLength < checkedLength  ) {
                groupLink.prop('checked',false);
            }


            if( uncheckedLength == 0) {
                groupLink.prop('checked',true);
            }

            if( !$(this).prop('checked') ) {
                $("form#mageForm div.skulink").remove();
            }

            if ( uncheckedLength < checkedLength  ) {
                if ( $(this).prop('checked') ) {
                    $("form#mageForm div.skulink").remove();
                    $('form#mageForm button').append('<div class="skulink"></div>');
                }
            }

            link.each(function(i) {
                var entityId = link.closest('td').siblings('td.eid').eq(i).text();
                var sku = link.closest('td').siblings('td.sku').eq(i).text();
                var linkedId = link.closest('td').siblings('td.linkedId').eq(i).text();
                var dataState = link.closest('td').siblings('td.dataState').eq(i).text();
                var type = link.closest('td').siblings('td.type').eq(i).text();
                var position = link.closest('tr').index();

                var hiddenId = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuLink['+i+'][id]',
                    class: 'SkuLink',
                    value: entityId
                });
                var hiddenSku = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuLink['+i+'][sku]',
                    class: 'SkuLink',
                    value: sku
                });
                var hiddenLinkedId = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuLink['+i+'][linkedId]',
                    class: 'SkuLink',
                    value: linkedId
                });

                var hiddenType = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuLink['+i+'][type]',
                    class: 'SkuLink',
                    value: type
                });

                var hiddenDataState = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuLink['+i+'][dataState]',
                    class: 'SkuLink',
                    value: dataState
                });

                hiddenId.appendTo('form#mageForm div.skulink');
                hiddenDataState.appendTo('form#mageForm div.skulink');
                hiddenSku.appendTo('form#mageForm div.skulink');
                hiddenLinkedId.appendTo('form#mageForm div.skulink');
                hiddenType.appendTo('form#mageForm div.skulink');
            });
        });
    };

    var newSkuImages = function () {
        var dTable = $('#kpiImages');
        var dtable = dTable.DataTable({
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
            var uncheckedLength = $('tbody input.skuImage:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuImage:checkbox(":checked")').length;
            if ( $(this).prop('checked') ) {
                if( uncheckedLength == 0) {
                    groupImage.prop('checked','checked');
                }
            } else {
                if( uncheckedLength < checkedLength ) {
                    groupImage.prop('checked',false);
                }
            }
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][imageid]',
                class: 'SkuImage',
                value: imageId
            });
            var hiddenEntityId = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][id]',
                class: 'SkuImage',
                value: entityId
            });
            var hiddenLabel = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][label]',
                class: 'SkuImage',
                value: label
            });

            var hiddenFilename = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][filename]',
                class: 'SkuImage',
                value: filename
            });
            var hiddenSku = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][sku]',
                class: 'SkuImage',
                value: sku
            });
            var hiddenPosition = $('<input>').attr({
                type: 'hidden',
                name: 'skuImage['+position+'][position]',
                class: 'SkuImage',
                value: imgPos
            });

            if( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageImages');
                hiddenEntityId.appendTo('form#mageImages');
                hiddenFilename.appendTo('form#mageImages');
                hiddenSku.appendTo('form#mageImages');
                hiddenPosition.appendTo('form#mageImages');
                hiddenLabel.appendTo('form#mageImages');
            }

            if( !$(this).is(':checked') ) {
                $("form#mageImages input[name='skuImage["+ position +"][imageid]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][id]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][filename]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][sku]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][position]']").remove();
                $("form#mageImages input[name='skuImage["+ position +"][label]']").remove();
            }
        });
        groupImage.prop('checked',false);
        groupImage.on('change',function(){
            $('form#mageImages button').append('<div class="skuimg"></div>');
            if( $(this).prop("checked") ) {
                $('.skuImage').prop('checked',true);
            } else {
                $('form#mageImages input.SkuImage').remove();
                $('.skuImage').prop('checked',false);
            }
            var uncheckedLength = $('tbody input.skuImage:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuImage:checkbox(":checked")').length;
            if ( uncheckedLength < checkedLength  ) {
                groupImage.prop('checked',false);
            }
            if( groupImage.prop('checked') ) {
                if( uncheckedLength < checkedLength ) {
                    $('form#mageImages input.SkuImage').remove();
                }
            }

            if( uncheckedLength == 0) {
                groupImage.prop('checked',true);
            }
            var image = $('#kpiImages tbody #skuImage');


            if( !$(this).prop('checked') ) {
                $("form#mageImages div.skuimg").remove();
            }

            if ( uncheckedLength < checkedLength  ) {
                if ( $(this).prop('checked') ) {
                    $('form#mageImages .SkuImage').remove();
                    $("form#mageImages div.skuimg").remove();
                    $('form#mageImages button').append('<div class="skuimg"></div>');
                }
            }

            image.each(function(i) {
                var imageId = image.closest('td').siblings('td.imageid').eq(i).text();
                var entityId = image.closest('td').siblings('td.eid').eq(i).text();
                var sku = image.closest('td').siblings('td.sku').eq(i).text();
                var label = image.closest('td').siblings('td.lbl').eq(i).text();
                var imgPos = image.closest('td').siblings('td.position').eq(i).text();
                var filename = image.closest('td').siblings('td.filename').eq(i).children('img').attr('src');

                var hiddenId = $('<input>').attr({
                    type: 'hidden',
                    class:  'SkuImage',
                    name: 'skuImage['+i+'][imageid]',
                    value: imageId
                });
                var hiddenEntityId = $('<input>').attr({
                    type: 'hidden',
                    class:  'SkuImage',
                    name: 'skuImage['+i+'][id]',
                    value: entityId
                });

                var hiddenFilename = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuImage['+i+'][filename]',
                    class:  'SkuImage',
                    value: filename
                });
                var hiddenLabel = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuImage['+i+'][label]',
                    class: 'SkuImg',
                    value: label
                });
                var hiddenSku = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuImage['+i+'][sku]',
                    class:  'SkuImage',
                    value: sku
                });

                var hiddenPosition = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuImage['+i+'][position]',
                    class:  'SkuImage',
                    value: imgPos
                });
                hiddenId.appendTo('form#mageImages div.skuimg');
                hiddenEntityId.appendTo('form#mageImages div.skuimg');
                hiddenFilename.appendTo('form#mageImages div.skuimg');
                hiddenSku.appendTo('form#mageImages div.skuimg');
                hiddenPosition.appendTo('form#mageImages div.skuimg');
                hiddenLabel.appendTo('form#mageImages div.skuimg');
            });
        });
    };

    var newProducts = function () {
        var dTable = $('#kpiNewProducts');
        var dtable = dTable.dataTable({
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
//        $('#kpiNewProducts tbody').on('click', 'td.details-control', function () {
//           var tr = $(this).closest('tr');
//            var row = dtable.api().row( tr );
//
//            if ( row.child.isShown() ) {
//                // This row is already open - close it
//                console.log('hoho');
//                row.child.hide();
//                tr.removeClass('shown');
//            }
//            else {
//                console.log('haha');
//                // Open this row
////                row.child( format(row.data()) ).show();
//                row.child().show();
//                tr.addClass('shown');
//            }
//        } );
        var groupNewSku = $('#skuNewProducts');

        $('#kpiNewProducts tbody').on('change', '#skuNewProduct',function (e) {
            e.preventDefault();
            var newProds = $(this);
            var id = newProds.closest('td').siblings('td.eid').text();
            var sku = newProds.closest('td').siblings('td.sku').text();
            var position = newProds.closest('tr').index();
//            var position = idChange.closest('td').siblings('td.count').text();
            var uncheckedLength = $('tbody input.skuNewProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuNewProduct:checkbox(":checked")').length;
            if ( uncheckedLength == 0 && !groupNewSku.prop('checked') ) {
                $("form#mageNewProds input.SkuNewProds").remove();
            }

            if ( $(this).prop('checked') ) {
                if( uncheckedLength == 0) {
                    groupNewSku.prop('checked','checked');
                }
            } else {
                if( uncheckedLength < checkedLength ) {
                    groupNewSku.prop('checked',false);
                }
            }
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuNewProduct['+position+'][id]',
                class: 'skuNewProduct',
                value: id
            });

            var hiddenSku = $('<input>').attr({
                type: 'hidden',
                name: 'skuNewProduct['+position+'][sku]',
                class: 'skuNewProduct',
                value: sku
            });

            if( $(this).prop('checked') ) {
//                $('form#mageImages button').append('<div class="skuimg"></div>');
                hiddenId.appendTo('form#mageNewProds');
                hiddenSku.appendTo('form#mageNewProds');
            }

            if( !$(this).is(':checked') ) {
                $("form#mageNewProds input[name='skuNewProduct["+ position +"][id]']").remove();
                $("form#mageNewProds input[name='skuNewProduct["+ position +"][sku]']").remove();
            }
        });
        groupNewSku.prop('checked',false);
        groupNewSku.on('change',function(){
//            console.log('haha');
            $('form#mageNewProds button').append('<div class="skunewprods"></div>');
            if( $(this).prop("checked") ) {
                $('.skuNewProduct').prop('checked',true);
            } else {
                $('form#mageNewProds input.SkuNewProds').remove();
                $('.skuNewProduct').prop('checked',false);
            }
            var uncheckedLength = $('tbody input.skuNewProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.skuNewProduct:checkbox(":checked")').length;
            if ( uncheckedLength < checkedLength  ) {
                groupNewSku.prop('checked',false);
            }
            if( !groupNewSku.prop('checked') ) {
                if( uncheckedLength == checkedLength ) {
                    $('form#mageNewProds input.skuNewProduct').remove();
                }
            }

            if( uncheckedLength == 0) {
                groupNewSku.prop('checked',true);
            }
            var newProds = $('#kpiNewProducts tbody #skuNewProduct');

            if( !$(this).prop('checked') ) {
                $("form#mageNewProds div.skunewprods").remove();
            }

            if ( $(this).prop('checked') ) {
                if ( uncheckedLength < checkedLength  ) {
                    $('form#mageNewProds .skuNewProduct').remove();
                    $("form#mageNewProds div.skunewprods").remove();
                    $('form#mageNewProds button').append('<div class="skunewprods"></div>');
                }
            }
            newProds.each(function(i) {
                var id = newProds.closest('td').siblings('td.eid').eq(i).text();
                var sku = newProds.closest('td').siblings('td.sku').eq(i).text();

                var hiddenId = $('<input>').attr({
                    type: 'hidden',
                    class:  'SkuNewProds',
                    name: 'skuNewProduct['+i+'][id]',
                    value: id
                });

                var hiddenSku = $('<input>').attr({
                    type: 'hidden',
                    name: 'skuNewProduct['+i+'][sku]',
                    class:  'SkuNewProds',
                    value: sku
                });

                hiddenId.appendTo('form#mageNewProds div.skunewprods');
                hiddenSku.appendTo('form#mageNewProds div.skunewprods');
            });
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

    var categoryProducts = function () {

        var table = $('#manageCats');

        // begin first table
        table.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/content/manage-categories",
                "type": 'POST',
                "data": function (d){
//                    d.related = $("#accessoriesForm input[name*='linkedSku]']").serializeArray();
                    d.category = $("#categoryProductsForm input[name='category']").serializeArray();
                }
            },

            "columns": [
                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='category_product'>"+
                        "<label for='categoryProduct'></label>"+
                        "<input type='checkbox' class='categoryProduct' name='categoryProduct[][sku]' value=''/></td>"
                },
                {
                    "class": "sku",
                    "data": "sku"
                },
                { "data": "value" },
//                { "data": "imagename" },
                { "data": "manufacturer" }
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
        var groupCatProducts = $('#categoryProds');

        if ( !table.find('input.categoryProduct').prop('checked') ) {
            $('.category_remove, .category_move').attr('disabled',true);
        }

        $('.category_remove').on('click',function(e){
            e.preventDefault();
            var data = $("#removeCatsForm").serializeArray();
            $.post('/content/manage-categories/remove',data,function(result){
                console.log(result);
//                toastr.success(result);
                table.api().draw();
            });

//            var data = $("#categoryProductsForm input[name='sku']").serializeArray();
//            console.log(data);
//            console.log('clicked');
//            $('#manageCats tbody tr').each(function(i,e){
//                var input = $(this).find('td input.categoryProduct');
////                console.log(typeof input);
//                console.log(input[i]);
////                console.log(i);
////                console.log(input[i].checked);
//
////                if ( input[i].checked == true ) {
////                    console.log('input checked');
////                    var sku = input.closest('td').siblings('td.sku').eq(i).text();
////                    console.log(sku);
////                }
//            });
//
//            var checkedLength = $('tbody input.categoryProduct:checkbox(":checked")').length;
////            console.log(checkedLength);
//            for (var i = 0; i < checkedLength; i++ ) {
//                var sku = $('.categoryProduct').closest('td').siblings('td.sku').eq(i).text();
////            console.log(sku);
//            }
//
////            if( $('.categoryProduct').prop('checked') ) {
////                var sku = $('.categoryProduct').closest('td').siblings('td.sku').text();
////                console.log('checked');
////                console.log(sku);
////            }
        });


        $('#manageCats tbody').on('change', '.categoryProduct',function (e) {
            var uncheckedLength = $('tbody input.categoryProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.categoryProduct:checkbox(":checked")').length;
            var manageCategory = $(this);
            var sku = manageCategory.closest('td').siblings('td.sku').text();
            var catId = $('input[name=category]').val();
            var position = manageCategory.closest('tr').index();

            var hiddenSku = $('<input>').attr({'type':'hidden','name':"manageCategory[" + position + "][sku]", 'class':'ManageCategory', 'value':sku});
            var hiddenCatId = $('<input>').attr({'type':'hidden','name':"manageCategory[" + position + "][cat_id]", 'class':'ManageCategory', 'value':catId});

            if( manageCategory.prop('checked') ) {
                manageCategory.attr('checked',true);
                $('.category_remove, .category_move').removeAttr('disabled');
                hiddenSku.appendTo('form#removeCatsForm');
                hiddenCatId.appendTo('form#removeCatsForm');
            }
            if( !$(this).prop('checked') ) {
                $(this).attr('checked',false);
                $("form#removeCatsForm input[name='manageCategory["+ position +"][sku]']").remove();
                $("form#removeCatsForm input[name='manageCategory["+ position +"][cat_id]']").remove();
            }
//            console.log(uncheckedLength , checkedLength);
            if( uncheckedLength < checkedLength ) {
                groupCatProducts.attr('checked',false);
            }
            if ( uncheckedLength == 0 ) {
                groupCatProducts.attr('checked',true);
            }
            if ( uncheckedLength == checkedLength ) {
                $('.category_remove, .category_move').attr('disabled', true);
            }
        });

        groupCatProducts.on('change',function(){
            var manageCategory = $('#manageCats tbody .categoryProduct');
            if( !$(this).prop('checked') ) {
                $("form#removeCatsForm input.ManageCategory").remove();
            }
            /*Cache cat id for products*/
            var catId = $('input[name=category]').val();

            if ( groupCatProducts.prop('checked') ) {
                $("form#removeCatsForm input.ManageCategory").remove();
                manageCategory.each(function(i) {
                    /*Cache sku for given row*/
                    var sku = manageCategory.closest('td')
                        .siblings('td.sku')
                        .eq(i)
                        .text();
                    var hiddenSku = $('<input>').attr({type: 'hidden',name: 'manageCategory['+i+'][sku]',class:  'ManageCategory',value: sku});
                    var hiddenCatId = $('<input>').attr({type: 'hidden',name: 'manageCategory['+i+'][cat_id]',class:  'ManageCategory',value: catId});
                    hiddenSku.appendTo('form#removeCatsForm');
                    hiddenCatId.appendTo('form#removeCatsForm');
                });
            }
            $('.categoryProduct').prop('checked',true);
            if( $(this).prop('checked') ) {
                $('.category_remove, .category_move').removeAttr('disabled');
            }
            if ( !$(this).prop('checked') ) {
                $('.categoryProduct').removeAttr('checked');
                $('.category_remove, .category_move').attr('disabled', true);
            }
        });
    };

    var categoryAddProducts = function () {
//        console.log($('#addProductsForm_wrapper').hasClass('dataTables_wrapper'));

//        if( $('#addProductsForm_wrapper').hasClass('dataTables_wrapper') ) {
//        var row = $('#catProductModalAdd').find('div.row');
//            $('#addProductsForm_wrapper').addClass('addProducts_wrapper');
//        }
        var addedTable = $('#addedProductsForm');
        var addTable = $('#addProductsForm');
        var selected = [];
        var TableAdded = addedTable.dataTable({
//            "processing": true,
//            "serverSide": true,
//            "ajax": {
//                url: "/content/manage-categories/search",
//                type: 'POST'
//            },
            "columns": [
//                {
//                    "orderable":    false,
//                    "data": null,
//                    "defaultContent":   "<td class='add_category_product'>"+
//                        "<label for='addCategoryProduct'></label>"+
//                        "<input type='checkbox' class='addCategoryProduct' name='addCategoryProduct[][sku]' value=''/></td>"
//                },
//                {
//                    "class": "hidden",
//                    "data": "position"
//                },
                {
//                    "class": "id",
                    "data": "id"
                },
                {
//                    "class": "sku",
                    "data": "sku"
                },
                {
//                    "class": "value",
                    "data": "value"
                },
                {
//                    "class": "manufacturer",
                    "data": "manufacturer"
                },
                {
                    "class":"remove",
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td><a href='#'>Remove</a></td>"
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

        var aTable = addTable.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/content/manage-categories/search",
                type: 'POST'
            },
//            "rowCallback": function( row, data ) {
////                console.log(data);
////                console.log(row);
//                if ( $.inArray(data.id, selected) !== -1 ) {
//                    $(row).addClass('selected');
//                }
//            },
            "columns": [
                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td class='add_category_product'>"+
                        "<label for='addCategoryProduct'></label>"+
                        "<input type='checkbox' class='addCategoryProduct' name='addCategoryProduct[][sku]' value=''/></td>"
                },
                {
                    "class": "id",
                    "data": "id"
                },
                {
                    "class": "sku",
                    "data": "sku"
                },
                {
                    "class": "value",
                    "data": "value"
                },
                {
                    "class": "manufacturer",
                    "data": "manufacturer"
                },
                {
                    "class":"remove hidden",
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td><a href='#'>Remove</a></td>"
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
        var addAllProducts = $('#addAllProducts');
        var i;
        var row = [];

//        if ( $('form#addProductsSubmitForm').has('input') ) {
//            $("form#addProductsSubmitForm input[type='hidden']").each(function(i,e){
//                console.log(e);
//            });
//        }


//        $('#addProductsForm tbody').on('click', 'tr', function () {
//            var id = $(this).find('td.id').text();
//            console.log(id);
//            var index = $.inArray(id, selected);
//            console.log(index);

//            if ( index === -1 ) {
//                selected.push( id );
//            } else {
//                selected.splice( index, 1 );
//            }

//            $(this).toggleClass('selected');
//            $('.odd.selected').css({'background-color':'#b0bed9 !important'});
//            $('.even.selected').css({'background-color':'#b0bed9 !important'});

//        } );
//        var addProduct;
        var index = 0;
        $('#addedProductsForm tbody tr:first').remove();

        $('#addProductsForm tbody').on('change', '.addCategoryProduct',function (e) {
            var uncheckedLength = $('tbody input.addCategoryProduct:checkbox:not(":checked")').length;
            var checkedLength = $('tbody input.addCategoryProduct:checkbox(":checked")').length;
            var addProduct = $(this);
            var id = addProduct.closest('td').siblings('td.id').text();
            var sku = addProduct.closest('td').siblings('td.sku').text();
            var img = addProduct.closest('td').siblings('td.value').find('img').prop('src');
            var name = addProduct.closest('td').siblings('td.value').text();
            var manufacturer = addProduct.closest('td').siblings('td.manufacturer').text();
//            var position = addProduct.closest('tr').index();

            var hiddenId = $('<input>').attr({'type':'hidden','name':"manageProduct[" + index + "][id]", 'class':'ManageProduct', 'value':id});
            var hiddenSku = $('<input>').attr({'type':'hidden','name':"manageProduct[" + index + "][sku]", 'class':'ManageProduct', 'value':sku});
            var hiddenImage = $('<input>').attr({'type':'hidden','name':"manageProduct[" + index + "][img]", 'class':'ManageProduct', 'value':img});
            var hiddenName = $('<input>').attr({'type':'hidden','name':"manageProduct[" + index + "][name]", 'class':'ManageProduct', 'value':name});
            var hiddenManufacturer = $('<input>').attr({'type':'hidden','name':"manageProduct[" + index + "][manufacturer]", 'class':'ManageProduct', 'value':manufacturer});



//            console.log(checkedLength, uncheckedLength);

//            for( i = 0; i < checkedLength-uncheckedLength; i++ ) {
//                row[i] = "<tr><td>"+id+"</td>" +
//                        "<td>"+sku+"</td>" +
//                        "<td><img width='100' height='100' src='"+img+"'/> <br />"+name+"</td>" +
//                        "<td>"+manufacturer+"</td></tr>";
//            }
//            console.log(row);
//            if ( checkedLength > 1) {
//                var newRow = row.join();
//            }
//            console.log(newRow);

//            console.log(id, sku, img, name, manufacturer);
//            var catId = $('input[name=addCategoryProduct]').val();
//            var row = '';
//            row += "<tr><td>"+position+"</td><td>"+id+"</td>" +
//                        "<td>"+sku+"</td>" +
//                        "<td><img width='100' height='100' src='"+img+"'/> <br />"+name+"</td>" +
//                        "<td>"+manufacturer+"</td>" +
//                    "</tr>";

//            console.log(row);

//            var hiddenSku = $('<input>').attr({'type':'hidden','name':"manageCategory[" + position + "][sku]", 'class':'ManageCategory', 'value':sku});
//            var hiddenCatId = $('<input>').attr({'type':'hidden','name':"manageCategory[" + position + "][cat_id]", 'class':'ManageCategory', 'value':catId});

            if( addProduct.prop('checked') ) {
                addProduct.closest('td').addClass('checker');
                addProduct.closest('tr').attr('id','new_row'+index);
                var row = addProduct.closest('tr').clone(true);//.find('td').first().remove();


//                    "class":"remove",
//                "orderable":    false,
//                    "data": null,
//                    "defaultContent":   "<td><a href='#'>Remove</a></td>""
//                $('#myHtml').clone(true).find('#elementThatYouWantToRemove').remove().end().html();
//                console.log(row);
                row.appendTo('#addedProductsForm tbody');
//                TableAdded.api().draw();
//                row = row.find('td').first().remove();
//                $('#addedProductsForm tbody tr').first().remove();
//                $('#addedProductsForm tbody').append('<tr>'+row+'</tr>');
//                $('#addedProductsForm tbody').find('tr').attr('id','new_row'+index);
//                $('#addedProductsForm tbody tr').find('td input[type="checkbox"]').remove();
                $('#addedProductsForm tbody tr').find('td.checker').remove();
                $('#addedProductsForm tbody tr').find('td.remove').removeClass('hidden');
//                $('#addedProductsForm tbody tr').after('<td class="remove"><a href="#">Remove</a></td>');
//                oTableDest.$('tr.selected').removeClass('row_selected');
//                $(this).addClass('row_selected');
                addProduct.closest('tr').removeClass('row_selected');
                addProduct.closest('tr').addClass('row_selected');
                hiddenId.appendTo('form#addProductsSubmitForm');
                hiddenSku.appendTo('form#addProductsSubmitForm');
                hiddenImage.appendTo('form#addProductsSubmitForm');
                hiddenName.appendTo('form#addProductsSubmitForm');
                hiddenManufacturer.appendTo('form#addProductsSubmitForm');
                index++;
//                console.log(aTable.api().row('.row_selected').data());

//                TableAdded.row.add([aTable.api().row('.row_selected').data()]).draw();
//                $('#addedProductsForm tbody tr').after('<tr></tr>');
//                TableAdded.api().row.add("<tr></tr>").draw();
//                TableAdded.row.add(["<tr><td>"+id+"</td>" +
//                                   "<td>"+sku+"</td>" +
//                                   "<td><img width='100' height='100' src='"+img+"'/> <br />"+name+"</td>" +
//                                   "<td>"+manufacturer+"</td></tr>"]).draw();
//                TableAdded.api().$('tbody').empty().append(row);
//                TableAdded.api().draw();
//                TableAdded.$('tr').addClass('row_selected');


                addProduct.prop('checked',true);
                $('.category_remove, .category_move').removeAttr('disabled');
//                hiddenSku.appendTo('form#removeCatsForm');
//                hiddenCatId.appendTo('form#removeCatsForm');
            }
            if( !addProduct.prop('checked') ) {
                index--;
//                console.log('tr#new_row'+index);
                $('#addedProductsForm tr#new_row'+index).remove();
                addProduct.closest('td').removeClass('checker');
                addProduct.attr('checked',false);
                addProduct.closest('tr').removeClass('row_selected');
                $("form#addProductsSubmitForm input[name='manageProduct["+ index +"][id]']").remove();
                $("form#addProductsSubmitForm input[name='manageProduct["+ index +"][sku]']").remove();
                $("form#addProductsSubmitForm input[name='manageProduct["+ index +"][img]']").remove();
                $("form#addProductsSubmitForm input[name='manageProduct["+ index +"][name]']").remove();
                $("form#addProductsSubmitForm input[name='manageProduct["+ index +"][manufacturer]']").remove();

//                TableAdded.api().row('.row_selected').remove().draw(false);

//                $("form#removeCatsForm input[name='manageCategory["+ position +"][sku]']").remove();
//                $("form#removeCatsForm input[name='manageCategory["+ position +"][cat_id]']").remove();
            }
//            console.log(uncheckedLength , checkedLength);
            if( uncheckedLength < checkedLength ) {
                addAllProducts.attr('checked',false);
            }
            if ( uncheckedLength == 0 ) {
                addAllProducts.attr('checked',true);
            }
            if ( uncheckedLength == checkedLength ) {
//                $('.category_remove, .category_move').attr('disabled', true);
            }
        });

//        $('#addedProductsForm tbody').on('click', 'tr',function (e) {
//            e.preventDefault();
//            $(this).closest('tr').remove();
//            TableAdded.api().row('.row_selected').remove().draw(false);
//        });

        $('#addedProductsForm tbody').on('click', '.remove',function (e) {
            e.preventDefault();
            var addProduct = $('#addProductsForm tbody .addCategoryProduct');
//            var id = $(this).closest('td').siblings('td:first').text();
            var id = $(this).closest('tr').attr('id');
            addProduct.each(function(i) {
                var rowID = addProduct.closest('tr')
                    .eq(i)
                    .attr('id');
                if (rowID == id ) {
                    $('#'+id).find('input[type="checkbox"]').attr('checked',false);
                }
            });

//            $(aTable,'tr').each(function(i){
//
//            });
//            console.log(aTable.find('td.id').text());
            $(this).closest('tr').remove();
//            TableAdded.api().row('.row_selected').remove().draw(false);
        });

        //populate edit popup
//        $('#addProductsForm tbody').on('click', 'tr', function(){
//            var manufacturer = $(this).closest('tr').find('td').eq(0).text();
//            var site = $(this).closest('tr').find('td').eq(1).text();
//
//        });
    };

    return {

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
            categoryProducts();
            categoryAddProducts();
        }
    };

}();
