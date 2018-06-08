siteObjJs.admin.staffAvailabilityJs = function () {

    var token = $('meta[name="csrf-token"]').attr('content');
    var grid;
    var deleteMessage = 'Are you sure you want to delete this record?';

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#" + formId).find("input[type=text], input[type=file], textarea").val("");
            $('#dates').datepicker('setDate', null);
            $('#from_date').datepicker('setDate', null);
            $('#to_date').datepicker('setDate', null);
            $("#center_id").select2('val', '');
            $('#staff_id').empty();
            $('#staff_id').append($('<option>', {
                value: '',
                text: siteObjJs.admin.staffAvailabilityJs.selectStaff,
            }));
            $('#staff_id').val("");
            $("#staff_id").select2('val', '');

            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        // Populate staf dropdown on center selection on create page
        $('#create-staff-availability').on('change', '.center_id', function (e) {
            if ($(this).val() != 0) {
                fetchStaffList(this);
            } else {
                $('#staff_id').empty();
                $('#staff_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.staffAvailabilityJs.selectStaff,
                }));
                $('#staff_id').val("");
            }
        });

        // Populate staff dropdown on center selection on index page
        $('#staff-availability-table').on('change', '#center-drop-down-search', function (e) {
            fetchStaffList(this, 'search');
        });

        //reset center dropdown when staff selected index is 0 on create form
        $('#create-staff-availability').on('change', '#staff-listing-content', function (e) {
            var selectedIndexStaff = $('option:selected', this).index();
            if (selectedIndexStaff == 0) {
                $("#center_id").select2("val", "");
            }
        });

        //reset staff dropdown when center selected index is 0 on edit form
        $('#edit_form').on('change', '.center_id', function (e) {
            var selectedIndexCenterE = $('option:selected', this).index();
            if (selectedIndexCenterE === 0) {
                $('#staff-drop-down #staff_id').empty();
                $('#staff-drop-down #staff_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.staffAvailabilityJs.selectStaff,
                }));
                $('#staff-drop-down #staff_id').val("");
                $('#staff-drop-down #staff_id').select2();
            } else {
                fetchStaffList(this);
            }
        });

        //reset center dropdown when machine selected index is 0 on edit form
        $('#edit_form').on('change', '#staff-drop-down', function (e) {
            var selectedIndexStaffE = $('option:selected', this).index();
            if (selectedIndexStaffE == 0) {
                $("#center_drop_down #center_id").select2('val', '');
            }
        });

        //reset center dropdown when staff selected index is 0 on search filters
        $('#staff-availability-table').on('change', 'td#staff-drop-down-search', function (e) {
            var selectedIndex = $('option:selected', this).index();
            if (selectedIndex == 0) {
                $("#center-drop-down-search").select2("val", "");
            }
        });

        //Reset staff dropdown when center selected index is 0 on search filters
        $('#staff-availability-table').on('change', '#center-drop-down-search', function (e) {
            var selectedIndexStaffC = $('option:selected', this).index();
            if (selectedIndexStaffC == 0) {
                $('#staff-availability-table #staff-drop-down-search #staff_id').empty();
                $('#staff-availability-table #staff-drop-down-search #staff_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.staffAvailabilityJs.selectStaff,
                }));
                $('#staff-availability-table #staff-drop-down-search #staff_id').val("");
            }
        });

        //Cancel Filter on search filters
        $('#staff-availability-table').on('click', '.filter-cancel', function (e) {
            $("#center-drop-down-search").select2("val", "");
            $('#staff-availability-table #staff-drop-down-search #staff_id').html("<option value='' selected>Select Staff</option>");
            $('#staff-availability-table #staff_id').select2();
        });

    };


