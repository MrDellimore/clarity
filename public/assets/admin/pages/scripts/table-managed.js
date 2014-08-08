var TableManaged = function () {

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
    var populateSkuHistory = function () {
        var table = $('#skuHistoryDisplay');

        var filterDateRange;


//        console.log('haha','hoho', filterDateRange);
//        $('#filterDateRange').on('focusout',function(){
//            var that = $(this);
//            $('.applyBtn').on("click", function(){
//                filterDateRange = $('#filterDateRange').val();
//            });
//            $('#btnDateRange').click(function(){
//                filterDateRange = that.val();
//                console.log('haha',filterDateRange);
//            });
//            console.log(filterDateRange);
//        });

        table.dataTable({

            "processing": true,
            "serverSide": true,

            "ajax": {
                "url": "/sku-history",
                "type": 'POST',
                "data": function (d){
                    d.filterDateRange =  $('#filterDateRange').val()
                }
            },

            "columns": [
                { "data": "entityID" },
                { "data": "oldValue" },
                { "data": "newValue" },
                { "data": "manufacturer" },
                { "data": "user" },
                { "data": "dataChanged" },
                { "data": "property" },
//                { "data": "user" },
                {
                    "class":    "revert",
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
//        }).columnFilter({
//            sPlaceHolder: "head:before",
//            aoColumns: [ 	{ type: "text" },
//                { type: "date-range" }
//            ]
//
//        });
//            table.ajax.reload({
//                "url": "/sku-history",
//                "type": 'POST'
//            });
        table.on('click.dt', '.revert', function (e) {
            e.preventDefault();
            var oldValue = $('tr > td:eq(1)').text();
            var newValue = $('tr > td:eq(2)').text();
//            $.post('/sku-history/revert',{'old':oldValue, 'new': newValue}, function(data){
            table.dataTable({
                "processing": true,
                "serverSide": true,

                "ajax": {
                    "url": "/sku-history/revert",
                    "type": 'POST',
                    "data": function (d){
                        d.oldValue = oldValue;
                        d.newValue = newValue;
                    }
                },

                "columns": [
                    { "data": "entityID" },
                    { "data": "oldValue" },
                    { "data": "newValue" },
                    { "data": "manufacturer" },
                    { "data": "user" },
                    { "data": "dataChanged" },
                    { "data": "property" },
//                { "data": "user" },
                    {
                        "class":    "revert",
                        "orderable":    false,
                        "data": null,
                        "defaultContent":   "<td><a href='#'>Revert</a></td>"
                    }

                ]
            });
//        });
//            });
//            /sku-history/revert

//            console.log('haha');
        });
    };

    var initAcessoryDisplay = function () {

        var table = $('#acessoriesDisplay');

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
    }

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
            initAcessoryDisplay();
            initCrossSellDisplay();
            populateSkuHistory();
        }

    };

}();