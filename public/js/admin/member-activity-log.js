siteObjJs.admin.memberActivityLogJs = function () {
    var initializeListener = function () {
        $("#s2id_customer_select").after('<div class="error"><span class="help-block help-block-error" id="customer_error"></span></div>');
        var selectedVal = $('#customer_select').val();
        var selectedDate = $('#activity_date').val();
        if (selectedVal != 0)
        {
            getActivityDeviation(selectedVal, selectedDate);
            handleTable('member-activity-log-table', selectedVal, selectedDate);
        } else {
            initalizeTable();
        }

        $('body').on('change', '#customer_select', function (e) {
            if ($(this).val() != 0)
            {
                var selectedDate = $('#activity_date').val();
                getActivityDeviation($(this).val(), selectedDate);
                handleTable('member-activity-log-table', $(this).val(), selectedDate);
            } else
            {
                $("#deviation_span").html("");
                $('#member-activity-log-table-body').empty("");
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
                            getActivityDeviation(customer_id, selectedDate);
                            handleTable('member-activity-log-table', customer_id, selectedDate);
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

    var handleTable = function (tableId, customerId, date) {
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#' + tableId),
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
                    {data: null, name: 'rownum', searchable: false, orderable: false},
                    {data: 'activity', name: 'activity'},
                    {data: 'duration', name: 'duration'},
                    {data: 'activity_date', name: 'activity_date'},
//                  {data: 'start_time', name: 'start_time'},
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
                },
                "ajax": {
                    "url": "member-activity-log/data",
                    "type": "GET",
                    "data": {customerId: customerId, date: date},
                },
            }
        });
        $('#data-search').hide();
    };

    var getActivityDeviation = function (clientId, date) {
        var actionUrl = 'member-activity-log/get-deviation';
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            data: {"client_id": clientId, "date": date},
            success: function (data)
            {
                if (0 == data) {
                    $("#deviation_span").html("No Deviation");
                } else if (data > 0) {
                    $("#deviation_span").html("Over Achieved (" + data + ")");
                } else {
                    $("#deviation_span").html("Under Achieved (" + data + ")");
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
    };

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $(".form_datetime").datepicker({
            timePicker: false,
            autoclose: true,
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
        });

        $('body').on('click', '.from-btn', function (e) {
            $(".from-date").datepicker('hide');
            $('.from-date').data('datepicker').setDate(null);
        });

        $('body').on('click', '.to-btn', function (e) {
            $(".to-date").datepicker('hide');
            $('.to-date').data('datepicker').setDate(null);
        });


        $(".activity-date").datepicker({
            autoclose: true,
            isRTL: Metronic.isRTL(),
            endDate: '+0d',
            format: 'dd-mm-yyyy',
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
        }).on('changeDate', function (e) {
            if ($(this).val() != 0 && $(this).val() != "")
            {
                var client_id = $("#customer_select").val();
                if (client_id != '') {
                    handleTable('member-activity-log-table', client_id, $(this).val());
                    getActivityDeviation(client_id, $(this).val());
                } else {
                    $("#customer_error").html("Please select Customer");
                    return false;
                }
            }

        });
    };

    var initalizeTable = function () {
        $('#member-activity-log-table').dataTable({
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
//            handleTable();
            handleDatePicker();
        }

    };
}();