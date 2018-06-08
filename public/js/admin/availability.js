siteObjJs.admin.availabilityJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });
        $('body').on('click', '.delete', function (e) {
            e.preventDefault();
            var availabilityId = $(this).attr('data-id');
            bootbox.confirm({
                buttons: {confirm: {label: 'CONFIRM'}},
                message: 'Are you sure you want to delete this record?',
                callback: function (result) {
                    if (result === false) {
                        return;
                    }

                    handleGroupAction(grid);
                }
            });
            function handleGroupAction(grid) {
                var token = $('meta[name="csrf-token"]').attr('content');
                var actionUrl = 'availability/' + availabilityId;
                jQuery.ajax({
                    url: actionUrl,
                    cache: false,
                    data: {
                        _token: token,
                        _method: "delete",
                        ids: availabilityId
                    },
                    type: "POST",
                    success: function (data)
                    {
                        grid.getDataTable().ajax.reload();
                        if (data.status === 'success') {
                            Metronic.alert({
                                type: 'success',
                                icon: 'success',
                                message: data.message,
                                container: $('#ajax-response-text'),
                                place: 'prepend',
                                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                            });
                        } else if (data.status === 'fail') {
                            Metronic.alert({
                                type: 'danger',
                                icon: 'warning',
                                message: data.message,
                                container: $('#ajax-response-text'),
                                place: 'prepend',
                                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {

                    }
                });
            }
        });

//        $('#edit-availability').on('change', '.availability-time', function () {
//            alert(2312);
//        });

    };


    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var availability_id = $(this).attr("id");
            var actionUrl = 'availability/' + availability_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    handleDateTimePicker('edit-availability', data.attributes.availability_date);
                    $("#edit-availability #start_time").val(data.attributes.start_time).attr('disabled', false);
                    $("#edit-availability #end_time").val(data.attributes.end_time).attr('disabled', false);
                    $("#edit-availability #break_time").val(data.attributes.break_time).attr('disabled', false);
                    if (data.attributes.carry_forward_availability == 1) {
                        $("#edit-availability #carry_forwarded_days").attr('disabled', false);
                    }
                    $("#edit-availability #dates").datepicker('setDate', data.attributes.availability_date);

                    siteObjJs.validation.formValidateInit('#edit-availability', handleAjaxRequest);
                    $('#edit-availability input[name=carry_forward_availability]').attr("disabled", true);
                    $('#edit-availability #carry_forwarded_days').attr("disabled", true);
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
        });
    };
    // Common method to handle add and edit ajax request and reponse

    var handleAjaxRequest = function () {
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var actionUrl = formElement.attr("action");
        var formID = formElement.attr("id");
        var actionType = formElement.attr("method");
        var formData = formElement.serialize();
        var icon = "check";
        var messageType = "success";

        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: actionType,
                    data: formData,
                    success: function (data)
                    {
                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }

                        //Empty the form fields
                        formElement.find("input[type=text], textarea").val("");
                        //trigger cancel button click event to collapse form and show title of add page
                        $('.btn-collapse').trigger('click');
                        //reload the data in the datatable
                        grid.getDataTable().ajax.reload();
                        Metronic.alert({
                            type: messageType,
                            icon: icon,
                            message: data.message,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    },
                    error: function (jqXhr, json, errorThrown)
                    {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        //alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                }
        );
    }
    var handleDateTimePicker = function (formId, defaultDate) {
        $('#' + formId + ' #start_time').timepicker({
            minuteStep: 60,
            showSeconds: false,
            showMeridian: true,
            disableFocus: true,
            defaultTime: false
        }).on('changeTime.timepicker', function (t) {
            var startTime = t.time.value;
//            console.log(startTime);
            var endTime = $('#' + formId + ' #end_time').val();
            if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime)))
            {
                $('#' + formId + ' #start_time-error').html("Start time should be less then end time");
            } else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0))
            {
                $('#' + formId + ' #start_time-error').html("Start time and end time cannot be same");
            } else {
                $('#' + formId + ' #start_time-error').html("");
            }
            $.ajax(
                    {
                        url: "availability/check-session-time",
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        "data": {time: startTime},
                        success: function (data)
                        {
                            alert(data);
                        }
                    }
            );


        });

        $('#' + formId + ' #end_time').timepicker({
            minuteStep: 60,
            showSeconds: false,
            showMeridian: true,
            disableFocus: true,
            defaultTime: false
        }).on('changeTime.timepicker', function (t) {
            var startTime = $('#' + formId + ' #start_time').val();
            var endTime = t.time.value;
            if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime)))
            {
                $('#' + formId + ' #end_time-error').html("End time should be greater then start time");
            } else if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0))
            {
                $('#' + formId + ' #end_time-error').html("Start time and end time cannot be same");
            } else {
                $('#' + formId + ' #end_time-error').html("");
            }
        });

        $('#' + formId + ' #break_time').timepicker({
            minuteStep: 60,
            showSeconds: false,
            showMeridian: true,
            disableFocus: true,
            defaultTime: false
        }).on('changeTime.timepicker', function (t) {
            var startTime = $('#' + formId + ' #start_time').val();
            var endTime = $('#' + formId + ' #end_time').val();
            var breakTime = t.time.value;
            if (endTime != '' && (Date.parse('01/01/2011 ' + breakTime) >= Date.parse('01/01/2011 ' + endTime)))
            {
                $('#' + formId + ' #break_time-error').html("Break time should be less than end time");
            } else if (startTime != '' && (Date.parse('01/01/2011 ' + breakTime) <= Date.parse('01/01/2011 ' + startTime)))
            {
                $('#' + formId + ' #break_time-error').html("Break time should be greater than start time");
            } else {
                $('#' + formId + ' #break_time-error').html("");
            }
        });

        $('#' + formId + " input[type = 'radio']").click(function () {
            if ($(this).is(":checked")) {
                if ($(this).val() == 1) {
                    $('#' + formId + " #carry_forwarded_days").prop('disabled', false);
                } else {
                    $('#' + formId + " #carry_forwarded_days").prop('disabled', true);
                }
            }
        });

        if (formId == 'create-availability') {
            $('#' + formId + ' #dates').datepicker({
                startDate: '0d',
                endDate: '+29d',
                clearButton: false,
                todayButton: false,
                multidate: 30,
                format: 'dd/mm/yyyy',
                todayHighlight: true,
            }).on('changeDate', function (e) {
                if (!e.dates || (e.dates).length > 1) {
                    $('input[name=carry_forward_availability]').attr("disabled", true);
                    $('#carry_forwarded_days').attr("disabled", true);
                } else {
                    $('input[name=carry_forward_availability]').attr("disabled", false);
                    var value = $("input[type = 'radio']:checked").val();
                    if (value != 0) {
                        $('#carry_forwarded_days').attr("disabled", false);
                    } else {
                        $('#carry_forwarded_days').attr("disabled", true);
                    }

                    if (!e.dates || (e.dates).length <= 0) {
                        $('#' + formId + ' #start_time,#end_time,#break_time').prop('disabled', true);
                        $('#' + formId + ' #availability_date').val('');
                    } else {
                        $('#' + formId + ' #start_time,#end_time,#break_time').prop('disabled', false);
                        var availableDates = [];
                        $.each(e.dates, function (i, dt) {
                            var dateString = '';
                            dateString += dt.getDate() + "-";
                            dateString += (dt.getMonth() + 1) + "-";
                            dateString += dt.getFullYear();
                            availableDates.push(dateString);
                        })
                        $('#' + formId + ' #availability_date').val((availableDates));
                    }
                }

            });
        } else {
            $('#' + formId + ' #dates').datepicker({
                startDate: defaultDate,
                endDate: defaultDate,
                defaultDate: defaultDate,
                clearButton: false,
                todayButton: false,
                format: 'dd/mm/yyyy',
            });
        }
    };

    var handleTable = function () {

        grid = new Datatable();
        grid.init({
            src: $('#availability-table'),
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
                    {data: 'availability_date', name: 'availability_date'},
                    {data: 'start_time', name: 'start_time'},
                    {data: 'end_time', name: 'end_time'},
                    {data: 'break_time', name: 'break_time'},
                    {data: 'carry_forward_availability', name: 'carry_forward_availability'},
                    {data: 'carry_forward_availability_days', name: 'carry_forward_availability_days'},
                    {data: 'status', name: 'status'},
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
                    "url": "availability/data",
                    "type": "GET"
                },
                "order": [
                    [1, "desc"]
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
            handleTable();
            fetchDataForEdit();
            handleDateTimePicker('create-availability', '');
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-availability', handleAjaxRequest);
        }

    };
}();