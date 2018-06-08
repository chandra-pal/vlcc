siteObjJs.admin.loginLogs = function () {
    var deleteConfirmMessage;
    var handleTable = function () {
        var grid = new Datatable();
        grid.init({
            src: $('#grid-table'),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
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
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'username', name: 'username'},
                    {data: 'ip_address', name: 'ip_address'},
                    {data: 'in_time', name: 'in_time'},
                    {data: 'last_access_time', name: 'last_access_time', orderable: false},
                    {data: 'logout_time', name: 'logout_time', visible: false},
                    {data: 'out_time', name: 'out_time'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    api.column(5, {page: 'current'}).data().each(function (group, i) {
                        $(rows).eq(i).children('td:nth-child(6)').html(group);
                    });
                },
                "ajax": {
                    "url": "login-logs/data",
                    "type": "GET"
                },
                "order": [
                    [3, "desc"]
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            var actionType = action.attr('data-actionType');
            var actionField = action.attr('data-actionField');
            var actionValue = action.val();
            var actionName = action.attr('data-action');

            if (action.val() !== "" && grid.getSelectedRowsCount() > 0) {

                var formdata = {
                    action: actionName,
                    actionField: actionField,
                    actionType: actionType,
                    actionValue: actionValue,
                    ids: grid.getSelectedRows()
                };

                handleGroupAction(grid, formdata);

            } else if (action.val() === "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record (s) selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        var table = grid.getTable();
        var oTable = table.dataTable();

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            bootbox.confirm(siteObjJs.admin.loginLogs.deleteConfirmMessage, function (result) {
                if (result === false) {
                    return;
                }

                var nRow = $(this).parents('tr')[0];

                var formdata = {
                    action: 'delete',
                    actionField: 'id',
                    actionType: 'group',
                    actionValue: userId,
                    ids: userId
                };
                handleGroupAction(grid, formdata);
            });
        });

        table.on('click', '.view', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');

            var nTr = $(this).parents('tr')[0];
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }

        });

        table.on('click', '.edit', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            var redirectUrl = 'linkcategory/' + userId + '/edit';
            window.location.href = redirectUrl;
        });

        function handleGroupAction(grid, data) {

            var token = $('meta[name="csrf-token"]').attr('content');
            var form = new FormData();
            form.append("action", data.action);
            form.append("actionType", data.actionType);
            form.append("field", data.actionField);
            form.append("value", data.actionValue);
            form.append("ids", data.ids);
            form.append("_token", token);
            var actionUrl = 'login-logs/group-action';

            jQuery.ajax({
                url: actionUrl,
                cache: false,
                data: form,
                dataType: "json",
                type: "POST",
                processData: false,
                contentType: false,
                success: function (data)
                {
                    grid.getDataTable().ajax.reload();
                    if (data.status === 'success') {
                        Metronic.alert({
                            type: 'success',
                            icon: 'success',
                            message: data.message,
                            container: $('#errorMessage'),
                            place: 'prepend'
                        });

                    }
                    else if (data.status === 'fail') {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: data.message,
                            container: grid.getTableWrapper(),
                            place: 'prepend'
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }
        $('#grid-table').on('click', '.filter-cancel', function (e) {
            $('#access_time_from').val('12:01 AM');
            $('#access_time_to').val('11:59 PM');
        });
    };

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }
        $(".form_datetime").datepicker({
            autoclose: true,
            isRTL: Metronic.isRTL(),
            format: "dd MM yyyy",
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
        });
    };

    var handleTimePickers = function () {
        if (jQuery().timepicker) {
            $('#access_time_from').timepicker({
                autoclose: true,
                showSeconds: false,
                minDate: 0,
                minuteStep: 1,
                defaultTime: '12:01 AM'
            });
            $('#access_time_to').timepicker({
                autoclose: true,
                showSeconds: false,
                minDate: 0,
                minuteStep: 1,
                defaultTime: '11:59 PM'
            });
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function (e) {
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    };

    return {
        init: function () {
            handleTable();
            handleDatePicker();
            handleTimePickers();
        }
    };

}();