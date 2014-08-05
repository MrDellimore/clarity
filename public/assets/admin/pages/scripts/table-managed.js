var TableManaged = function () {

//    var accessoryCall = function (){
//        var table = $('input[type=search]');
//        table.on('keyup',function(){
//            var searchValue = $(this).val();
//            var limit = $("select[name=acessoriesDisplay_length]").val();
//            var params = {
//                'searchValue' : searchValue,
//                'limit' :   limit
//            };
//            $.post('/form/accessories', params, function(data){
//                $.each(data, function(key, value) {
//                    if($('tr').hasClass('odd')){
//                        $('tr').addClass('add-accessories');
//                    }
//                    var entityID = value.entityID,
//                        sku = value.Sku,
//                        title = value.title,
//                        price = value.price,
//                        qty = value.inventory;
//                    console.log(data);
//                    var tableData = '<tr><td>'+entityID+'</td><td>'+sku+'</td><td>'+title+'</td><td>'+price+'</td><td>'+qty+'</td></tr>';
//                    $('.add-accessories').after(tableData);
//                });
//            }, "json");
//        });
//    }

    var initTable1 = function () {

        var table = $('#sample_1');

        // begin first table
        table.dataTable({


            "processing": true,
            "serverSide": true,

            "ajax": {
                url: "/search/quicksearch",
                type: 'POST'
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

    var initAccessoryDisplay = function () {

        var table = $('#accessoriesDisplay');

        var oTable = table.dataTable({

            "processing": true,
            "serverSide": true,

            "ajax": {
                url: "/form/accessories",
                type: 'POST'
            },
            "columns": [
                { "data": "id" },
                { "data": "sku" },
                { "data": "title" },
                { "data": "price" },
                { "data": "quantity" },
                { "data": "category" }
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
            }});

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            oTable.fnDeleteRow(nRow);
        });

        //var tableWrapper = jQuery('#sample_1_wrapper');
        //tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };

    var initCrossSellDisplay = function () {

        var table = $('#crossSellDisplay');

        var oTable = table.dataTable({

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
            }});

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            oTable.fnDeleteRow(nRow);
        });

        //var tableWrapper = jQuery('#sample_1_wrapper');
        //tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };

    return {

        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            initTable1();
            initAccessoryDisplay();
            initCrossSellDisplay();
//            accessoryCall();

        }

    };

}();