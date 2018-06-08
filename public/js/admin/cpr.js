siteObjJs.admin.cprJs = function () {

    // Initialize all the page-specific event listeners here.
    // var measurementRecordsTitle = '';
    var token = $('meta[name="csrf-token"]').attr('content');
    var initializeListener = function () {
        var logged_user_type = $("#logged_in_user_type").val();
        /*** Commenting this code as this can be managed using ACL logic ****/
        /*if (logged_user_type == 9) {
         $("fieldset").attr("disabled", "disabled");
         }*/

        if ($("#acl_flag").val() == 2) {
            $("fieldset").attr("disabled", "disabled");
        }

        $(".enter_comment").live("click", function (e) {
            var session_id = $(this).attr("id");
            var session_id = session_id.substring(session_id.lastIndexOf("_") + 1, session_id.length);
            var member_id = $("#member_id").val();
            var user_id = $("#logged_in_user_id").val();
            var package_id = $("#package_id").val();
            var logged_in_user_type = $("#logged_in_user_type").val();
            var actionUrl = 'escalation-matrix/get-comment';
            $(".ath_comment_error").css("display", "none");
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {session_id: session_id, member_id: member_id, user_id: user_id, package_id: package_id},
                success: function (data)
                {
                    $("#ath_comment").val(data.ath_comment);
                    $("#session_programme_record_session_id").val(session_id);
                    if (logged_in_user_type == 9) {
                        $(".submit_ath_comment").css('display', 'block');
                    } else {
                        $(".submit_ath_comment").css('display', 'none');
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
        });

        $(".verify_otp").live("click", function (e) {
            var id = $(this).attr("id");
            bootbox.confirm({
                buttons: {confirm: {label: 'CONFIRM'}},
                message: 'After confirming OTP will be sent to customers mobile number.',
                callback: function (result) {
                    if (result === false) {
                        return;
                    } else {
                        $("span.otp_error").html("Please Enter Otp.")
                        $("span.otp_error").css("display", "none");
                        $("#otp").val("");
                        $("#session_programme_record").val(id);
                        sendOtp($("#member_id").val(), id);
                        showOtpPopup();
                    }
                }
            });
        });

        $(".refresh-session-record").live("click", function (e) {
            var id = $(this).attr("id");
            var session_record_id = id.substring(id.lastIndexOf("-") + 1, id.length);
            updateServiceExecutionStatus(session_record_id, 'refresh');
            handleSessionTable();
        });

        $(".retry-clm-execution-call").live("click", function (e) {
            var id = $(this).attr("id");
            id = id.substring(id.lastIndexOf("-") + 1, id.length);
            //handleClmServiceExecution(id);
            var session_id = $("#session_id" + id).val();
            var member_id = $("#customer_select").val();
            var before_weight = $("#before_weight" + id).val();
            var after_weight = $("#after_weight" + id).val();
            var therapist = $("#therapist" + id).val();
            var actionUrl = "clm-service-execution";
            var icon = "check";
            var messageType = "success";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {member_id: member_id, session_id: session_id, before_weight: before_weight, after_weight: after_weight, therapist: therapist, member_session_record_id: id},
                success: function (data) {
                    updateServiceExecutionStatus(id, 'retry');
                    handleSessionTable();
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

        $(".service_execution").live("click", function (e) {
            var id = $(this).attr("id");
            var session_id = $("#session_id" + id).val();
            var member_id = $("#customer_select").val();
            var before_weight = $("#before_weight" + id).val();
            var after_weight = $("#after_weight" + id).val();
            var therapist = $("#therapist" + id).val();
            var actionUrl = "clm-service-execution";
            var icon = "check";
            var messageType = "success";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {member_id: member_id, session_id: session_id, before_weight: before_weight, after_weight: after_weight, therapist: therapist, member_session_record_id: id},
                success: function (data) {
                    $("p.service_execution_" + id).html("");
                    $("p.service_execution_" + id).html('<span>Pending</span><a style="margin-left: 10px;" id="refresh-clm-execution-' + id + '" href="javascript:;" class="btn btn-xs default refresh-session-record" title="refresh"><i class="fa fa-refresh"></i></a>');
                    if (data.status === "error") {
                        icon = "times";
                        messageType = "danger";
                    }
                    Metronic.alert({
                        type: messageType,
                        message: data.message,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: 25
                    });

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

        $(".submit_otp").live("click", function (e) {
            if ($("#otp").val() == "") {
                $("span.otp_error").html("Please Enter Otp.")
                $("span.otp_error").css("display", "block");
                return false;
            } else {
                $("span.otp_error").html("")
                $("span.otp_error").css("display", "none");
            }
            var otp = $("#otp").val();
            var member_id = $("#member_id").val();
            var otp_id = $("#otp_id").val();
            var id = $("#session_programme_record").val();
            var actionUrl = "verify-otp";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {member_id: member_id, otp: otp, otp_id: otp_id, session_programme_id: id},
                success: function (data) {
                    if (data.success == "success") {
                        // Otp verified successfully
                        $('#showVerifyOtpPopup').modal('toggle');
                        $("p.session_wrapper" + id).html("<span id=" + id + "'>Verified<span>");
                    } else {
                        //error while verifying otp
                        $("span.otp_error").css("display", "block");
                        $("span.otp_error").html(data.message);
                        $("#otp").val("");
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
        });

        $("#ath_comment").live("keypress", function (e) {
            var ath_comment = $(this).val();
            if (ath_comment == "") {
                $(".ath_comment_error").css("display", "block");
            } else {
                $(".ath_comment_error").css("display", "none");
            }
        });

        $(".submit_ath_comment").live("click", function (e) {
            var session_id = $("#session_programme_record_session_id").val();
            var member_id = $("#member_id").val();
            var user_id = $("#logged_in_user_id").val();
            var ath_comment = $("#ath_comment").val();
            var actionUrl = 'escalation-matrix/add-comment';
            if (ath_comment != "") {
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {session_id: session_id, member_id: member_id, user_id: user_id, ath_comment: ath_comment},
                    success: function (data)
                    {
                        $('#myModal').modal('toggle');
                        $("tr#session_program_record_summary" + session_id + " td").children("p").remove();
                        $("#athcomment_" + session_id).before("<p style='float:left;width:50%;word-wrap:break-word;'><b>ATH Comment : </b>" + ath_comment + "</p>");
                        $("#athcomment_" + session_id).removeAttr("style");
                        $("#athcomment_" + session_id).css("float", "right");
                        $("#athcomment_" + session_id).html('<i class="fa fa-pencil"></i>');
                        $("#athcomment_" + session_id).removeClass('red').addClass("yellow-gold");
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
            } else {
                $(".ath_comment_error").css("display", "block");
                $("#ath_comment").focus();
            }
        });

        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#recorded_date").select2('val', '');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');

            $("#" + formId).find("input[type=text], input[type=file], textarea").not(".skip-date, [disabled=disabled]").val("");
            if (formId == 'create-measurement-record') {
                $("#measurement_record_table_view").show();
                $(".measurement-record-form-btn").show();
                $("#measurement_record_form_view").hide();
            }

            $("#" + formId).find("#other").val('').parent().hide();
            $("#" + formId).find("#epilepsy").val('').parent().hide();

            $("#" + formId).find("#other_error").html('');
            $("#" + formId).find("#epilepsy_error").html('');

            var checkboxObj = $("#" + formId).find('input[type="checkbox"]');
            checkboxObj.prop("checked", false);

            checkboxObj.parents('span').removeClass('checked');
            checkboxObj.parents('.checkbox-container').siblings().find('span').removeClass("checked");

            var radioObj = $("#" + formId).find('input[type="radio"]');
            radioObj.prop("checked", false);

            radioObj.parents('span').removeClass('checked');
            radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");
        });

        $('.btn-expand-form-measurement').click(function () {
            $("#measurement_record_table_view").hide();
            $(".measurement-record-form-btn").hide();
            $("#measurement_record_form_view").show();
        });

        //on change of center show members of that center
        $('body').on('change', '#center_select', function (e) {
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
                        if (customer_id != 0 || customer_id != '') {
                            //handleTable('recommendation-table', selectedVal);
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
        // On change of Customer display CPR form
        $('body').on('change', '#customer_select', function (e) {
            var currentUrl = document.location.href;
            var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
            if (lastId == "cpr") {
                actionUrl = "cpr/cpr-ajax";
            } else {
                actionUrl = "cpr-ajax";
            }

            if ($(this).val() != 0) {
                // Call Ajax function to display CPR Form
                var member_id = $(this).val();
                var actionUrl = actionUrl;
                var icon = "check";
                var messageType = "success";
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {member_id: member_id},
                    beforeSend: function ()
                    {
                        $(".cpr-ajax-response").html("");
                        var img_tag = "<div style='text-align:center;'><img src='" + assetsUrl + "/loading-spinner-default.gif' class='loading_image'/></div>";
                        $(".cpr-ajax-response").html(img_tag);
                    },
                    success: function (data)
                    {
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                            Metronic.alert({
                                type: 'danger',
                                message: "Technical error occured.",
                                container: $('#ajax-response-text'),
                                place: 'prepend',
                                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                            });
                            return false;
                        }
                        if (data.latest_session_id == 0) {
                            var url = adminUrl + "/cpr";
                            window.location.href = url;

                            $(".cpr-ajax-response").html("");
                            $(".cpr-ajax-response").html(data.form);
                            $("div.radio-list").css("padding-left", "20px");
                            handleDatepicker();
                            handleTable();
                            setTimeout(checkBcaDate(), 200);
                            setTimeout(handleBcaRecordTable(), 400);
                            setTimeout(handleMeasurementTable(), 600);
                            if (siteObjJs.admin.cprJs.sessionId != 0) {
                                setTimeout(handleSessionTable(), 800);
                            }
                            setTimeout(handleDietaryAssessmentTable(), 1000);
                            setTimeout(handleFitnessAssessmentTable(), 1200);
                            setTimeout(handleMedicalAssessmentTable(), 1400);
                            setTimeout(handleSkinHairAnalysisTable(), 1600);
                            setTimeout(handleReviewFitnessActivityTable(), 1600);
                            setTimeout(handleMeasurementRecordsTable(), 1800);
                            //bind the validation method to 'add' form on load

                            var currentUrl = document.location.href;
                            var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
                            if (currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length) == "undefined") {
                                window.history.pushState("object or string", "Title", "cpr");
                            }
                            if ($("#session_id").val() != 0 && lastId == "cpr" && typeof ($("#session_id").val()) !== "undefined") {
                                window.history.pushState("object or string", "Title", "cpr/" + $("#session_id").val());
                            }
                            var logged_user_type = $("#logged_in_user_type").val();
                            if (logged_user_type == 9) {
                                $("fieldset").attr("disabled", "disabled");
                            }
                            siteObjJs.validation.formValidateInit('#create-cpr', handleCPRRecords);
                        } else {
                            var url = adminUrl + "/cpr/" + data.latest_session_id;
                            window.location.href = url;
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
            } else {
                //$('#member-activity-log-table-body').empty("");
            }
        });

        $('body').on('click', '.calculate-weight', function (e) {
            var session_id = $("#session_id").val();
            var package_id = $("#package_id").val();
            var member_id = $("#member_id").val();
            var actionUrl = 'store-session-records-summary';
            var icon = "check";
            var messageType = "success";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {'member_id': member_id, 'session_id': session_id, 'package_id': package_id, 'isForceAction': 1, 'recorded_date': $('#session_date').val()},
                success: function (data)
                {
                    //data: return data from server
                    if (data.status === "error")
                    {
                        icon = "times";
                        messageType = "danger";
                    }
                    sessionGrid.getDataTable().ajax.reload();
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

        $('#bca_csv_file').bind('change', function (e) {
            $("#bca_csv_file-error").html('');
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.cprJs.maxFileSize;
                    $('#csv-file-error').text(error);
                    return false;
                }

                var ext = $('#bca_csv_file').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['csv']) == -1) {
                    var error = siteObjJs.admin.cprJs.csvMimes;
                    $('#csv-file-error').text(error);
                    return false;
                } else
                {
                    $('#csv-file-error').text('');
                }

            }

        });

        $('#bca_image').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            $("#bca_image-error").html('');
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.cprJs.maxFileSize;
                    $('#bca-image-error').text(error);
                    return false;
                }

                var ext = $('#bca_image').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.cprJs.mimes;
                    $('#bca-image-error').text(error);
                    return false;
                } else {
                    $('#bca-image-error').text('');
                }

            }

        });

        $('body').on('click', '.add-row-btn', function (e) {
            var rowCount = $("#row_count").val();
            var newRowCount = ++rowCount;
            var actionUrl = 'cpr/get-new-row';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {row_count: newRowCount},
                success: function (data)
                {
                    var rowCount = $('#slimming_programme_recored-table tr').length;
                    if (rowCount > 0) {
                        $('#slimming_programme_recored-table tr:last').after(data.form);
                    } else {
                        $('#slimming_programme_recored-table').append(data.form);
                    }
                    $("#row_count").val(newRowCount);
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

        $('body').on('click', 'input[type=radio][name=smoking]', function (e) {
            var value = $(this).val();
            if (0 == value) {
                $(".smoking_frequency_div").css('display', 'none');
            }
            if (1 == value) {
                $(".smoking_frequency_div").show();
            }
        });

        $('body').on('click', 'input[type=radio][name=alcohol]', function (e) {
            var value = $(this).val();
            if (0 == value) {
                $(".alcohol_frequency_div").hide();
            }
            if (1 == value) {
                $(".alcohol_frequency_div").show();
            }
        });

        $('#bca_csv_file').bind('change', function (e) {
            //this.files[0].size gets the size of your file.

            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.memberProfileImageJs.maxFileSize;
                    $('#csv-file-error').text(error);
                    return false;
                }

                var ext = $('#bca_csv_file').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['csv']) == -1) {
                    var error = siteObjJs.admin.memberProfileImageJs.csvMimes;
                    $('#csv-file-error').text(error);
                    return false;
                } else {
                    $('#csv-file-error').text('');
                }
                handleCsvData();
            }

        });

        // dynamic calculations
        $("#height,#waist,#weight").on('change', function () {
            var height = $('#height').val();
            var waist = $('#waist').val();
            var weight = $('#weight').val();
            var heightInMeter = parseFloat(height) / 100;
//for whtr
            var WHtR = (parseFloat(waist / height) * 100);
            if (WHtR != '' && !isNaN(WHtR)) {
                $('#whtr').val(WHtR.toFixed(2)).prop('readonly', true);
            } else {
                $('#whtr').val('').prop('readonly', true);
            }
//for bmi
            var bmi = parseFloat(weight) / (heightInMeter * heightInMeter);
            if (bmi != '' && !isNaN(bmi)) {
                $('#bmi').val(bmi.toFixed(2)).prop('readonly', true);
            } else {
                $('#bmi').val('').prop('readonly', true);
            }
        });

        $("#programme_booked,#programme_needed").on('change', function () {
            var programme_booked = parseFloat($('#programme_booked').val());
            var programme_needed = parseFloat($('#programme_needed').val());
            var needGap = programme_needed - programme_booked;
            if (needGap != '' && !isNaN(needGap)) {
                $('#need_gap').val(needGap.toFixed(2)).prop('readonly', true);
            } else {
                $('#need_gap').val('').prop('readonly', true);
            }
        });

//        $.validator.addMethod("require_from_group", function (value, element, options) {
//            var numberRequired = options[0];
//            var selector = options[1];
//            //Look for our selector within the parent form
//            var validOrNot = $(selector, element.form).filter(function () {
//                // Each field is kept if it has a value
//                return $(this).val();
//                // Set to true if there are enough, else to false
//            }).length >= numberRequired;
//            if (!$(element).data('being_validated')) {
//                var fields = $(selector, element.form);
//                fields.data('being_validated', true);
//                // .valid() means "validate using all applicable rules" (which
//                // includes this one)
//                fields.valid();
//                fields.data('being_validated', false);
//            }
//            return validOrNot;
//        }, $.validator.format("Please fill out at least {0} of these fields."));
//
        $.validator.addClassRules("min-one-required", {
            require_from_group: [1, ".min-one-required"]
        });
    };


    var handleClmServiceExecution = function (id) {


    };

    // Function to send otp on customer's mobile number
    var sendOtp = function (member_id, id) {
        var actionUrl = "send-otp";
        var before_weight = $("#before_weight" + id).val();
        var after_weight = $("#after_weight" + id).val();
        //return false;
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: member_id, before_weight: before_weight, after_weight: after_weight},
            success: function (data)
            {
                $("#otp_id").val(data.otp_id);
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

    var showOtpPopup = function () {
        $('#showVerifyOtpPopup').modal('toggle');
    }

    var handleCsvData = function () {
        var form = new FormData();

        var bca_csv_file = $('#bca_csv_file')[0].files[0];
        if (bca_csv_file) {
            if (bca_csv_file.size > 2097152) {
                var error = siteObjJs.admin.cprJs.maxFileSize;
                $('#csv-file-error').text(error);
                return false;
            }
            var ext = $('#bca_csv_file').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['csv']) == -1) {
                var error = siteObjJs.admin.cprJs.csvMimes;
                $('#csv-file-error').text(error);
                return false;
            }
            form.append('bca_csv_file', bca_csv_file);
        }

        form.append('member_id', $("#member_id").val());
        form.append('package_id', $("#package_id").val());

        var actionUrl = 'upload-csv';
        $.ajax(
                {
                    url: actionUrl,
                    type: "POST",
                    data: form,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        $("#recorded_date").val(data[0].reg_date);
                        $("#basal_metabolic_rate").val(data[0].bmr);
                        $("#body_mass_index").val(data[0].bmi);
                        $("#fat_weight").val(data[0].fat_weight);
                        $("#fat_percent").val(data[0].fat_percent);
                        $("#lean_body_mass_weight").val(data[0].lean_body_mass_weight);
                        $("#lean_body_mass_percent").val(data[0].lean_body_mass_percent);
                        $("#water_weight").val(data[0].water_weight);
                        $("#water_percent").val(data[0].water_percent);
                        $("#target_weight").val(data[0].target_weight);
//                        $("#target_fat_percent").val(data[0].target_fat_percent);
                        $("#visceral_fat_level").val(data[0].visceral_fat_level);
                        $("#protein").val(data[0].protein);
                        $("#mineral").val(data[0].mineral);
//                        $("#visceral_fat_area").val(data[0].visceral_fat_area);

                        //data: return data from server
                        if (data.status === "error")
                        {
                        }

                        //reload the data in the datatable
                        Metronic.alert({
                            type: '',
                            icon: '',
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

    var handleCPRRecords = function () {
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';
        var input_session = document.createElement('input');
        input_session.type = 'hidden';
        input_session.value = $("#session_id").val();
        input_session.name = 'session_id';
        formElement.append(input_member);
        formElement.append(input_session);
        var actionUrl = formElement.attr("action");
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
                        $('.btn-collapse').trigger('click');
                        //reload the data in the datatable
                        handleTable();
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

    var handleTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/data";
        } else {
            var actionUrl = "data";
        }
        var session_id = 0;
        if (typeof $("#session_id").val() !== "undefined") {
            var session_id = $("#session_id").val();
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            type: 'POST',
            data: {"session_id": session_id, "member_id": $("#member_id").val()},
            success: function (response)
            {
                var todaySessionRecord = [];

                if (response.todaySessionRecord.length > 0) {
                    todaySessionRecord = response.todaySessionRecord[0];
                }
                response = response[0];
                $('#client_id').val(response.member.crm_customer_id).prop('readonly', true);
                $('#package_no').val(response.member_package.id).prop('readonly', true);
                $('#member_package_number').val(response.member_package.crm_package_id).prop('readonly', true);
                $('#first_name').val(response.member.first_name);
                $('#last_name').val(response.member.last_name);

                var radioObj = $('input[type="radio"][name="gender"][value="' + response.member.gender + '"]');
                radioObj.prop("checked", true);

                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                if (response.member.date_of_birth != '0000-00-00' && response.member.date_of_birth != null) {
                    $('#dob').val(response.member.date_of_birth).prop('readonly', true);
                    if ('' != $("#dob").val()) {
                        var dob = new Date($("#dob").val());
                        var today = new Date();
                        var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));

                        $('#age').val(age).prop('readonly', true);
                    }

                } else {
                    $('#dob').val('').prop('readonly', true);
                    $('#age').val('').prop('readonly', true);
                }

                $('#height').val(response.member_package.height);
                $('#waist').val(response.member_package.waist);
                var WHtR = (parseFloat(response.member_package.waist / response.member_package.height) * 100);
                if (WHtR != '' && !isNaN(WHtR)) {
                    $('#whtr').val(WHtR.toFixed(2)).prop('readonly', true);
                } else {
                    $('#whtr').val('').prop('readonly', true);
                }

                $('#weight').val(response.member_package.weight);
                var heightInMeter = parseFloat(response.member_package.height) / 100;
                var bmi = parseFloat(response.member_package.weight / (heightInMeter * heightInMeter));
                if (bmi != '' && !isNaN(bmi)) {
                    $('#bmi').val(bmi.toFixed(2)).prop('readonly', true);
                } else {
                    $('#bmi').val('').prop('readonly', true);
                }

                $('#address').val(response.member.address);
                $('#profession').val(response.member.profession);
                $('#alternate_phone_number').val(response.member.alternate_phone_number);
                $('#mobile').val(response.member.mobile_number).prop('readonly', true);
                $('#email').val(response.member.email);
                $('#existing_medical_problem').val(response.member.existing_medical_problem);
                $('#category_code').val(response.member.category_code);
                $('#services_to_be_avoided').val(response.member.services_to_be_avoided);
                $('#family_physician_name').val(response.member.family_physician_name);
                $('#family_physician_number').val(response.member.family_physician_number);
                $('#therapies').val(response.member.therapies);
                $('#programme_booked').val(response.member_package.programme_booked);
                $('#programme_needed').val(response.member_package.programme_needed);
                var needGap = parseFloat(response.member_package.programme_needed - response.member_package.programme_booked);
                if (needGap != '' && !isNaN(needGap)) {
                    $('#need_gap').val(needGap.toFixed(2)).prop('readonly', true);
                } else {
                    $('#need_gap').val('').prop('readonly', true);
                }

                $('#programme_re_booked').val(response.member_package.programme_re_booked);
                $('#conversion').val(response.member_package.conversion);
                $('#remarks').val(response.member_package.remarks);
                $('#programme_booked_rs').val(response.member_package.total_payment).prop('readonly', true);
                $('#programme_booked_by').val(response.member_package.programme_booked_by).prop('readonly', true);
                $('#payment_made').val(response.member_package.payment_made).prop('readonly', true);
                //constant values used in all Forms
                $('#package_id').val(response.member_package.id);
                $('#member_id').val(response.member.id);
                $('#session_date').val(response.session_date);
// if session record is present for current record

                if ($.isEmptyObject(todaySessionRecord) == false) {
                    $('#bp').val(todaySessionRecord.bp).prop('disabled', true);
                    $('.session-date').val(todaySessionRecord.recorded_date).prop('disabled', true);
                    $('#before_weight').val(todaySessionRecord.before_weight).prop('disabled', true);
                    $('#after_weight').val(todaySessionRecord.after_weight).prop('disabled', true);
                    $('#a_code').val(todaySessionRecord.a_code).prop('disabled', true);
                    $('#diet_and_activity_deviation').val(todaySessionRecord.diet_and_activity_deviation).prop('disabled', true);
                    $('#create-session-records button[type="submit"]').prop('disabled', true);
                    $('#ajax-response-text-session').removeClass('hidden');
                } else {
                    $(".session-date").val(response.session_date).prop('readonly', true);
                }


            }
        });
    };

    var handleCprFormBca = function (formElement) {
        $('#csv-file-error').text('');
        $('#bca-image-error').text('');
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';
        var input_session = document.createElement('input');
        input_session.type = 'hidden';
        input_session.value = $("#session_id").val();
        input_session.name = 'session_id';
        var input_package = document.createElement('input');
        input_package.type = 'hidden';
        input_package.value = $("#package_id").val();
        input_package.name = 'package_id';

        formElement.append(input_member);
        formElement.append(input_session);
        formElement.append(input_package);

        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        var bca_image = $('#bca_image')[0].files[0];

        if (bca_image) {
            if (bca_image.size > 2097152) {
                var error = siteObjJs.admin.cprJs.maxFileSize;
                $('#bca-image-error').text(error);
                return false;
            }
            var ext = $('#bca_image').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                var error = siteObjJs.admin.cprJs.mimes;
                $('#bca-image-error').text(error);
                return false;
            }
            form.append('bca_image', bca_image);
        }

        form.append('_token', token);
        if ($('input[name=_method]').val()) {
            form.append('_method', $('input[name=_method]').val());
        }

        $.ajax(
                {
                    url: actionUrl,
                    type: actionType,
                    data: form,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        if (data.month > 0 || data.month === '') {
//                            $(".sw-btn-prev").attr("disabled", "disabled");
//                            $(".sw-btn-next").attr("disabled", "disabled");
                            $(".step-anchor li").removeClass("done");
                            $("#bca_alert").html('');
                            $(".bca-alert").hide();
                            $("#bca_alert_msg").html('Please update BCA record.');
                        } else if (data.month == 0) {
                            $(".bca-alert").show();
                            $("#bca_alert").html("Please note you have already filled BCA for this month!");
                            $("#bca_alert_msg").html('');
                        }
                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }
                        $("#bca_csv_file").val('');
                        $("#bca_image").val('');
                        $(".sw-btn-prev").removeProp("disabled");
                        $(".sw-btn-next").removeProp("disabled");
                        $(".step-anchor li:first").addClass("done");
                        //Empty the form fields
                        formElement.find("input[type=text], textarea").val("");
                        //trigger cancel button click event to collapse form and show title of add page
                        $('.btn-collapse').trigger('click');
                        //reload the data in the datatable
                        bcaGrid.getDataTable().ajax.reload();
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

    var handleCprFormMeasurment = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';
        var input_session = document.createElement('input');
        input_session.type = 'hidden';
        input_session.value = $("#session_id").val();
        input_session.name = 'session_id';
        var input_package = document.createElement('input');
        input_package.type = 'hidden';
        input_package.value = $("#package_id").val();
        input_package.name = 'package_id';

        formElement.append(input_member);
        formElement.append(input_session);
        formElement.append(input_package);

        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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
                        $("#recorded_date").select2('val', '');
                        //reload the data in the datatable
                        measurmentGrid.getDataTable().ajax.reload();
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

    var handleCprFormSesssionRecords = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';
        var input_session = document.createElement('input');
        input_session.type = 'hidden';
        input_session.value = $("#session_id").val();
        input_session.name = 'session_id';
        var input_package = document.createElement('input');
        input_package.type = 'hidden';
        input_package.value = $("#package_id").val();
        input_package.name = 'package_id';

        formElement.append(input_member);
        formElement.append(input_session);
        formElement.append(input_package);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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
                        sessionGrid.getDataTable().ajax.reload();
                        handleTable();
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

    var handleCprDietaryAssessmnet = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        var smokeValue = $('input[name=smoking]:checked').val();
        if (smokeValue == 1 && $("#smoking_frequency").val() == '') {
            $("#smoking_frequency-error").html('Please Enter Smoking Frequency');
            $("#smoking_frequency").focus();
            return false;
        } else {
            $("#smoking_frequency-error").html('');
        }

        var alcoholValue = $('input[name=alcohol]:checked').val();
        if (alcoholValue == 1 && !$("input[name='alcohol_frequency']:checked").val()) {
            $("#alcohol_frequency-error").html('Please Select Alcohol Frequency');
            $("#diet_total_calories").focus();
            return false;
        } else {
            $("#alcohol_frequency-error").html('');
        }
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

                        var smokeValue = $('input[name=smoking]:checked').val();
                        var alcoholValue = $('input[name=alcohol]:checked').val();
                        if (smokeValue == 0) {
                            $("#smoking_frequency").val('');
                        }
                        if (alcoholValue == 0) {
                            $("input[name='alcohol_frequency']:checked").removeProp('checked');
                            $("input[name='alcohol_frequency']").prev().removeClass('checked');
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

    var handleCprFitnessAssessmnet = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

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

    var handleCprMedicalAssessmnet = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();

        if ($('#field_25').prop('checked')) {
            if ($("#other").val() == '') {
                $('#other_error').html('Please Enter Other Medical Problem');
                if ($('#current_associated_mediacl_prob').hasClass('collapsed')) {
                    $('#current_associated_mediacl_prob').click();
                }
                return false;
            } else {
                $('#other_error').html('');
            }
        }

        if ($('#field_9').prop('checked')) {
            if ($("#epilepsy").val() == '') {
                $('#epilepsy_error').html('Please Select Date of Last Attack (Epilepsy)');
                if ($('#current_associated_mediacl_prob').hasClass('collapsed')) {
                    $('#current_associated_mediacl_prob').click();
                }
                return false;
            } else {
                $('#epilepsy_error').html('');
            }
        }

        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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

    var handleCprSkinHairAnalysis = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();


        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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

    var handleCprReview = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");

        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();

        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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

                        formElement.find("input[type=text], textarea").val("");
                        //trigger cancel button click event to collapse form and show title of add page
                        $('.btn-collapse').trigger('click');
                        //reload the data in the datatable
                        reviewGrid.getDataTable().ajax.reload();

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

    var handleMeasurementRecord = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");

        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();


        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: actionType,
                    data: formData,
                    success: function (data)
                    {

                        if (data.status == 'empty-error') {
                            icon = "times";
                            messageType = "danger";
                        } else {

                            $("#measurement_record_table_view").show();
                            $(".measurement-record-form-btn").show();
                            $("#measurement_record_form_view").hide();
                            handleMeasurementRecordsTable();
                            $("#" + formID).find("input[type=text], input[type=file], textarea").val("");
                        }

                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
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

    var handleMedicalreview = function (formElement) {

        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");

        var input_member = document.createElement('input');
        input_member.type = 'hidden';
        input_member.value = $("#member_id").val();
        input_member.name = 'member_id';
        var input_session = document.createElement('input');
        input_session.type = 'hidden';
        input_session.value = $("#session_id").val();
        input_session.name = 'session_id';

        formElement.append(input_member);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();


        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
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

                        formElement.find("input[type=text], textarea").val("");
                        //trigger cancel button click event to collapse form and show title of add page
                        $('.btn-collapse').trigger('click');
                        medicalReviewGrid.getDataTable().ajax.reload();
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

    var handleBcaRecordTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/get-bca-record";
        } else {
            var actionUrl = "get-bca-record";
        }
        $('#bca-record-table').dataTable().fnDestroy();
        bcaGrid = new Datatable();
        bcaGrid.init({
            src: $('#bca-record-table'),
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
                    {data: 'bca_image', name: 'bca_image'},
                    {data: 'recorded_date', name: 'recorded_date'},
                    {data: 'basal_metabolic_rate', name: 'basal_metabolic_rate'},
                    {data: 'fat_weight', name: 'fat_weight'},
                    {data: 'fat_percent', name: 'fat_percent', orderable: false, searchable: false},
                    {data: 'lean_body_mass_weight', name: 'lean_body_mass_weight', orderable: false, searchable: false},
                    {data: 'lean_body_mass_percent', name: 'lean_body_mass_percent', orderable: false, searchable: false},
                    {data: 'water_weight', name: 'water_weight', orderable: false, searchable: false},
                    {data: 'water_percent', name: 'water_percent', orderable: false, searchable: false},
                    {data: 'target_weight', name: 'target_weight', orderable: false, searchable: false},
                    {data: 'target_fat_percent', name: 'target_fat_percent', orderable: false, searchable: false},
                    {data: 'body_mass_index', name: 'body_mass_index', orderable: false, searchable: false},
                    {data: 'visceral_fat_level', name: 'visceral_fat_level', orderable: false, searchable: false},
                    {data: 'visceral_fat_area', name: 'visceral_fat_area', orderable: false, searchable: false, visible: false},
                    {data: 'mineral', name: 'mineral', orderable: false, searchable: false},
                    {data: 'protein', name: 'protein', orderable: false, searchable: false},
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
                    "url": actionUrl,
                    "type": "GET",
                    data: {"member_id": $("#member_id").val(), "package_id": $("#package_id").val()},
                }
            }
        });
        $('#data-search').hide();
    };

    var handleMeasurementTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/get-measurement-record";
        } else {
            var actionUrl = "get-measurement-record";
        }
        $('#measurements-table').dataTable().fnDestroy();
        measurmentGrid = new Datatable();
        measurmentGrid.init({
            src: $('#measurements-table'),
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
                    {data: 'recorded_date', name: 'recorded_date'},
                    {data: 'neck', name: 'neck'},
                    {data: 'chest', name: 'chest'},
                    {data: 'arms', name: 'arms', orderable: false, searchable: false},
                    {data: 'arm_right', name: 'arm_right', orderable: false, searchable: false},
                    {data: 'tummy', name: 'tummy', orderable: false, searchable: false},
                    {data: 'waist', name: 'waist', orderable: false, searchable: false},
                    {data: 'hips', name: 'hips', orderable: false, searchable: false},
                    {data: 'thighs', name: 'thighs', orderable: false, searchable: false},
                    {data: 'thighs_right', name: 'thighs_right', orderable: false, searchable: false},
                    {data: 'total_cm_loss', name: 'total_cm_loss', orderable: false, searchable: false},
                    {data: 'therapist_name', name: 'therapist_name', orderable: false, searchable: false},
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
                    "url": actionUrl,
                    "type": "GET",
                    data: {"member_id": $("#member_id").val(), "package_id": $("#package_id").val()},
                }
            }
        });
        $('#data-search').hide();
    };

    var updateServiceExecutionStatus = function (session_record_id, flag) {
        var actionUrl = "clm-update-service-execution-flag";
        var icon = "check";
        var messageType = "success";
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {session_record_id: session_record_id, flag: flag},
            success: function (data) {
                //console.log(data);
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

    var handleSessionTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr")
        {
            var actionUrl = "cpr/get-session-record";
        } else {
            var actionUrl = "get-session-record";
        }
        $('#session-record-table').dataTable().fnDestroy();
        sessionGrid = new Datatable();
        sessionGrid.init({
            src: $('#session-record-table'),
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
                    {data: 'bp', name: 'bp'},
                    {data: 'recorded_date', name: 'recorded_date'},
                    {data: 'before_weight', name: 'before_weight'},
                    {data: 'after_weight', name: 'after_weight'},
                    {data: 'a_code', name: 'a_code'},
                    {data: 'diet_and_activity_deviation', name: 'diet_and_activity_deviation'},
                    {data: 'therapist_id', name: 'therapist_id'},
                    {data: 'otp_verified', name: 'otp_verified'},
                    {data: 'service_executed', name: 'service_executed'},
                    {data: 'net_weight_loss', name: 'net_weight_loss', visible: false},
                    {data: 'net_weight_gain', name: 'net_weight_gain', visible: false},
                    {data: 'balance_programme_kg', name: 'diet_and_activity_deviation', visible: false},
                    {data: 'ath_comment', name: 'ath_comment', visible: false},
                    {data: 'session_id', name: 'session_id', visible: false},
                    {data: 'id', name: 'id', visible: false},
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    var displayLength = settings._iDisplayLength;
                    if ($("#session-record-table .dataTables_empty")[0]) {
                        $('.calculate-weight').hide();
                    } else {
                        $('.calculate-weight').show();
                    }
                    api.column(0, {page: 'current'}).data().each(function (group, i) {
                        recNum = ((page * displayLength) + i + 1);
                        $(rows).eq(i).children('td:first-child').html(recNum);
                    });
                    api.column(11, {page: 'current'}).data().each(function (group, i) {
                        if (group != '') {
                            var balance_programme_kg = group;
                            var net_weight_gain = api.column(11, {page: 'current'}).data()[i];
                            var net_weight_loss = api.column(10, {page: 'current'}).data()[i];
                            var ath_comment = api.column(13, {page: 'current'}).data()[i];
                            var session_id = api.column(14, {page: 'current'}).data()[i];
                            var id = api.column(15, {page: 'current'}).data()[i];
                            var html = '<tr role="row" class="weight-row session_program_record_summary" id="session_program_record_summary' + session_id + '"><td colspan="2"><b>Net Weight Loss : ' + net_weight_loss + '</b></td><td colspan="2"><b>Net Weight gain : ' + net_weight_gain + '</b></td><td colspan="2"><b>Balance (Kg) Programme : ' + balance_programme_kg + '</b></td>';

                            var logged_in_user_type = $("#logged_in_user_type").val();
                            if (logged_in_user_type == 9 && net_weight_loss < 1) {
                                if (ath_comment == null || ath_comment == "") {
                                    html = html + '<td colspan="4"><button type="button" id="athcomment_' + session_id + '" class="btn btn-info yellow-gold enter_comment" data-toggle="modal" data-target="#myModal" style="float:right;padding:10px 15px;">Comment</button></td></tr>';
                                } else {
                                    html = html + '<td colspan="4"><p style="float:left;width:50%;word-wrap:break-word;text-align:justify;"><b>ATH Comment : </b>' + ath_comment + '</p><button type="button" id="athcomment_' + session_id + '" class="btn btn-info yellow-gold enter_comment" data-toggle="modal" data-target="#myModal" style="float:right;"><i class="fa fa-pencil"></i></button></td></tr>';
                                }
                            } else if (logged_in_user_type == 4 || logged_in_user_type == 8) {
                                if (ath_comment == null) {
                                    html = html + '<td colspan="4"><b>ATH Comment : </b>NA</td></tr>';
                                } else {
                                    html = html + '<td colspan="4"><b>ATH Comment : </b> ' + ath_comment + '</td></tr>';
                                }
                            } else {
                                html = html + '<td colspan="4"></td>';
                            }
                            $(rows).eq(i).after(html);
                        }
                    });
                },
                'ordering': false,
                'searching': false,
                "ajax": {
                    "url": actionUrl,
                    "type": "GET",
                    data: {"member_id": $("#member_id").val(), "package_id": $("#package_id").val(), "session_id": $("#session_id").val()},
                }
            }
        });
        $('#data-search').hide();
    };

    var handleDatepicker = function () {
        if (!jQuery().datepicker) {
            return;
        }
        $(".bca-date").datepicker({
            autoclose: true,
            endDate: '+0d',
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            format: "yyyy-mm-dd"
        });

        $("#dob").datepicker({
            autoclose: true,
            endDate: '+0d',
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            format: "yyyy-mm-dd"

        }).on('changeDate', function (selected) {
            var age = calculateAge(selected.date);
            $("#age").val(age).prop('readonly', true);
        });

        $("#epilepsy").datepicker({
            autoclose: true,
            endDate: '+0d',
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            format: "yyyy-mm-dd"
        });

        $("#followup_date").datepicker({
            autoclose: true,
            startDate: '+0d',
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            format: "yyyy-mm-dd"
        });

        $(".review-date").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            endDate: '+0d',
        });

    };

    var calculateAge = function (birthday) { // birthday is a date
        var ageDifMs = Date.now() - birthday.getTime();
        var ageDate = new Date(ageDifMs); // miliseconds from epoch
        var age = Math.abs(ageDate.getUTCFullYear() - 1970);
        if (age == 0) {
            $("#dob-error").html("Please select valid Date of Birth");
            $("#dob").val('');
        } else if (age < 12) {
            $("#age-error").html("Person's age should be 12 years or more");
        } else {
            $("#dob-error").html("");
            $("#age-error").html("");
            return age;
        }
    }

    var checkBcaDate = function () {
        var stepNoVar = 0;
        if (typeof (window.localStorage.getItem('stepNoGlobal')) != 'undefined') {
            stepNoVar = window.localStorage.getItem('stepNoGlobal');
        }
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/check-bca-date";
        } else {
            var actionUrl = "check-bca-date";
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: $("#member_id").val(), package_id: $("#package_id").val()},
            success: function (data)
            {
                $("#check_bca_data_flag").val(data);
                var logged_in_user_type = $("#logged_in_user_type").val();
                if (logged_in_user_type == 9 || siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") {
                    initilizeSmartWizard(parseInt(stepNoVar));
                    window.localStorage.setItem('stepNoGlobal', 0);
                    if ((logged_in_user_type == 4 || logged_in_user_type == 8) && (data > 0 || data === '')) {
                        $("#bca_alert_msg").html('Please update BCA record.');
                        $("#bca_alert_common").val('Please update BCA record.');
                    }
                } else {
                    if (data > 0 || data === '') {
                        initilizeSmartWizard(1);
                        //$(".sw-btn-prev").attr("disabled", "disabled");
                        //$(".sw-btn-next").attr("disabled", "disabled");
                        $(".step-anchor li").removeClass("done");
                        $("#bca_alert").html('');
                        $(".bca-alert").hide();
                        $("#bca_alert_msg").html('Please update BCA record.');
                        $("#bca_alert_common").val('Please update BCA record.');
                    } else if (data == 0) {
                        initilizeSmartWizard(0);
                        $("#bca_alert_msg").html('');
                        $(".bca-alert").show();
                        $("#bca_alert").html("Please note you have already filled BCA for One month! ");
                    }
                }
                siteObjJs.validation.formValidateInit('#create-bca-records', handleCprFormBca);
                siteObjJs.validation.formValidateInit('#create-measurements-records', handleCprFormMeasurment);
                siteObjJs.validation.formValidateInit('#create-session-records', handleCprFormSesssionRecords);
                siteObjJs.validation.formValidateInit('#create-dietary-assessment', handleCprDietaryAssessmnet);
                siteObjJs.validation.formValidateInit('#create-fitness-assessment', handleCprFitnessAssessmnet);
                siteObjJs.validation.formValidateInit('#create-medical-assessment', handleCprMedicalAssessmnet);
                siteObjJs.validation.formValidateInit('#create-skin-hair-analysis', handleCprSkinHairAnalysis);
                siteObjJs.validation.formValidateInit('#create-review-fitness-activity-records', handleCprReview);
                siteObjJs.validation.formValidateInit('#create-measurement-record', handleMeasurementRecord);
                siteObjJs.validation.formValidateInit('#create-medical-review', handleMedicalreview);
            }
        });
    }

    var initilizeSmartWizard = function (stepNo) {
        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: stepNo,
//            selected: 7,
            theme: 'dots',
            transitionEffect: 'fade',
            showStepURLhash: false,
            keyNavigation: false,
            toolbarSettings: {
                toolbarPosition: 'top',
                toolbarExtraButtons: []
            },
            anchorSettings: {
                keyNavigation: false,
//                anchorClickable: true,
//                enableAllAnchors: true,
                markDoneStep: true, // add done css
                markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
                enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
            }
        });
        $('#smartwizard').smartWizard("reset");
        $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
            return true;
        });
        $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
            if (stepNumber == 1) {
                $('#step-2 .collapse.box-expand-form').trigger('click');
                if ($("#bca_alert_common").val() != "") {
                    $("#bca_alert_msg").html('Please update BCA record.');
                }
            }
            if (stepNumber == 2) {
                $('#step-3 .collapse.box-expand-form').trigger('click');
            }
            if (stepNumber == 3) {
                $('#step-4 .collapse.box-expand-form').trigger('click');
            }
        });
    }

    var handleDietaryAssessmentTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/fetch-dietary-assessment-data";
        } else {
            var actionUrl = "fetch-dietary-assessment-data";
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            type: 'POST',
            data: {"member_id": $("#member_id").val()},
            success: function (response)
            {
                $("#food_allergy").val(response.food_allergy);

                var radioObj = $('input[type="radio"][name="smoking"][value="' + response.smoking + '"]');
                radioObj.prop("checked", true);

                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                if (response.smoking == 0) {
                    $(".smoking_frequency_div").hide();
                    var radioObj = $('input[type="radio"][name="smoking_frequency"][value="' + response.smoking_frequency + '"]');
                    radioObj.prop("checked", true);
                    radioObj.parents('span').removeClass('checked');
                    radioObj.parents('.radio-container-smoking-frequency').siblings().find('span').removeClass("checked");

                } else {
                    $(".smoking_frequency_div").removeAttr('style');
                    var radioObj = $('input[type="radio"][name="smoking_frequency"][value="' + response.smoking_frequency + '"]');
                    radioObj.prop("checked", true);
                    radioObj.parents('span').addClass('checked');
                    //radioObj.parents('span').removeClass('checked');
                    //radioObj.parents('.radio-container-smoking-frequency').siblings().find('span').removeClass("checked");
                }

                $("#food_allergy").val(response.food_allergy);
                if (response.meals_per_day != 0) {
                    $("#meals_per_day").val(response.meals_per_day);
                } else {
                    $("#meals_per_day").val('');
                }

                if (response.food_habbit == "") {
                    var radioObj = $('input[type="radio"][name="food_habbit"][value="1"]');
                } else {
                    var radioObj = $('input[type="radio"][name="food_habbit"][value="' + response.food_habbit + '"]');
                }
                radioObj.prop("checked", true);
                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                $("#eat_out_per_week").val(response.eat_out_per_week);
                $("#fasting").val(response.fasting);
                $("#alcohol").val(response.alcohol);

                var radioObj = $('input[type="radio"][name="alcohol"][value="' + response.alcohol + '"]');
                radioObj.prop("checked", true);

                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                if (response.alcohol == 0) {
                    $(".alcohol_frequency_div").hide();
                    var radioObj = $('input[type="radio"][name="alcohol_frequency"][value="' + response.alcohol_frequency + '"]');
                    radioObj.prop("checked", true);

                    radioObj.parents('span').removeClass('checked');
                    radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");
                } else {
                    var radioObj = $('input[type="radio"][name="alcohol_frequency"][value="' + response.alcohol_frequency + '"]');
                    radioObj.prop("checked", true);

                    radioObj.parents('span').addClass('checked');
                    radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");
                    $(".alcohol_frequency_div").removeAttr('style');
                }

                if (response.diet_total_calories != 0) {
                    $("#diet_total_calories").val(response.diet_total_calories);
                } else {
                    $("#diet_total_calories").val('');
                }

                if (response.diet_cho != 0) {
                    $("#diet_cho").val(response.diet_cho);
                } else {
                    $("#diet_cho").val();
                }

                if (response.diet_protein != 0) {
                    $("#diet_protein").val(response.diet_protein);
                } else {
                    $("#diet_protein").val('');
                }

                if (response.diet_fat != 0) {
                    $("#diet_fat").val(response.diet_fat);
                } else {
                    $("#diet_fat").val('');
                }

                $("#create-dietary-assessment #remark").val(response.remark);
                $("#wellness_counsellor_name").val(response.wellness_counsellor_name);
            }
        });
    };

    var handleFitnessAssessmentTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/fetch-fitness-assessment-data";
        } else {
            var actionUrl = "fetch-fitness-assessment-data";
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            type: 'POST',
            data: {"member_id": $("#member_id").val()},
            success: function (response)
            {
                $("#static_posture").val(response.static_posture);
                $("#sit_and_reach_test").val(response.sit_and_reach_test);
                $("#shoulder_flexibility_right").val(response.shoulder_flexibility_right);
                $("#shoulder_flexibility_left").val(response.shoulder_flexibility_left);
                if (response.pulse != 0) {
                    $("#pulse").val(response.pulse);
                } else {
                    $("#pulse").val();
                }

                if (response.back_problem_test != 1) {
                    var radioObj = $('input[type="radio"][name="back_problem_test"][value="' + response.back_problem_test + '"]');
                    radioObj.prop("checked", true);

                    radioObj.parents('span').addClass('checked');
                    radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");
                }

                $("#current_activity_pattern").val(response.current_activity_pattern);
                $("#current_activity_type").val(response.current_activity_type);
                $("#current_activity_frequency").val(response.current_activity_frequency);
                $("#current_activity_duration").val(response.current_activity_duration);
                $("#create-fitness-assessment #remark").val(response.remark);

                if (response.home_care_kit != 0) {
                    var radioObj = $('input[type="radio"][name="home_care_kit"][value="' + response.home_care_kit + '"]');
                    radioObj.prop("checked", true);

                    radioObj.parents('span').addClass('checked');
                    radioObj.parents('.radio-container1').siblings().find('span').removeClass("checked");
                }


                $("#physiotherapist_name").val(response.physiotherapist_name);
            }
        });
    };

    var handleMedicalAssessmentTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/fetch-medical-assessment-data";
        } else {
            var actionUrl = "fetch-medical-assessment-data";
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            type: 'POST',
            data: {"member_id": $("#member_id").val()},
            success: function (response)
            {
                $("#biochemical_profile_div").html(response.biochemical_profile);
                if ($.inArray('25', response.current_associated_medical_problem) > -1) {
                    $("#other_div").show();
                }
                if ($.inArray('9', response.current_associated_medical_problem) > -1) {
                    $("#epilepsy_div").show();
                }
                $("#medical_problem_cell").html(response.form);
                $("#epilepsy").val(response.epilepsy);
                $("#other").val(response.other);
                $("#physical_finding").val(response.physical_finding);
                $("#systemic_examination").val(response.systemic_examination);
                $("#gynae_obstetrics_history").val(response.gynae_obstetrics_history);

                var radioObj = $('input[type="radio"][name="sleeping_pattern"][value="' + response.sleeping_pattern + '"]');
                radioObj.prop("checked", true);

                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                $("#clients_birth_weight").val(response.clients_birth_weight);
                $("#past_mediacl_history").val(response.past_mediacl_history);
                $("#family_history_of_diabetes_obesity").val(response.family_history_of_diabetes_obesity);
                $("#detailed_history").val(response.detailed_history);
                $("#treatment_history").val(response.treatment_history);
                $("#suggested_investigation").val(response.suggested_investigation);
                if (response.followup_date == '0000-00-00' || response.followup_date == '') {
                    $("#followup_date").val('');
                } else {
                    $("#followup_date").val(response.followup_date);
                }

                $("#doctors_name").val(response.doctors_name);

                if (response.assessment_date === '0000-00-00' || response.assessment_date == '') {
                    $("#assessment_date").val('');
                } else {
                    $("#assessment_date").val(response.assessment_date);
                }

                $('.field').click(function () {
                    if ($(this).is(':checked')) {
                        if ($(this).val() == 25) {
                            $("#other_div").show();
                        }
                        if ($(this).val() == 9) {
                            $("#epilepsy_div").show();
                        }
                    }
                    if ($(this).is(':unchecked')) {
                        if ($(this).val() == 25) {
                            $("#other_div").hide();
                        }
                        if ($(this).val() == 9) {
                            $("#epilepsy_div").hide();
                        }
                    }
                });

                if (response.biochemicalConditionTest != '') {
                    jQuery.each(response.biochemicalConditionTest, function (i, k) {
                        $("#initial_" + k.biochemical_condition_test_id).val(k.initial);
                        $("#final_" + k.biochemical_condition_test_id).val(k.final);
                    });
                }
            }

        });
    };

    var handleSkinHairAnalysisTable = function () {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/fetch-skin-hair-analysis-data";
        } else {
            var actionUrl = "fetch-skin-hair-analysis-data";
        }
        $.ajax({
            url: actionUrl,
            cache: false,
            type: 'POST',
            data: {"member_id": $("#member_id").val()},
            success: function (response)
            {

                if (response.skin_type != '') {
                    jQuery.each(response.skin_type, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="skin_type[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-skin-type').siblings().find('span').removeClass("checked");
                    });
                }

                if (response.skin_condition != '') {
                    jQuery.each(response.skin_condition, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="skin_condition[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-skin-condition').siblings().find('span').removeClass("checked");
                    });
                }
                $("#hyperpigmentation_type").val(response.hyperpigmentation_type);
                $("#hyperpigmentation_size").val(response.hyperpigmentation_size);
                $("#hyperpigmentation_depth").val(response.hyperpigmentation_depth);
                $("#scars_depth").val(response.scars_depth);
                $("#scars_size").val(response.scars_size);

                var radioObj = $('input[type="radio"][name="scars_pigmented"][value="' + response.scars_pigmented + '"]');
                radioObj.prop("checked", true);

                radioObj.parents('span').addClass('checked');
                radioObj.parents('.radio-container').siblings().find('span').removeClass("checked");

                $("#fine_lines_and_wrinkles").val(response.fine_lines_and_wrinkles);
                $("#skin_curvature").val(response.skin_curvature);

                if (response.other_marks != '') {
                    jQuery.each(response.other_marks, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="other_marks[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-other-marks').siblings().find('span').removeClass("checked");
                    });
                }

                if (response.hair_type != '') {
                    jQuery.each(response.hair_type, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="hair_type[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-hair-type').siblings().find('span').removeClass("checked");
                    });
                }

                if (response.condition_of_scalp != '') {
                    jQuery.each(response.condition_of_scalp, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="condition_of_scalp[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-scalp-condition').siblings().find('span').removeClass("checked");
                    });
                }

                $("#hair_density").val(response.hair_density);

                if (response.condition_of_hair_shaft != '') {
                    jQuery.each(response.condition_of_hair_shaft, function (i, val) {
                        var checkboxObj = $('input[type="checkbox"][name="condition_of_hair_shaft[]"][value="' + val + '"]');
                        checkboxObj.prop("checked", true);

                        checkboxObj.parents('span').addClass('checked');
                        checkboxObj.parents('.checkbox-container-hair-shaft').siblings().find('span').removeClass("checked");
                    });
                }

                $("#history_of_allergy").html(response.history_of_allergy);
                $("#conclusion").html(response.conclusion);
                $("#skin_and_hair_specialist_name").val(response.skin_and_hair_specialist_name);

                if (response.analysis_date == '0000-00-00' || response.analysis_date == '') {
                    $("#analysis_date").val();
                } else {
                    $("#analysis_date").val(response.analysis_date);
                }


            }
        });
    };

    var handleReviewFitnessActivityTable = function (tableId, customerId) {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/get-review-record";
        } else {
            var actionUrl = "get-review-record";
        }
        $('#review-record-table').dataTable().fnDestroy();
        reviewGrid = new Datatable();
        reviewGrid.init({
            src: $('#review-record-table'),
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
                    {data: 'review_date', name: 'review_date'},
                    {data: 'static_posture_score', name: 'static_posture_score'},
                    {data: 'sit_and_reach_test', name: 'sit_and_reach_test'},
                    {data: 'right_shoulder_flexibility', name: 'right_shoulder_flexibility'},
                    {data: 'left_shoulder_flexibility', name: 'left_shoulder_flexibility'},
                    {data: 'pulse', name: 'pulse'},
                    {data: 'slr', name: 'slr'},
                    {data: 'specific_activity_advice', name: 'specific_activity_advice'},
                    {data: 'specific_activity_duration', name: 'specific_activity_duration'},
                    {data: 'precautions_and_contraindications', name: 'precautions_and_contraindications'},
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
                    "url": actionUrl,
                    "type": "GET",
                    data: {"member_id": $("#member_id").val()},
                }
            }
        });
        $('#data-search').hide();
    };

    var handleMeasurementRecordsTable = function () {

        var measurementRecord = siteObjJs.admin.cprJs.measurementRecordsTitle;
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = 'cpr/get-member-measurement-record';
        } else {
            var actionUrl = "get-member-measurement-record";
        }
        jQuery.ajax({
            url: actionUrl,
            cache: false,
            data: {"member_id": $("#member_id").val()},
            type: "GET",
            success: function (data)
            {
                var measurementRecord = siteObjJs.admin.cprJs.measurementRecordsTitle;
                var m = 1;
                var dateArray = [];
                $.each(measurementRecord, function (measurementRecordKey, measurementRecordValue) {

                    var fullHtml = '';
                    var calculateHtml = '';

                    var fullHtml = '<table class="table table-striped table-bordered table-hover"><thead><tr role="" class="heading"><th> # </th><th style="vertical-align: top !important;"> Date </th>';
                    $.each(measurementRecordValue, function (key, value) {
                        fullHtml += '<th style="vertical-align: top !important;"> ' + value.title + '</th>';
                    });

                    fullHtml += '</tr></thead><tbody>';
                    $.each(data, function (dataResultKey, dataResultvalue) {
                        if (m == dataResultKey) {
                            var index = 1;
                            $.each(dataResultvalue, function (resultKey, resultValue) {
                                var tableData = '';
                                $.each(measurementRecordValue, function (measurementKey, measurementValue) {
                                    if (resultValue[measurementKey] !== undefined) {
                                        tableData += '<td> ' + resultValue[measurementKey] + '</td>';
                                    } else {
                                        tableData += '<td> N/A </td>';
                                    }

                                });

                                var dateAr = resultKey.split('-');
                                var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];

                                fullHtml += '<tr><td> ' + index + '</td><td> ' + newDate + '</td>' + tableData + '</tr>';
                                dateArray.push(resultKey);
                                index++;
                            });
                        }

                    });

                    fullHtml += '</tbody></table>';
                    $("#_" + measurementRecordKey.toLowerCase() + "-table").html(fullHtml);

                    //to cm loss calculation
                    var total_column = $("#_" + measurementRecordKey.toLowerCase() + "-table").find('tr')[0].cells.length;
                    var rowCount = $('#_' + measurementRecordKey.toLowerCase() + '-table tr').length;
                    if (rowCount == 1) {
                        calculateHtml = '<tr><td colspan="' + (total_column - 1) + '"> No records found !<td><tr>';
                    } else {
                        var table_data = '';
                        for (var i = 2; i <= (total_column - 1); i++) {

                            var first_cell = $('#_' + measurementRecordKey.toLowerCase() + '-table tr:nth(1) td:nth(' + i + ')').text();
                            var last_cell = $('#_' + measurementRecordKey.toLowerCase() + '-table tr:last td:nth(' + i + ')').text();

                            var total_cm_loss = (parseFloat(first_cell) || 0) - (parseFloat(last_cell) || 0);
                            if (total_cm_loss < 0) {
                                total_cm_loss = 0;
                            }
                            table_data += '<td>' + total_cm_loss + '</td>';

                        }

                        calculateHtml = '<tr><td></td><td><b>Total cm Loss</b></td>' + table_data + '<tr>';
                    }

                    $('#_' + measurementRecordKey.toLowerCase() + '-table tr:last').after(calculateHtml);
                    $('#_' + measurementRecordKey.toLowerCase() + '-table tr:last').css('display', 'none');

                    $('.measurement-record-date-' + m).datepicker('remove');


                    m++;

                });
                handleMeasurementRecordsDate(dateArray);
                if ($("#_arm-table-anchor").hasClass('collapsed')) {
                    $("#_arm-table-anchor").click();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

        $('#data-search').hide();
    };

    var handleMedicalreviewTable = function () {
        $('#medical-review-table').dataTable().fnDestroy();
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);

        if ((siteObjJs.admin.cprJs.sessionId == 0 || typeof siteObjJs.admin.cprJs.sessionId === "undefined") && lastId == "cpr") {
            var actionUrl = "cpr/get-medical-review-record";
        } else {
            var actionUrl = "get-medical-review-record";
        }
        medicalReviewGrid = new Datatable();
        medicalReviewGrid.init({
            src: $('#medical-review-table'),
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
                    {data: 'date', name: 'date'},
                    {data: 'advice', name: 'advice'},
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
                    "url": actionUrl,
                    "type": "POST",
                    data: {"member_id": $("#member_id").val()},
                }
            }
        });
        $('#data-search').hide();
    };

    var handleMeasurementRecordsDate = function (dateArray) {

        $('.measurement-record-date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            endDate: '+0d',
            datesDisabled: dateArray,
        });
        $('.measurement-record-date').datepicker('update');
    };

    return {
        //main function to initiate the module
        init: function () {
//            siteObjJs.admin.cprJs.measurementRecordsTitle;
            var currentUrl = document.location.href;
            var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);

            if (currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length) == "undefined") {
                window.history.pushState("object or string", "Title", "cpr");
            }

            if (siteObjJs.admin.cprJs.sessionId != 0 && lastId == "cpr" && typeof siteObjJs.admin.cprJs.sessionId !== "undefined") {
                window.history.pushState("object or string", "Title", "cpr/" + siteObjJs.admin.cprJs.sessionId);
            }

            initializeListener();

            if ($(".view_cpr_flag").length <= 0) {
                handleDatepicker();
                handleTable();
                setTimeout(checkBcaDate(), 200);
                setTimeout(handleBcaRecordTable(), 400);
                setTimeout(handleMeasurementTable(), 600);
                if (siteObjJs.admin.cprJs.sessionId != 0) {
                    setTimeout(handleSessionTable(), 800);
                }
                setTimeout(handleDietaryAssessmentTable(), 1000);
                setTimeout(handleFitnessAssessmentTable(), 1200);
                setTimeout(handleMedicalAssessmentTable(), 1400);
                setTimeout(handleSkinHairAnalysisTable(), 1600);
                setTimeout(handleReviewFitnessActivityTable(), 1600);
                setTimeout(handleMeasurementRecordsTable(), 1800);
                setTimeout(handleMedicalreviewTable(), 2000);
                //bind the validation method to 'add' form on load
                siteObjJs.validation.formValidateInit('#create-cpr', handleCPRRecords);
            }
        }
    };
}();


