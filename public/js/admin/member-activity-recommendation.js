siteObjJs.admin.memberActivityRecommendationJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {

        var selectedVal = $('#customer_select').val();
        if (selectedVal != 0) {
            handleTable('member-activity-recommendation-table', selectedVal);
        } else {
            initalizeTable();
        }

        $("#s2id_customer_select").after('<div class="error"><span class="help-block help-block-error" id="customer_error"></span></div>');
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#activity_type_id").select2('val', '');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
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
                            handleTable('member-activity-recommendation-table', customer_id);
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

        $('body').on('change', '#customer_select', function (e) {
            $("#customer_error").html('');
            if ($(this).val() != 0) {
                handleTable('member-activity-recommendation-table', $(this).val());
            } else {
                $('#member-activity-recommendation-table-body').empty("");
            }
        });

        $('body').on('keyup', '#duration', function (e) {
            var activity_id = $("#activity_type_id").val();
            if ('' != activity_id && '' != $(this).val()) {
                fetchCaloreis(activity_id, $(this).val());
            }
        });

        $('body').on('change', '#activity_type_id', function (e) {
            var duration = $("#duration").val();
            if ('' != duration && '' != $(this).val()) {
                fetchCaloreis($(this).val(), duration);
            }
        });

    };

    var fetchCaloreis = function (activityID, duration) {
        var actionUrl = 'member-activity-recommendation/fetch-calories/' + activityID;
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                var calories = data.calories;
                var breakdown = calories / 60;
                var final_calories = parseInt(breakdown) * parseInt(duration);
                $("#calories_recommended").val(final_calories);
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

    // Common method to handle add and edit ajax request and reponse
    var handleAjaxRequest = function () {
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var formData = formElement.serializeArray();
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var icon = "check";
        var messageType = "success";

        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        if (formID === 'create-member-activity-recommendation') {
            var member_id = $('#customer_select').val();
            if ('' != member_id) {
                form.append('member_id', member_id);
            } else {
                $("#customer_error").html("Please select Customer");
                return false;
            }
        }
        $.ajax(
                {
                    url: actionUrl,
                    type: actionType,
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }

                        //Empty the form fields
                        formElement.find("input[type=text], textarea").val("");
                        $("#activity_type_id").select2('val', '');
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

    // Common method to handle add and edit ajax request and reponse
    var handleTable = function (tableId, customerId) {
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
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'activity_type.activity_type', name: 'activity_type'},
                    {data: 'recommendation_date', name: 'recommendation_date', orderable: false},
                    {data: 'duration', name: 'duration', orderable: false},
                    {data: 'calories_recommended', name: 'calories_recommended', orderable: false},
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
                    "url": "member-activity-recommendation/data/" + customerId,
                    "type": "GET"
                },
            }
        });
        $('#data-search').hide();
    };

    var initalizeTable = function () {
        $('#member-activity-recommendation-table').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "bPaginate": false,
            "bSort": false,
            "emptyTable": "No data available in table"
        });
    };

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }
        $("#recommendation_date").datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            startDate: '+0d',
            isRTL: Metronic.isRTL(),
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
        });
    };


    var handleFilterDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $(".form_datetime").datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
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
            handleDatePicker();
            handleFilterDatePicker();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-member-activity-recommendation', handleAjaxRequest);
        }

    };
}();