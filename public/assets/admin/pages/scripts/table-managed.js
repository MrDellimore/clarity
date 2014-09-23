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
                    "class":"old_value",
                    "data": "oldValue"
                },
                {
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

//        alert( 'Data source: '+ table.api().ajax.url() );
//        table.columns[0].attr('class','entityId');
//        table.columns[4].attr('class','manId');
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

    var updateMageItems = function () {
        var dTable = $('#kpiUpdates');
        var dtable = dTable.DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "/api-feeds",
                type: 'POST'
//                "data": function (d){
//                    d.related = $("#crossSellForm input[name*='linkedSku]']").serializeArray();
//                    d.position = $("#crossSellForm input[name*='position]']").serializeArray();
//                }
            },
            "columns": [

                {
                    "orderable":    false,
                    "data": null,
                    "defaultContent":   "<td id='sku_item'>"+
                                            "<label for='skuItem'></label>"+
                                                "<input type='checkbox' id='skuItem' name='skuItem[]' value=''/></td>"
                },
                {
                    "class": "hidden count",
                    "data": "count"
                },
                {
                    "class": "eid",
                    "data": "id"
                },
                { "data": "item"},
                {
                    "class": "hidden prty",
                    "data": "oproperty"
                },
                {
                    "data": "property"
                },
                { "data": "newValue"},
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
//        var entityId = $('#kpiUpdates td.eid');
//        console.log(entityId);
//        console.log(dTable);
//        console.log(dtable);

//        if( dTable.hasClass('eid') ) {
////            console.log('yes');
//        } else {
////            console.log('no');
//        }

        $('#kpiUpdates tbody').on('change', '#skuItem',function (e) {
            e.preventDefault();
            var idChange = $(this);
            var entityId = idChange.closest('td').siblings('td.eid').text();
//            var position = idChange.closest('td').length;
//            var position = idChange.closest('td').siblings('.eid').index('td');
            var position = idChange.closest('td').siblings('td.count').text();
            var property = idChange.closest('td').siblings('td.prty').text();
            $(this).val(entityId).attr('name','skuItem['+position+']');
            var hiddenId = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][id]',//$(this).attr('name'),
                value: entityId
            });
            var hiddenProperty = $('<input>').attr({
                type: 'hidden',
                name: 'skuItem['+position+'][property]',//$(this).attr('name'),
                value: property
            });
            if( $(this).prop('checked') ) {
                hiddenId.appendTo('form#mageForm');
                hiddenProperty.appendTo('form#mageForm');
            }
            if( !$(this).is(':checked') ) {
                $("form#mageForm input[name='skuItem["+ position +"][id]']").remove();
                $("form#mageForm input[name='skuItem["+ position +"][property]']").remove();
            }
        });
        var groupSku = $('#skuItems');

        groupSku.on('change',function(){
            var item = $('td input#skuItem');
//            console.log(item.length);
            if( $(this).prop("checked") ) {
                item.each(function(i){
                    var inputId = $('<input>').attr({
                        type: 'hidden',
                        name: 'skuItem['+i+'][id]',
                        value: $("input[name='skuItem["+i+"][id]'").val()
                    });
                    var inputProperty = $('<input>').attr({
                        type: 'hidden',
                        name:'skuItem['+i+'][property]',
                        value: $("input[name='skuItem["+i+"][property]'").val()
                    });
                    $(this).prop('checked','checked');
                    inputId.appendTo('form#mageForm');
                    inputProperty.appendTo('form#mageForm');
                });
            } else {
                item.each(function(i){
//                    var inputId = $('<input>').attr({
//                        type: 'hidden',
//                        name:'skuItem['+i+'][id]',
//                        value: $("input[name='skuItem["+i+"][id]'").val()
//                    });
//                    var inputProperty = $('<input>').attr({
//                        type: 'hidden',
//                        name:'skuItem['+i+'][property]',
//                        value: $("input[name='skuItem["+i+"][property]'").val()
//                    });
                    $(this).prop('checked', '');
                    $('form#mageForm input').remove();
                });
            }
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
        }

    };

}();