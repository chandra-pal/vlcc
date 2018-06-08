siteObjJs.admin.roomAvailabilityJs = function () {

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
            $('#room_id').empty();
            $('#room_id').append($('<option>', {
                value: '',
                text: siteObjJs.admin.roomAvailabilityJs.selectRoom,
            }));
            $('#room_id').val("");
            $("#room_id").select2('val', '');

            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        // Populate room dropdown on center selection on create page
        $('#create-room-availability').on('change', '.center_id', function (e) {
            if ($(this).val() != 0) {
                fetchRoomList(this);
            } else {
                $('#room_id').empty();
                $('#room_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.roomAvailabilityJs.selectRoom,
                }));
                $('#room_id').val("");
            }
        });

        //Populate room dropdown on center selection on index page
        $('#room-availability-table').on('change', '#center-drop-down-search', function (e) {
            fetchRoomList(this, 'search');
        });

        //reset center dropdown when room selected index is 0 on create form
        $('#create-room-availability').on('change', '#room-listing-content', function (e) {
            var selectedIndexRoom = $('option:selected', this).index();
            if (selectedIndexRoom == 0) {
                $("#center_id").select2("val", "");
            }
        });

        //reset room dropdown when center selected index is 0 on edit form
        $('#edit_form').on('change', '.center_id', function (e) {
            var selectedIndexCenterE = $('option:selected', this).index();
            if (selectedIndexCenterE === 0) {
                $('#room-drop-down #room_id').empty();
                $('#room-drop-down #room_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.roomAvailabilityJs.selectRoom,
                }));
                $('#room-drop-down #room_id').val("");
                $('#room-drop-down #room_id').select2();
            } else {
                fetchRoomList(this);
            }
        });

        //reset center dropdown when machine selected index is 0 on edit form
        $('#edit_form').on('change', '#room-drop-down', function (e) {
            var selectedIndexRoomE = $('option:selected', this).index();
            if (selectedIndexRoomE == 0) {
                $("#center_drop_down #center_id").select2('val', '');
            }
        });

        //reset center dropdown when room selected index is 0 on search filters
        $('#room-availability-table').on('change', 'td#room-drop-down-search', function (e) {
            var selectedIndex = $('option:selected', this).index();
            if (selectedIndex == 0) {
                $("#center-drop-down-search").select2("val", "");
            }
        });

        //Reset machine dropdown when center selected index is 0 on search filters
        $('#room-availability-table').on('change', '#center-drop-down-search', function (e) {
            var selectedIndexRoomC = $('option:selected', this).index();
            if (selectedIndexRoomC == 0) {
                $('#room-availability-table #room-drop-down-search #room_id').empty();
                $('#room-availability-table #room-drop-down-search #room_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.roomAvailabilityJs.selectRoom,
                }));
                $('#room-availability-table #room-drop-down-search #room_id').val("");
            }
        });

        //Cancel Filter on search filters
        $('#room-availability-table').on('click', '.filter-cancel', function (e) {
            $("#center-drop-down-search").select2("val", "");
            $('#room-availability-table #room-drop-down-search #room_id').html("<option value='' selected>Select Room</option>");
            $('#room-availability-table #room_id').select2();
        });
    };


    // Method to fetch Rooms list on center dropdown value changed

    var fetchRoomList = function (elet, content) {
        content = content || '';
        var currentForm = $(elet).closest("form");
        var centerIDRoom = $(elet).val();
        if (centerIDRoom !== '') {
            var actionUrl = 'rooms-availability/roomData/' + centerIDRoom;
        }

        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data) {
                if (content === 'search') {
                    htm = $($.parseHTML(data.list)).filter('div#room-listing-content');
                    $('#room-availability-table').find("td#room-drop-down-search").html(htm.html());
                    $('#room-availability-table #room_id').select2();
                } else {
                    $(currentForm).find("#room-drop-down").html(data.list);
                    $('#room-drop-down #room_id').select2();
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
            var room_availability_id = $(this).attr("id");
            var actionUrl = 'rooms-availability/' + room_availability_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $("#edit_form").html(data.form);
                    $("div.radio-list").css("padding-left", "20px");
                    $("#edit-room-availability #center_id").select2();
                    $("#edit-room-availability #room_id").select2();
                    handleDateTimePicker('edit-room-availability', data.attributes.availability_date);
                    $("#edit-room-availability #room_start_time").val(data.attributes.start_time).attr('disabled', false);
                    $("#edit-room-availability #room_end_time").val(data.attributes.end_time).attr('disabled', false);
                    // $("#edit-room-availability #break_time").val(data.attributes.break_time).attr('disabled', false);
                    if (data.attributes.carry_forward_availability == 1) {
                        $("#edit-room-availability #carry_forwarded_days").attr('disabled', false);
                    }
                    $("#edit-room-availability #dates").datepicker('setDate', data.attributes.availability_date);
                    //on editing carry forward availability radio no checked, carry forward availability days textbox disabled & value 0
                    $('input:radio[name="carry_forward_availability"][value="0"]').prop('checked', true);
                    //$("#edit-room-availability #carry_forwarded_days").attr("value", "0");
                    $("#edit-room-availability #carry_forwarded_days").val("");
                    $("#edit-room-availability #carry_forwarded_days").attr('disabled', true);

                    siteObjJs.validation.formValidateInit('#edit-room-availability', handleAjaxRequest);

                    //commenting code by Gauri for disabling carry forward block on edit
                    // $('#edit-room-availability input[name=carry_forward_availability]').attr("disabled", true);
                    //$('#edit-room-availability #carry_forwarded_days').attr("disabled", true);
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

        var startTime = $('#' + formId + ' #room_start_time').val();
        var endTime = $('#' + formId + ' #room_end_time').val();
        
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
                $("#room_id").select2('val', '');
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
        // var startTime = $('#' + formId + ' #room_start_time').val();
        // var endTime = $('#' + formId + ' #room_end_time').val();
        $('#' + formId + ' #room_start_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time_slot,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                var startTime = $('#' + formId + ' #room_start_time').val();
                var endTime = $('#' + formId + ' #room_end_time').val();
                //}).on('changeTime.timepicker', function (t) {
                //var startTime = t.time.value;
                // var startTime = $('#' + formId + ' #start_time').val();
                //var endTime = $('#' + formId + ' #end_time').val();

                //Start Time validation
                if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
                    $('#' + formId + ' #room_start_time_error').html("Start time should be less than end time");
                    $('#' + formId + ' #room_start_time').val('');
                } else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
                    $('#' + formId + ' #room_start_time_error').html("Start time and end time cannot be same");
                    $('#' + formId + ' #room_start_time').val('');
                } else if (Date.parse('01/01/2011 ' + end_time) - Date.parse('01/01/2011 ' + startTime) == 0) {
                    $('#' + formId + ' #room_start_time_error').html("End time cannot be same as start time.");
                    $('#' + formId + ' #room_start_time').val('');
                } else {
                    $('#' + formId + ' #room_start_time_error').html("");
                }
            }
        });

        $('#' + formId + ' #room_end_time').timepicker({
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
                var startTime = $('#' + formId + ' #room_start_time').val();
                var endTime = $('#' + formId + ' #room_end_time').val();
                //  var endTime = t.time.value;

                //End Time Validation
                if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
                    $('#' + formId + ' #room_end_time_error').html("End time should be greater than start time");
                    $('#' + formId + ' #room_end_time').val('');
                } else if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
                    $('#' + formId + ' #room_end_time_error').html("Start time and end time cannot be same");
                    $('#' + formId + ' #room_end_time').val('');
                } else if (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + start_time) == 0) {
                    $('#' + formId + ' #room_end_time_error').html("Start Time cannot be same as end time.");
                    $('#' + formId + ' #room_end_time').val('');
                } else {
                    $('#' + formId + ' #room_end_time_error').html("");
                }
            }
        });
        /* $('#' + formId + ' #break_time').timepicker({
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
         });*/

        $('#' + formId + " input[type = 'radio']").click(function () {
            if ($(this).is(":checked")) {
                if ($(this).val() == 1) {
                    $('#' + formId + " #carry_forwarded_days").prop('disabled', false);
                } else {
                    $('#' + formId + " #carry_forwarded_days").prop('disabled', true);
                }
            }
        });

        if (formId == 'create-room-availability') {
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
                    // $('#' + formId + ' #room_start_time,#room_end_time,#break_time').prop('disabled', true);
                    $('#' + formId + ' #availability_date').val('');
                } else {
                    //$('#' + formId + ' #room_start_time,#room_end_time,#break_time').prop('disabled', false);
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
    var groupActionUrl = adminUrl + '/rooms-availability/group-action';
    var ajaxMessageIdentifier = $('#ajax-response-text');

    var handleTable = function () {
        // $('#room-availability-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#room-availability-table'),
            // src: $('#' + tableId),
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
                    //{data: null, name: 'rownum', searchable: false},
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'id', name: 'id', orderable: false},
                    {data: 'rcname', name: 'rcname'},
                    {data: 'rname', name: 'rname'},
                    {data: 'availability_date', name: 'availability_date'},
                    {data: 'start_time', name: 'start_time'},
                    {data: 'end_time', name: 'end_time'},
                    //{data: 'break_time', name: 'break_time'},
                    //{data: 'carry_forward_availability', name: 'carry_forward_availability'},
                    //{data: 'carry_forward_availability_days', name: 'carry_forward_availability_days'},
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
                        $(rows).eq(i).children('td:nth-child(2)').html(recNum);
                    });

