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
    }
    var populateSkuHistory = function () {
        var table = $('#skuHistoryDisplay');

        var filterDateRange;

        $('#filterDateRange .applyBtn').on("click", function(){
            console.log('haha',filterDateRange);
            filterDateRange = $('#filterDateRange').val();
        });
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
                url: "/sku-history",
                type: 'POST',
                data: function (d){
                    d.filterDateRange = filterDateRange;
                }
//                $('.applyBtn').on("click", function() {
//                        return $('#filterDateRange').val();
//                        return $('#filterDateRange').val();
//                    })
//                    d.myKey = "myValue";
//                    d.custom = $('#myInput').val();
//                    etc
//                }

//                {
//                    "filterDateRange" : filterDateRange
//                    "filterDateRange" : $('#filterDateRange').val()
//                }
                    // d.custom = $('#myInput').val();
                    // etc

            },

            "columns": [
                { "data": "entityID" },
                { "data": "oldValue" },
                { "data": "newValue" },
                { "data": "user" },
                { "data": "dataChanged" },
                { "data": "property" },
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

        table.on('click', '.revert', function (e) {
            e.preventDefault();
            console.log('haha');
        });

//        table.on('click', 'tbody tr .checkboxes', function () {
//            $(this).parents('tr').toggleClass("active");
//        });

//        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    }

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