// Method to fetch Users(staff) list on center dropdown value changed

    var fetchStaffList = function (elet, content) {
        content = content || '';
        var currentForm = $(elet).closest("form");
        var centerIDStaff = $(elet).val();
        if (centerIDStaff != '') {
            var actionUrl = 'staff-availability/staffData/' + centerIDStaff;
        }

        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data) {
                if (content === 'search') {
                    htm = $($.parseHTML(data.list)).filter('div#staff-listing-content');
                    $('#staff-availability-table').find("td#staff-drop-down-search").html(htm.html());
                    $('#staff-availability-table #staff_id').select2();
                } else {
                    $(currentForm).find("#staff-drop-down").html(data.list);
                    $('#staff-drop-down #staff_id').select2();
                }
            },
            error: function (jqXhr, json, errorThrown) {
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

    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var staff_availability_id = $(this).attr("id");
            var actionUrl = 'staff-availability/' + staff_availability_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $("#edit_form").html(data.form);
                    $("div.radio-list").css("padding-left", "20px");
                    $("#edit-staff-availability #center_id").select2();
                    $("#edit-staff-availability #staff_id").select2();
                    handleDateTimePicker('edit-staff-availability', data.attributes.availability_date);
                    $("#edit-staff-availability #staff_start_time").val(data.attributes.start_time).attr('disabled', false);
                    $("#edit-staff-availability #staff_end_time").val(data.attributes.end_time).attr('disabled', false);
                    $("#edit-staff-availability #break_time").val(data.attributes.break_time).attr('disabled', false);
                    if (data.attributes.carry_forward_availability == 1) {
                        $("#edit-staff-availability #carry_forwarded_days").attr('disabled', false);
                    }
                    $("#edit-staff-availability #dates").datepicker('setDate', data.attributes.availability_date);
                    //on editing carry forward availability radio no checked, carry forward availability days textbox disabled & value 0
                    $('input:radio[name="carry_forward_availability"][value="0"]').prop('checked', true);
                    //$("#edit-staff-availability #carry_forwarded_days").attr("value", "0");
                    $("#edit-staff-availability #carry_forwarded_days").val("");
                    $("#edit-staff-availability #carry_forwarded_days").attr('disabled', true);

                    siteObjJs.validation.formValidateInit('#edit-staff-availability', handleAjaxRequest);

                    //commenting code by Gauri for disabling carry forward block on edit
                    //$('#edit-staff-availability input[name=carry_forward_availability]').attr("disabled", true);
                    //$('#edit-staff-availability #carry_forwarded_days').attr("disabled", true);
                },
                error: function (jqXhr, json, errorThrown) {
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
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var formId = formElement.attr("id");
        var icon = "check";
        var messageType = "success";

        var start_time = $('#' + formId + ' #config_session_start_time').val();
        var end_time = $('#' + formId + ' #config_session_end_time').val();

        var startTime = $('#' + formId + ' #staff_start_time').val();
        var endTime = $('#' + formId + ' #staff_end_time').val();

        $("#submit_btn").attr('disabled', 'disabled');
        $("#save_btn").attr('disabled', 'disabled');
        
        $.ajax({
            url: actionUrl,
            cache: false,
            type: actionType,
            data: formData,
            success: function (data) {
                //data: return data from server
                if (data.status === "error") {
                    icon = "times";
                    messageType = "danger";
                }

                //Empty the form fields
                formElement.find("input[type=text], textarea").val("");
                $("#center_id").select2('val', '');
                $("#staff_id").select2('val', '');
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
            error: function (jqXhr, json, errorThrown) {
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
        });
    }
    var handleDateTimePicker = function (formId, defaultDate) {
        var start_time = $('#config_session_start_time').val();
        var end_time = $('#config_session_end_time').val();
        var end_time_slot = $('#session_booking_end_time_for_start_time').val();
        // var startTime = $('#' + formId + ' #staff_start_time').val();
        // var endTime = $('#' + formId + ' #staff_end_time').val();
        $('#' + formId + ' #staff_start_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time_slot,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                var startTime = $('#' + formId + ' #staff_start_time').val();
                var endTime = $('#' + formId + ' #staff_end_time').val();
                //}).on('changeTime.timepicker', function (t) {
                //  var startTime = t.time.value;
                //  // var startTime = $('#' + formId + ' #start_time').val();
                // var endTime = $('#' + formId + ' #end_time').val();

                //Start Time Validation
                if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
                    $('#' + formId + ' #staff_start_time_error').html("Start time should be less than end time");
                    $('#' + formId + ' #staff_start_time').val('');
                } else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
                    $('#' + formId + ' #staff_start_time_error').html("Start time and end time cannot be same");
                    $('#' + formId + ' #staff_start_time').val('');
                } else if (Date.parse('01/01/2011 ' + end_time) - Date.parse('01/01/2011 ' + startTime) == 0) {
                    $('#' + formId + ' #staff_start_time_error').html("End time cannot be same as start time.");
                    $('#' + formId + ' #staff_start_time').val('');
                } else {
                    $('#' + formId + ' #staff_start_time_error').html("");
                }
            }
        });

        //End Time Validation
        $('#' + formId + ' #staff_end_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                //}).on('changeTime.timepicker', function (t) {
                var startTime = $('#' + formId + ' #staff_start_time').val();
                var endTime = $('#' + formId + ' #staff_end_time').val();
                //var endTime = t.time.value;

                //End Time Validation
                if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
                    $('#' + formId + ' #staff_end_time_error').html("End time should be greater than start time");
                    $('#' + formId + ' #staff_end_time').val('');
                } else if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
                    $('#' + formId + ' #staff_end_time_error').html("Start time and end time cannot be same");
                    $('#' + formId + ' #staff_end_time').val('');
                } else if (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + start_time) == 0) {
                    $('#' + formId + ' #staff_end_time_error').html("Start Time cannot be same as end time.");
                    $('#' + formId + ' #staff_end_time').val('');
                } else {
                    $('#' + formId + ' #staff_end_time_error').html("");
                }
            }
        });

        $('#' + formId + ' #break_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time_slot,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                // }).on('changeTime.timepicker', function (t) {
                var startTime = $('#' + formId + ' #staff_start_time').val();
                var endTime = $('#' + formId + ' #staff_end_time').val();
                var breakTime = $('#' + formId + ' #break_time').val();
                //var breakTime = t.time.value;

                //Break Time Validation
                if (endTime != '' && (Date.parse('01/01/2011 ' + breakTime) >= Date.parse('01/01/2011 ' + endTime))) {
                    $('#' + formId + ' #break_time_error').html("Break time should be less than end time");
                    $('#' + formId + ' #break_time').val('');
                } else if (startTime != '' && (Date.parse('01/01/2011 ' + breakTime) <= Date.parse('01/01/2011 ' + startTime))) {
                    $('#' + formId + ' #break_time_error').html("Break time should be greater than start time");
                    $('#' + formId + ' #break_time').val('');
                } else if (breakTime == '') {
                    $('#' + formId + ' #break_time_error').html("");
                } else {
                    $('#' + formId + ' #break_time_error').html("");
                }
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

        if (formId == 'create-staff-availability') {
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
                    var radioObj = $('input[type="radio"][name="carry_forward_availability"][value="0"]');
                    radioObj.prop("checked", true);

                    radioObj.parents('span').addClass('checked');
                    radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                    $('input[name=carry_forward_availability]').attr("disabled", true);
                    $('#carry_forwarded_days').attr("disabled", true);
                    $('#carry_forwarded_days').val("");
                } else {
                    $('input[name=carry_forward_availability]').attr("disabled", false);
                    var value = $("input[type = 'radio']:checked").val();
                    if (value != 0) {
                        $('#carry_forwarded_days').attr("disabled", false);
                    } else {
                        $('#carry_forwarded_days').attr("disabled", true);
                    }
                }

                if (!e.dates || (e.dates).length <= 0) {
                    // $('#' + formId + ' #staff_start_time,#staff_end_time,#break_time').prop('disabled', true);
                    $('#' + formId + ' #availability_date').val('');
                } else {
                    //$('#' + formId + ' #staff_start_time,#staff_end_time,#break_time').prop('disabled', false);
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
                //}
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

    var grid;
    var groupActionUrl = adminUrl + '/staff-availability/group-action';
    var ajaxMessageIdentifier = $('#ajax-response-text');

    var handleTable = function () {
        // $('#staff-availability-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#staff-availability-table'),
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
                    // {data: null, name: 'rownum', searchable: false},
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'id', name: 'id', orderable: false},
                    {data: 'scname', name: 'scname'},
                    {data: 'sname', name: 'sname'},
                    {data: 'availability_date', name: 'availability_date'},
                    {data: 'start_time', name: 'start_time'},
                    {data: 'end_time', name: 'end_time'},
                    {data: 'break_time', name: 'break_time'},
                    /* {data: 'carry_forward_availability', name: 'carry_forward_availability'},
                     {data: 'carry_forward_availability_days', name: 'carry_forward_availability_days'},*/
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
                        $(rows).eq(i).children('td:nth-child(2)').html(recNum);//td:first-child
                    });
                },
                "ajax": {
                    "url": "staff-availability/data",
                    "type": "GET"
                }
            }
        });

        //  var grid;
        var deleteMessage = 'Are you sure you want to delete this record?';

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $('.table-group-action-input', grid.getTableWrapper());
            var actionType = action.attr('data-actionType');
            var actionField = action.attr('data-actionField');
            var actionValue = action.val();
            var actionName = action.attr('data-action');
            var rowCount = grid.getSelectedRowsCount();
            if (rowCount === 1) {
                var finalMessage = 'Are you sure you want to delete this  record?';
            } else {
                var finalMessage = 'Are you sure you want to delete these ' + rowCount + ' records?';
            }
            var message = siteObjJs.admin.staffAvailabilityJs.deleteMessage;
            if (action.val() === '' && grid.getSelectedRowsCount() > 0) {
                var formdata = {
                    action: actionName,
                    actionField: actionField,
                    actionType: actionType,
                    actionValue: actionValue,
                    ids: grid.getSelectedRows()
                };
                if (actionName == 'delete') {
                    formdata['action'] = actionName;
                    bootbox.confirm({
                        buttons: {confirm: {label: 'CONFIRM'}},
                        //message: 'Are you sure you want to delete this record?',
                        message: finalMessage,
                        callback: function (result) {
                            if (result === false) {
                                return;
                            }
                            handleGroupAction(grid, formdata);
                        }});
                } else {
                    handleGroupAction(grid, formdata);
                }
            }