//                    var d = new Date();
//                    var month = d.getMonth()+1;
//                    var day = d.getDate();
//                    var output1= ((''+day).length<2 ? '0' : '') + day + '/' + ((''+month).length<2 ? '0' : '') + month + '/' +d.getFullYear() ;
//                    api.column(4, {page: 'current'}).data().each(function (group, i) {
//                    var availability_date1 = (group!= null) ? group : '';
//                    var arr = availability_date1.split('-');
//                    var month = "";
//                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//                    var j = 0;
//                    for (j; j < months.length; j++) {
//                        if (months[j] == arr[1]) {
//                            break;
//                        }
//                    }
//                    j++;
//                    if (j >= 10) month = j;
//                    else month = "0" + j;
//
//                    var formatddate = arr[0] + '/' + month + '/' + arr[2];
//
//                var today = new Date();
//                var d1=formatddate.split('/');
//                var f1 = new Date(d1[2], d1[1] - 1, d1[0]);
//                var timeinmilisec = today.getTime() - f1.getTime();
//                //console.log( Math.floor(timeinmilisec / (1000 * 60 * 60 * 24)) );
//                var daysDiff = Math.floor(timeinmilisec / (1000 * 60 * 60 * 24));
//
//                      if(daysDiff > 1){
//                            //console.log('Date entered is greater than current date');
//                            $(rows).eq(i).hide();
//                        }else{
//                           //console.log('Date entered is not greater than current date');
//                            $(rows).eq(i).show();
//                        }
//              });
                },
                "ajax": {
                    "url": "rooms-availability/data",
                    "type": "GET"
                }
            }
        });

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
            var message = siteObjJs.admin.roomAvailabilityJs.deleteMessage;
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
            handleDateTimePicker('create-room-availability', '');
            handleDatePicker();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-room-availability', handleAjaxRequest);
        }

    };
}();