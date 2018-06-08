siteObjJs.admin.sessionBookingsJs = function () {

// Initialize all the page-specific event listeners here.
    var staff_arr = new Array();
    var initializeListener = function () {
        if (session_page_type == "notification") {
            handleDateTimePicker('edit-session-bookings', '');
            siteObjJs.validation.formValidateInit('#' + $('form').attr('id'), handleAjaxRequest);
        }

        var selectedVal = $('#customer_select').val();
        if ($("#session_notification_flag").length > 0) {
            siteObjJs.validation.formValidateInit('#edit-session-bookings', handleAjaxRequest);
        } else {
            if ($("#customer_select").length > 0 && selectedVal != 0) {
                fetchPackageList(selectedVal);
                getAvailabilityList(selectedVal);
            }
        }

        $('body').on("click", "a.mark-session-complete", function (e) {
            var id = $(this).attr('id');
            var actionUrl = adminUrl + '/update-session-status';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {session_id: id},
                success: function (data)
                {
                    $('#todays-sessions-table').dataTable().fnDestroy();
                    handleTable();
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
            $("#staff_id").select2();
            $("#machine_id").select2();
            $("#room_id").select2();
            $('#staff_calender').html('');
            $('#machine_calender').html('');
            $('#room_calender').html('');
            $('#session_date').val('');
            $("#used-services-block").hide();
        });

        $('body').on('change', '#customer_select', function (e) {
            $('#edit-session-bookings').parents('div.portlet-body.form').find('.btn-collapse').trigger('click');
            $('#todays-sessions-table').empty("");
            $('#fullcalendar').fullCalendar('destroy');
            $('#session_start_time').val("");
            $('#session_end_time').val("");
            $("#staff_id").select2();
            $("#machine_id").select2();
            $("#room_id").select2();
            $('#staff_calender').html('');
            $('#machine_calender').html('');
            $('#room_calender').html('');
            $('#session_date').val('');
            $("#service_id").select2('val', '');

            if ($(this).val() != 0) {
                var customerId = $(this).val();
                getAvailabilityList(customerId);
            } else {
                var customerId = 0;
            }
            handleCalendar(customerId);
            fetchPackageList(customerId);
            if (customerId == 0) {
                fetchServiceList(0, 0);
            }
        });

        $('body').on('change', '#service_id', function (e) {
            var form = this.form;
            var formId = this.form.id;
            checkSessionAvailable(formId);
        });

        $('body').on('change', '#center_select', function (e) {
            $('#session_start_time').val("");
            $('#session_end_time').val("");
            $("#staff_id").select2('val', 'empty');
            $("#machine_id").select2('val', 'empty');
            $("#room_id").select2('val', 'empty');
            $('#staff_calender').html('');
            $('#machine_calender').html('');
            $('#room_calender').html('');
            $('#session_date').val('');
            $("#service_id").select2('val', '');
            $("#package_id").select2('val', '');
            var centerId = $(this).val();
            if (centerId != "") {
                var actionUrl = adminUrl + '/members/centerwise-members';
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
                            $('#fullcalendar').fullCalendar('destroy');
                            handleCalendar(customer_id);
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

        $('.submit_session').click(function (e) {
            console.log(e);
            var selectedVal = $('#customer_select').val();
            if (selectedVal == "") {
                $(".customer_error").html("Please select Customer.");
                $("div#s2id_customer_select a.select2-choice").css("border", "1px solid red");
            } else {
                $(".customer_error").html("");
                $("div#s2id_customer_select a.select2-choice").css("border", "1px solid #e5e5e5");
            }
        });

        $('.togglelable, .btn-expand-form').click(function (e) {
            $(".cancellation_comment").data('rule-required',false);
            $(".session_cancellatiom_comment").css("display", "none");
            //$(".session_tr_comment").attr("style", "");
            $(".submit_session").attr("disabled", false);
            $(".session-validation").html("");
            //$("#customer_select").select2('val', '');
            $("#package_id").select2('val', '');
            $("#service_id").select2('val', '');
            $("#ola_cab_required").select2('val', '');
            $("#status").select2('val', 'empty');
            $("#staff_id").select2('val', 'empty');
            $("#machine_id").select2('val', 'empty');
            $("#room_id").select2('val', 'empty');
            $(".select2-search-choice").remove();
            /*$("#machine_id").data('rule-required',true);
            $("#room_id").data('rule-required',true);
            $(".session_tr_machine").attr("style", "");
            $(".session_tr_room").attr("style", "");*/
        });

        $('.btn-expand-history').click(function(e){
            $("#time-from").val("");
            $("#time-to").val("");
            $(".history").css("display","block");
            $(".date-Block").css("display","block");
        });

        $('.date-hide').click(function(e){
            $(".history").css("display","none");
            $("#time-from").val("");
            $("#time-to").val("");
        });

		$('.date-submit').click(function(){
			var fromDate = $("#time-from").val();
			var toDate = $("#time-to").val();
			var arrStartDate = fromDate.split("-");
			var date1 = new Date(arrStartDate[2],arrStartDate[1],arrStartDate[0]);
			var arrEndDate = toDate.split("-");
			var date2 = new Date(arrEndDate[2],arrEndDate[1],arrEndDate[0]);
			if(fromDate == "")
			{
				$("#date-error").css('display','block');
				return false;
			}
			if(date1 > date2)
			{
				$("#date-range-error").css('display','block');
				return false;
			}
			else
			{
				$("#date-range-error").css('display','none');
			}
		});
		
        $('body').on('change', '.session_status', function (e) {
            var session_status = $(this).val();
            $('.cancellation_comment').val('');
            if (session_status == 4) {
                $(".cancellation_comment").data('rule-required',true);
                //$(".session_tr_comment").css("display", "none");
                //$(".session_cancellatiom_comment").css("display", "block");
                $(".session_cancellatiom_comment").attr("style", "");

            } else {
                $(".cancellation_comment").data('rule-required',false);
                //$(".session_tr_comment").attr("style", "");
                $(".session_cancellatiom_comment").css("display", "none");
            }
            /*if (session_status == 9) {
                $("#machine_id").data('rule-required',false);
                $("#room_id").data('rule-required',false);
                $(".session_tr_machine").css("display", "none");
                $(".session_tr_room").css("display", "none");
            } else {
                $("#machine_id").data('rule-required',true);
                $("#room_id").data('rule-required',true);
                $(".session_tr_machine").attr("style", "");
                $(".session_tr_room").attr("style", "");
            }*/
        });

        $('body').on('change', '.select-package', function (e) {
            //if ($(this).val() != 0) {
            var form_id = $(this).closest("form").attr('id');
            //var memberId = $('#' + form_id + ' #customer_select').val();
            var memberId = $('#customer_select').val();
            fetchServiceList(memberId, $(this).val());
            //} 
        });

        $('body').on('change', '.session_date', function (e) {
            var form = this.form;
            var formId = this.form.id;
            var startTime = $('#' + formId + ' #session_start_time').val();
            var session_date = $('#' + formId + ' #session_date').val();
            // Check if Difference between todays date & session date is not greater than 90 days
            var todaysDate = new Date();
            var diff = new Date(new Date(session_date) - todaysDate);
            // get days
            var days = diff / 1000 / 60 / 60 / 24;
            if (Math.floor(days) >= 90) {
				
                Metronic.alert({
                    type: 'danger',
                    message: "Booking date should be before 90 days from today.",
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
                $('#' + formId + ' #session_date').val('');
                return false;
            }

            if (session_date != null) {
                checkSessionAvailable(formId);
            }

            var form_id = $(this).closest("form").attr('id');
            $("#" + form_id + " #session_date_error").html('');
            var date = new Date($("#" + form_id + " #session_date").val());
            var session_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

            if ($("#staff_id").val() != null) {
                var staff_calender = "staff_calender";
                handleAvailabilityCalendar($("#" + form_id + "  #staff_id").val(), $("#center_id").val(), 1, staff_calender, session_date, form_id);
            }

            if ($("#machine_id").val() != null) {
                var machine_calender = "machine_calender";
                handleAvailabilityCalendar($("#" + form_id + "  #machine_id").val(), $("#center_id").val(), 2, machine_calender, session_date, form_id);
            }

            if ($("#room_id").val() != null) {
                var room_calender = "room_calender";
                handleAvailabilityCalendar($("#" + form_id + "  #room_id").val(), $("#center_id").val(), 3, room_calender, session_date, form_id);
            }
        });

        $('body').on('click', '.delete', function (e) {
            e.preventDefault();
            var sessionBookingsId = $(this).attr('id');
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
                var actionUrl = adminUrl + '/session-bookings/' + sessionBookingsId;
                jQuery.ajax({
                    url: actionUrl,
                    cache: false,
                    data: {
                        _token: token,
                        _method: "delete",
                        ids: sessionBookingsId
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

        $('body').on('change', '#staff_id', function (e) {
            var form_id = $(this).closest("form").attr('id');
            if ($(this).val() != 0 && $(this).val() != null) {
                if ('' != $("#" + form_id + " #session_date").val()) {
                    $("#" + form_id + " #session_date_error").html('');
                    var date = new Date($("#" + form_id + " #session_date").val());
                    var session_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                    var div_id = "staff_calender";
                    handleAvailabilityCalendar($(this).val(), $("#center_id").val(), 1, div_id, session_date, form_id);
                } else {
                    $("#" + form_id + " #session_date_error").html("Please select Session Date.");
                }
            } else {
                $('#' + form_id + ' #staff_calender').html('');
            }
        });

        $('body').on('change', '#machine_id', function (e) {
            var form_id = $(this).closest("form").attr('id');
            var addedId;
            var addedText;
            if (e.added){
                addedId = e.added.id;
                addedText = e.added.text;
            }
            if (addedId == 9999999){
                $(this).val(addedId);
                var len = $("#machine_list ul .select2-search-choice").length;
                if (len > 0){
                    $("#machine_list ul .select2-search-choice").not(':last').remove();
                }
                //$("#machine_list ul .select2-search-choice:not(:last)").remove();
            }

            if ($(this).val() != 9999999 && $(this).val() != null) {
                if ($( "#machine_list ul li div").eq(0).text() == 'Not Required'){
                    $( "#machine_list ul li div").eq(0).parent( "li" ).remove();
                    $(this).val(addedId);
                }
                if ('' != $("#" + form_id + " #session_date").val()) {
                    if ($(this).val() != 0 && $(this).val() != null) {
                        $("#" + form_id + " #session_date_error").html("");
                        var date = new Date($("#" + form_id + " #session_date").val());
                        var session_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                        var div_id = "machine_calender";
                        handleAvailabilityCalendar($(this).val(), $("#center_id").val(), 2, div_id, session_date, form_id);
                    }
                } else {
                    $("#" + form_id + " #session_date_error").html("Please select Session Date.");
                }
            } else {
                $('#' + form_id + ' #machine_calender').html('');
            }
        });

        $('body').on('change', '#room_id', function (e) {
            var form_id = $(this).closest("form").attr('id');
            var addedId;
            var addedText;
            if (e.added){
                addedId = e.added.id;
                addedText = e.added.text;
            }
            if (addedId == 9999999){
                $(this).val(addedId);
                //$("#room_list ul .select2-search-choice:not(:last)").remove();
                var len = $("#room_list ul .select2-search-choice").length;
                if (len > 0){
                    $("#room_list ul .select2-search-choice").not(':last').remove();
                }
            }
            if ($(this).val() != 9999999 && $(this).val() != null) {
                if ($( "#room_list ul li div").eq(0).text() == 'Not Required'){
                    $( "#room_list ul li div").eq(0).parent( "li" ).remove();
                    $(this).val(addedId);
                }
                if ('' != $("#" + form_id + " #session_date").val()) {
                    if ($(this).val() != 0 && $(this).val() != null) {
                        $("#" + form_id + " #session_date_error").html("");
                        var date = new Date($("#" + form_id + " #session_date").val());
                        var session_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                        var div_id = "room_calender";
                        handleAvailabilityCalendar($(this).val(), $("#center_id").val(), 3, div_id, session_date, form_id);
                    }
                } else {
                    $("#" + form_id + " #session_date_error").html("Please select Session Date.");
                }
            } else {
                $('#' + form_id + ' #room_calender').html('');
            }
        });

    };

    var getAvailabilityList = function (memberId) {
        var actionUrl = "session-bookings/get-availability-list";
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: memberId},
            success: function (data) {

                $("#staff_list").html(data.staff_list);
                $("#staff_id").select2();

                $("#machine_list").html(data.machine_list);
                $("#machine_id").select2();

                $("#room_list").html(data.room_list);
                $("#room_id").select2();

                $("#center_id").val(data.center_id);

                $("#staff_id").select2('val', 'empty');
                $("#machine_id").select2('val', 'empty');
                $("#room_id").select2('val', 'empty');

                $("#staff_id").find("option").eq(0).attr('disabled', 'disabled');
                $("#machine_id").find("option").eq(0).attr('disabled', 'disabled');
                $("#room_id").find("option").eq(0).attr('disabled', 'disabled');
            }
        });
    };

    var handleDatePicker = function (dateArray) {
        if (!jQuery().datepicker) {
            return;
        }
        var date = new Date();
        var currentMonth = date.getMonth();
        var currentDate = date.getDate();
        var currentYear = date.getFullYear();

//        $(".form_datetime").datepicker({
//            autoclose: true,
//            isRTL: Metronic.isRTL(),
//            format: "dd MM yyyy"
//        });

        $(".form_datetime").datepicker({
            autoclose: true,
            isRTL: Metronic.isRTL(),
            format: 'dd MM yyyy',
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
//        }).on('changeDate', function (e) {
//            if ($(this).val() != 0 && $(this).val() != "")
//            {
//                var client_id = $("#customer_select").val();
//                if (client_id != '') {
//                    handleTable('member-activity-log-table', client_id, $(this).val());
//                    getActivityDeviation(client_id, $(this).val());
//                } else {
//                    $("#customer_error").html("Please select Customer");
//                    return false;
//                }
//            }

        });
		$('#time-from').datepicker({
           format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#time-to').datepicker('setStartDate', startDate);
        }).on('clearDate', function (selected) {
            $('#time-to').datepicker('setStartDate', null);
        });

        $('#time-to').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#time-from').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
           $('#time-from').datepicker('setEndDate', null);
       });
		
    };

    var checkSessionAvailable = function (formId) {   
        var customer_id = $("#customer_select").val();
        var session_date = $('#' + formId + ' #session_date').val();
        var packageId = $('#' + formId + ' #package_id').val();
        var serviceId = $('#' + formId + ' #service_id').val();
        var startTime = $('#' + formId + ' #session_start_time').val();
        var endTime = $('#' + formId + ' #session_end_time').val();
        var session_id = 0;
        if ($("#session_id").length != 0) {
            var session_id = $('#' + formId + ' #session_id').val();
        }
        
        // if (customer_id != "" && session_date != "" && customer_id != undefined && session_date != undefined) {
        if (customer_id != "" && session_date != "" && packageId != "" && serviceId != "" && startTime != "" && endTime != "" && customer_id != undefined && session_date != undefined && packageId != undefined && serviceId != undefined && startTime != undefined && endTime != undefined) {
            var actionUrl = adminUrl + '/check-session-booking';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {customer_id: customer_id, session_date: session_date, package_id: packageId, service_id: serviceId, start_time: startTime, end_time: endTime, session_id: session_id},
                success: function (data)
                {
                    if (data > 0) {
                        Metronic.alert({
                            type: 'danger',
                            message: "You have already Booked Session for this time slot.",
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                        $(".select-customer").select2('val', '');
                        $(".session_date").val('');
                        $(".session_start_time").val('');
                        $("#session_end_time").val('');
                        $(".select-package").select2('val', '');
                        $("#service_id").select2('val', '');
                        return false;
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
    };

    // Function To List Packages Data of selected member
    var fetchPackageList = function (memberId) {
        var actionUrl = adminUrl + '/members/packages';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: memberId},
            success: function (data)
            {
                $(".package_information").html("");
                $(".package_information").html(data.package_list);
                $(".select-package").select2({
                    allowClear: true,
                    placeholder: $(this).attr('data-label-text'),
                    width: null
                });
                //checkSessionAvailable();
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

    // Function To List Service Data of selected member
    var fetchServiceList = function (memberId, packageId) {
        var actionUrl = adminUrl + '/members/services';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: memberId, package_id: packageId},
            success: function (data)
            {
                $(".service_information").html("");
                $(".service_information").html(data.service_list);
                $(".select-service").select2({
                    allowClear: true,
                    placeholder: $(this).attr('data-label-text'),
                    width: null
                });
                $("#service_id").find("option").eq(0).attr('disabled', 'disabled');
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

    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

    var handleDateTimePicker = function (formId, defaultDate) {

        var start_time = $('#config_session_start_time').val();
        var end_time = $('#config_session_end_time').val();
        var end_time_slot = $('#session_booking_end_time_for_start_time').val();
        $('#' + formId + ' #session_start_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time_slot,
            //minTime: start_time,
            //maxTime: end_time,
            //defaultTime: start_time,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                checkSessionAvailable(formId);
//                var startTime = $('#' + formId + ' #session_start_time').val();
//                var endTime = $('#' + formId + ' #session_end_time').val();
//                var session_date = $('#' + formId + ' #session_date').val();
//
//                var currentTime = new Date();
//                var selected_time = new Date(session_date + " " + startTime);
//                var diffHours = (selected_time - currentTime) / 1000 / 60;
//
//
//                if (currentTime > selected_time) {
//                    $('#' + formId + ' #start_time_display').html("You cannot book Past Sessions.");
//                    $('#' + formId + ' #session_start_time').val('');
//                }
//                else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime)))
//                {
//                    $('#' + formId + ' #start_time_display').html("Start time should be less than end time");
//                    $('#' + formId + ' #session_start_time').val('');
//                }
//                else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0))
//                {
//                    $('#' + formId + ' #start_time_display').html("Start time and end time cannot be same");
//                    $('#' + formId + ' #session_start_time').val('');
//                } else if (Date.parse('01/01/2011 ' + end_time) - Date.parse('01/01/2011 ' + startTime) == 0) {
//                    $('#' + formId + ' #start_time_display').html("Start Time cannot be same as end time.");
//                    $('#' + formId + ' #session_start_time').val('');
//                } else if (diffHours < 180) {
//                    $('#' + formId + ' #start_time_display').html("You cannot book session within 3 hours time.");
//                    $('#' + formId + ' #session_start_time').val('');
//                } else {
//                    $('#' + formId + ' #start_time_display').html("");
//                }
            }
        });

        $('#' + formId + ' #session_end_time').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: start_time,
            maxTime: end_time,
            //minTime: start_time,
            //maxTime: end_time,
            //defaultTime: start_time,
            startTime: start_time,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function (time) {
                var startTime = $('#' + formId + ' #session_start_time').val();
                var endTime = $('#' + formId + ' #session_end_time').val();
                checkSessionAvailable(formId);
//                if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime)))
//                {
//                    $('#' + formId + ' #end_time_display').html("End time should be greater than start time");
//                    $('#' + formId + ' #session_end_time').val('');
//                }
//                else if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0))
//                {
//                    $('#' + formId + ' #end_time_display').html("Start time and end time cannot be same");
//                    $('#' + formId + ' #session_end_time').val('');
//                } else if (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + start_time) == 0) {
//                    $('#' + formId + ' #end_time_display').html("End time cannot be same as start time.");
//                    $('#' + formId + ' #session_end_time').val('');
//                } else {
//                    $('#' + formId + ' #end_time_display').html("");
//                }
            }
        });
    };

    // Method to fetch and place edit form with data using ajax call
    var fetchDataForEdit = function (id) {
        console.log("function called");
        //$('.portlet-body').on('click', '.edit-form-link', function () {
        var session_id = id;
        var actionUrl = adminUrl + '/session-bookings/' + session_id + '/edit';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data)
            {
                $("#edit_form").html(data.form);

                $("#edit-session-bookings #status").select2();

                $("#edit-session-bookings #service_id").select2();
                $("#edit-session-bookings #service_id").find("option").eq(0).attr('disabled', 'disabled');

                $("#staff_list").html(data.staff_list);
                $("#edit-session-bookings #staff_id").select2();

                $("#machine_list").html(data.machine_list);
                $("#edit-session-bookings #machine_id").select2();

                $("#room_list").html(data.room_list);
                $("#edit-session-bookings #room_id").select2();

                handleDateTimePicker('edit-session-bookings', '');
                handleDatePicker();
                siteObjJs.validation.formValidateInit('#edit-session-bookings', handleAjaxRequest);

                $("#edit-session-bookings #staff_id").find("option").eq(0).attr('disabled', 'disabled');
                $("#edit-session-bookings #machine_id").find("option").eq(0).attr('disabled', 'disabled');
                $("#edit-session-bookings #room_id").find("option").eq(0).attr('disabled', 'disabled');

                var date = new Date($("#edit-session-bookings #session_date").val());
                var session_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

                var form_id = "edit-session-bookings"
                var staff_calender = "staff_calender";
                var machine_calender = "machine_calender";
                var room_calender = "room_calender";

                handleAvailabilityCalendar($('#edit-session-bookings #staff_id').val(), $("#center_id").val(), 1, staff_calender, session_date, form_id);
                if ($("#machine_id").val() != 9999999 && $("#machine_id").val() != null){
                    handleAvailabilityCalendar($('#edit-session-bookings #machine_id').val(), $("#center_id").val(), 2, machine_calender, session_date, form_id);
                }
                if ($("#room_id").val() != 9999999 && $("#room_id").val() != null){
                    handleAvailabilityCalendar($('#edit-session-bookings #room_id').val(), $("#center_id").val(), 3, room_calender, session_date, form_id);
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
        //});
    };

    var handleTable = function () {
        grid = new Datatable();
        grid.init({
            src: $('#todays-sessions-table'),
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
                    {data: 'first_name', name: 'first_name', orderable: false},
                    {data: 'center_name', name: 'center_name', orderable: false},
                    {data: 'session_date', name: 'session_date', orderable: false},
                    {data: 'service_name', name: 'service_name', searchable: false, orderable: false},
                    {data: 'start_time', name: 'start_time', orderable: false},
                    {data: 'end_time', name: 'end_time', orderable: false},
                    {data: 'session_comment', name: 'session_comment', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'action', name: 'action', orderable: false}
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
                    "url": adminUrl + "/view-todays-sessions/data",
                    "type": "GET"
                },
                "order": [
                    //[2, "asc"],
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
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
        var member_id = $('#customer_select').val();
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
        if (member_id != '') {
            form.append('member_id', member_id);
        } else {
            $("#customer_error").html("Please select Customer.");
            return false;
        }
        if (formId === 'edit-session-bookings') {
            form.append('_method', 'PUT');
        }
        // Session Start Time Validation
        var start_time = $('#' + formId + ' #config_session_start_time').val();
        var end_time = $('#' + formId + ' #config_session_end_time').val();

        var startTime = $('#' + formId + ' #session_start_time').val();
        var endTime = $('#' + formId + ' #session_end_time').val();
        var session_date = $('#' + formId + ' #session_date').val();

        var currentTime = new Date();
        var selected_time = new Date(session_date + " " + startTime);
        var diffHours = (selected_time - currentTime) / 1000 / 60;

        if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
            $('#' + formId + ' #start_time_display').html("Start time should be less than end time");
            return false;
        } else if (endTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
            $('#' + formId + ' #start_time_display').html("Start time and end time cannot be same");
            return false;
        } else if (Date.parse('01/01/2011 ' + end_time) - Date.parse('01/01/2011 ' + startTime) == 0) {
            $('#' + formId + ' #start_time_display').html("Start Time cannot be same as end time.");
            return false;
        } else {
            $('#' + formId + ' #start_time_display').html("");
        }


        // Session End time Validation
        if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) < Date.parse('01/01/2011 ' + startTime))) {
            $('#' + formId + ' #end_time_display').html("End time should be greater than start time");
            return false;
        } else if (startTime != '' && (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + startTime) == 0)) {
            $('#' + formId + ' #end_time_display').html("Start time and end time cannot be same");
            return false;
        } else if (Date.parse('01/01/2011 ' + endTime) - Date.parse('01/01/2011 ' + start_time) == 0) {
            $('#' + formId + ' #end_time_display').html("End time cannot be same as start time.");
            return false;
        } else {
            $('#' + formId + ' #end_time_display').html("");
        }

        $(".submit_session").attr("disabled", true);

        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: actionType,
                    data: form,
                    processData: false,
                    contentType: false,
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

                        //reload the data in the datatable
                        //grid.getDataTable().ajax.reload();

//                        if ($("#session_notification_flag").length > 0) {
//                            $("#edit_form").html("");
//                        } else {
                        $('.btn-collapse').trigger('click');
                        //}

                        if (data.status == "success") {
                            //$("#customer_select").select2('val', '');
                            $("#package_id").select2('val', '');
                            $('#fullcalendar').fullCalendar('refetchEvents');
                        }

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

    var handleCalendar = function (customerId) {
//        if (session_page_type == "notification") {
//            var actionUrl = adminUrl+"data";
//        } else {
//            var actionUrl = "session-bookings/data";
//        }

        var actionUrl = adminUrl + "/session-bookings/data";

        $('#fullcalendar').fullCalendar({
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            "header": {
                "left": "prev title next",
                //"center": "prev title next",
                "right": "today,month,agendaWeek,agendaDay"
            },
            "timeFormat": 'h:mm a',
            "firstDay": 1,
            "minTime": $("#minTime").val(),
            "maxTime": $("#maxTime").val(),
            "nowIndicator": true,
            "locale": "nl",
            "weekNumbers": true,
            "selectable": true,
            "defaultView": "month",
            "height": "auto",
            "aspectRatio": "1",
            slotDuration: "00:30:01",
            "eventClick": function (event, jsEvent, view) {
                // Sessions edit for only those users who created them
                if ($("#logged_in_user_id").val() == event.created_by) {
                    $("#edit_form").html("");
                    var img_tag = "<img src='" + assetsUrl + "/loading-spinner-default.gif' class='loading_image'/>";
                    $("#edit_form").html(img_tag);
                    fetchDataForEdit(event.id);
                    $("#customer_select").select2("val", event.member_id); //set the value
                    /*$("#customer_select").select2({
                     allowClear: true,
                     placeholder: $(this).attr('data-label-text'),
                     width: null
                     });*/
                } else {
                    Metronic.alert({
                        type: 'danger',
                        message: "You are not allowed to edit session.",
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                    return false;
                }
            },
            "events": function (start, end, timezone, callback) {
                $.ajax({
                    url: actionUrl,
                    dataType: 'json',
                    type: "POST",
                    "data": {customer_id: customerId},
                    success: function (data) {
                        callback(data);
                    }
                });
            },
            eventRender: function (event, element) {
                element.attr('title', event.title);
                element.find('span.fc-title').html(element.find('span.fc-title').text());
                element.find('div.fc-title').html(element.find('div.fc-title').text());
            }
        });
    };

    var handleAvailabilityCalendar = function (id, centerId, flag, div_id, session_date, form_id) {
        $('#' + form_id + ' #' + div_id).fullCalendar('destroy');
        $('#' + form_id + ' #' + div_id).fullCalendar({
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            defaultView: 'agendaDay',
            defaultDate: session_date,
            editable: false,
            selectable: true,
            eventLimit: true, // allow "more" link when too many events
//            allDay: false,
            allDaySlot: false,
            header: {
                left: 'prev,next today',
                center: 'title',
//                right: 'agendaDay'
            },
            "minTime": $("#minTime").val(),
            "maxTime": $("#maxTime1").val(),
            "height": "100",
            "aspectRatio": "4",
            views: {
                agendaTwoDay: {
                    type: 'agenda',
                    // views that are more than a day will NOT do this behavior by default
                    // so, we need to explicitly enable it
                    groupByResource: true
                }
            },
            "resources": function (callback) {
                $.ajax({
                    url: "session-bookings/fetch-resource",
                    dataType: 'json',
                    type: "POST",
                    cache: false,
                    "data": {id: id, center_id: centerId, flag: flag},
                    success: function (data) {
                        callback(data);
                    }
                });
            },
            "events": function (start, end, timezone, callback) {
                $.ajax({
                    url: "session-bookings/fetch-availability",
                    dataType: 'json',
                    type: "POST",
                    "data": {id: id, center_id: centerId, date: session_date, flag: flag},
                    success: function (data) {
                        callback(data);
                        $('#' + div_id).find('.fc-event-container').next().css('width', '30% !important');
                    }
                });
            },
            eventAfterRender: function (event, element) {
//                element.attr('title', event.title);
//                element.find('span.fc-title').html(element.find('span.fc-title').text());
//                $('#' + div_id).find('.fc-event-container').css('width', '30% !important');
//                $('#staff_calender').find('.fc-event-container').next().css('width', '30% !important');
//
            }

        });
    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleTable();
            if ($('#customer_select').length > 0 && $('#customer_select').val() != "" && $('#customer_select').val() != 0) {
                var customer_id = $('#customer_select').val();
            } else {
                var customer_id = 0;
            }
            handleCalendar(customer_id);
            handleDatePicker();
            handleDateTimePicker('create-session-bookings', '');
            //fetchDataForEdit();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-session-bookings', handleAjaxRequest);

        }

    };
}();
