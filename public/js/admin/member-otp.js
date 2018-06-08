siteObjJs.admin.memberOtpJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        var selectedVal = $('#customer_select').val();
        if (selectedVal != 0) {
            handleTable('member-otp-table', selectedVal);
        } else {
            initalizeTable();
        }

        $('body').on('change', '#customer_select', function (e) {

            if ($(this).val() != 0)
            {
                handleTable('member-otp-table', $(this).val());
            } else
            {
                $('#member-otp-table-body').empty("");
            }
        });

        $('body').on('change', '#center_select', function (e) {
            var centerId = $(this).val();
            if (centerId != "") {
                var actionUrl = 'members/centerwise-members';
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {center_id: centerId},
                    success: function (data)
                    {
                        $(".members_list").html("");
                        $(".members_list").html(data.members_list);
                        $("#customer_select").select2({
                            allowClear: true,
                            placeholder: $(this).attr('data-label-text'),
                            width: null
                        });
                        var customer_id = $('#customer_select').val();
                        if (customer_id != "" && customer_id != 0) {
                            handleTable('member-otp-table', customer_id);
                        }
                    },
                    error: function (jqXhr, json, errorThrown)
                    {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                });
            }
        });

    };

    var handleTable = function (tableId, customerId) {
        $('#' + tableId).dataTable().fnDestroy();

        grid = new Datatable();
        grid.init({
            src: $('#member-otp-table'),
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
                    {data: 'id', name: 'id', visible: false},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'otp', name: 'otp'},
                    {data: 'sms_delivered', name: 'sms_delivered', visible: false},
                    {data: 'otp_used', name: 'otp_used'},
                    {data: 'attempt_count', name: 'attempt_count'},
                    {data: 'created_at', name: 'created_at'},
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
                },
                "ajax": {
                    "url": "member-otp/data/" + customerId,
                    "type": "GET"
                },
                "order": [
                    [7, "desc"],
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
    };

    var initalizeTable = function () {
        $('#member-otp-table').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "bPaginate": false,
            "bSort": false,
            "emptyTable": "No data available in table"
        });
    };
    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            //initalizeTable();
        }

    };
}();