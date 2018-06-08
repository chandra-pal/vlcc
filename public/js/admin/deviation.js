siteObjJs.admin.deviationJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        var date = $("#newDate").val();
        var schedule_type = $("#schedule_type").val();
        var diteticianId = $("#diteticianId").val();
        if ('' != date && '' != schedule_type && '' != diteticianId) {
            handleTable('deviation-table', date, schedule_type, diteticianId);
        } else {
            handleListTable('deviation-list-table', date, schedule_type, diteticianId);
        }
    };
    var handleListTable = function (tableId, date, schedule_type, diteticianId) {
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#deviation-list-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'devitionId', name: 'devitionId', visible: false},
                    {data: 'centerName', name: 'centerName'},
                    {data: 'firstName', name: 'firstName'},
                    {data: 'scheduleName', name: 'scheduleName'},
                    {data: 'calories_recommended', name: 'calories_recommended'},
                    {data: 'calories_consumed', name: 'calories_consumed'},
                    {data: null, name: 'difference'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    var displayLength = settings._iDisplayLength;
                    api.column(0, {page: 'current'}).data().each(function (group, i) {
                        recNum = ((page * displayLength) + i + 1);
                        $(rows).eq(i).children('td:first-child').html(recNum);
                    });

                    api.column(7, {page: 'current'}).data().each(function (group, i) {
                        var difference = parseInt(group.calories_consumed - group.calories_recommended);
                        $(rows).eq(i).children('td:nth-child(7)').html(difference);

                    });
                },
                "ajax": {
                    "url": "member-diet-deviation/getListData",
                    "type": "POST",
                    "data": {date: date, schedule_type: schedule_type, diteticianId: diteticianId},
                    "dataType": "json",
                },
                "order": [
                    [2, "asc"],
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
    };
    var handleTable = function (tableId, date, schedule_type, diteticianId) {
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#deviation-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'devitionId', name: 'devitionId', visible: false},
                    {data: 'firstName', name: 'firstName'},
                    {data: 'scheduleName', name: 'scheduleName'},
                    {data: 'calories_recommended', name: 'calories_recommended'},
                    {data: 'calories_consumed', name: 'calories_consumed'},
                    {data: null, name: 'difference'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    var displayLength = settings._iDisplayLength;
                    api.column(0, {page: 'current'}).data().each(function (group, i) {
                        recNum = ((page * displayLength) + i + 1);
                        $(rows).eq(i).children('td:first-child').html(recNum);
                    });

                    api.column(6, {page: 'current'}).data().each(function (group, i) {
                        var difference = parseInt(group.calories_consumed - group.calories_recommended);
                        $(rows).eq(i).children('td:nth-child(6)').html(difference);

                    });
                },
                "ajax": {
                    "url": "data",
                    "type": "POST",
                    "data": {date: date, schedule_type: schedule_type, diteticianId: diteticianId},
                    "dataType": "json",
                },
                "order": [
                    [2, "asc"],
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
    };
    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
        }

    };
}();