//            else if (actionValue === '') {
//                Metronic.alert({
//                    type: 'danger',
//                    icon: 'warning',
//                    message: 'Please select an action',
//                    container: grid.getTableWrapper(),
//                    place: 'prepend',
//                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
//                });
//            }
            else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record (s) selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });

        var table = grid.getTable();
        var oTable = table.dataTable();
        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var message = $(this).attr('data-message');
            bootbox.confirm({
                buttons: {confirm: {label: 'CONFIRM'}},
                message: message,
                callback: function (result) {
                    if (result === false) {
                        return;
                    }
                    var formdata = {
                        action: action,
                        actionField: 'id',
                        actionType: 'group',
                        actionValue: userId,
                        ids: userId
                    };
                    handleGroupAction(grid, formdata);
                }

            });
        });

        function handleGroupAction(grid, data) {
            var form = new FormData();
            form.append('action', data.action);
            form.append('actionType', data.actionType);
            form.append('field', data.actionField);
            form.append('value', data.actionValue);
            form.append('ids', data.ids);
            form.append('_token', token);
            jQuery.ajax({
                url: groupActionUrl,
                cache: false,
                data: form,
                dataType: 'json',
                type: 'POST',
                processData: false,
                contentType: false,
                success: function (data) {
                    grid.getDataTable().ajax.reload();
                    if (data.status === 'success') {
                        Metronic.alert({
                            type: 'success',
                            icon: 'success',
                            message: data.message,
                            container: ajaxMessageIdentifier,
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    } else if (data.status === 'fail') {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: data.message,
                            container: grid.getTableWrapper(),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }
    };

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $(".form_datetime").datepicker({
            timePicker: false,
            format: 'yyyy/mm/dd',
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

    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleTable();
            fetchDataForEdit();
            handleDateTimePicker('create-staff-availability', '');
            handleDatePicker();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-staff-availability', handleAjaxRequest);
        }
    };
}();