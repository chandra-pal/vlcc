siteObjJs.admin.memberDietLogJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        var selectedVal = $('#customer_select').val();
        var selectedDate = $('#diet_date').val();
        if (selectedVal != 0)
        {
            handleTable('member-diet-log-table', selectedVal, selectedDate);
            getDietRecommendation('member-diet-log-recommendation-table', selectedVal, selectedDate);
            if ($("#logged_in_user_type").val() == "9") {
                $(".diet-recommendation-form").removeClass("display-hide");
                siteObjJs.validation.formValidateInit('#create-member-diet-log', handleAjaxRequest);
            }
            getDietPlanDetails(selectedVal);
        } else {
            initalizeTable();
        }
        $("#s2id_customer_select").after('<div class="error"><span class="help-block help-block-error" id="customer_error"></span></div>');

        $('body').on("click", ".btn-collapse", function () {
            var count = $("#row_count").val();
            var i = '';
            for (i = 2; i <= count; i++) {
                $('table#member-diet-log-recommendation-table tr.child-row-' + i).remove();
            }
            $("#select-new-food_1").select2('val', '');
            $(".calories").html("");
            $(".measure").html("");
            $(".diet-recommendation-form").hide();
            $("#row_count").val(1);
            $('.help-block-error').html('');
            $("#schedule_type_id").select2('val', '');
            $("#food_id").select2('val', '');
            $("#food_type_id").select2('val', '');
        });

        $('body').on('click', '.recommende-diet-btn', function (e) {
            $(".diet-recommendation-form").show();
            siteObjJs.validation.formValidateInit('#create-member-diet-log', handleAjaxRequest);
        });

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
                        if (customer_id != "" && customer_id != 0) {
                            handleTable('member-diet-log-table', customer_id, selectedDate);
                            getDietRecommendation('member-diet-log-recommendation-table', customer_id, selectedDate);
                            if ($("#logged_in_user_type").val() == "9") {
                                $(".diet-recommendation-form").removeClass("display-hide");
                                siteObjJs.validation.formValidateInit('#create-member-diet-log', handleAjaxRequest);
                            }
                            getDietPlanDetails(customer_id);
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
            handleDatePicker();
            if ($(this).val() != 0)
            {
                $("#button_panle").show();
                $("#warnig_message").show();
                if ("" != $("#diet_date").val()) {
                    handleTable('member-diet-log-table', $(this).val(), $("#diet_date").val());
                    getDietRecommendation('member-diet-log-recommendation-table', $(this).val(), $("#diet_date").val());
                    if ($("#acl_flag").val() == "2") {
                        $(".diet-recommendation-form").removeClass("display-hide");
                        siteObjJs.validation.formValidateInit('#create-member-diet-log', handleAjaxRequest);
                    }
                } else {
                    var today = new Date();
                    var dd = ("0" + today.getDate()).slice(-2);
                    var mm = ("0" + (today.getMonth() + 1)).slice(-2); //January is 0!
                    var yyyy = today.getFullYear();
                    var current_date = dd + "-" + mm + "-" + yyyy;
                    $(".diet-date").val(current_date);
                    handleTable('member-diet-log-table', $(this).val(), current_date);
//                    getDietRecommendation('member-diet-log-recommendation-table', $(this).val(), current_date);
                }

                getDietPlanDetails($(this).val());

            } else {
                $("#button_panle").hide();
                $("#warnig_message").hide();
                $('#member-diet-log-table-body').empty("");
                var count = $("#row_count").val();
                var i = '';
                for (i = 2; i <= count; i++) {
                    $('table#member-diet-log-recommendation-table tr.child-row-' + i).remove();
                }
                $("#select-new-food_1").select2('val', '');
                $(".calories").html("");
                $(".measure").html("");
                $(".diet-recommendation-form").hide();
                $("#row_count").val(1);
                $('.help-block-error').html('');
                $("#schedule_type_id").select2('val', '');
                $(".recommende-diet-btn").hide();
            }
        });

        // Display Add New food html while assigning member diet plan

        $('body').on('change', '.select-new-food', function (e) {
//            alert();
            var selectedFoodId = $(this).val();
            var parent_row = $(this).closest("tr");
            var current_row_class = $(this).closest("tr").attr("class");
            var arr = current_row_class.split('-');
            var current_row = arr[2];
            if ('' != $(this).val()) {
                var actionUrl = adminUrl + '/member-diet-log/get-food-details/' + selectedFoodId;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    type: "GET",
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        $(".child-row-" + current_row).find(".calories").html(data.calories);
                        $(".child-row-" + current_row).find(".measure").html(data.measure);
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
                $(".child-row-" + current_row).find(".calories").html('');
                $(".child-row-" + current_row).find(".measure").html('');
            }

        });

        $('.schedule-group-box').live('click', function (e) {
            var target = $(e.target);
            if (!target.is('a') && !target.is('i')) {
                var dataScheduleId = $(this).attr('data-schedule-id');
                $.each($('.roomBox'), function () {
                    if ($(this).hasClass('roomBox-' + dataScheduleId)) {
                        $(this).toggleClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            }
        });


        $('body').on('change', '#food_type_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchFoodList($(this).val());
            } else
            {
                $('#create-member-diet-log #food_id').empty();
                $('#create-member-diet-log #food_id').append($('<option>', {
                    value: '',
                    text: 'Select Food',
                }));
            }
        });
    };

// Method to food list on food type changes

    var fetchFoodList = function (food_type_id) {
        var dropdownData = {
            food_type_id: food_type_id
        }
        var actionUrl = adminUrl + '/member-diet-log/get-food-list';
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            data: dropdownData,
            dataType: 'json',
            success: function (data)
            {
                $("#member_food_type_list_data").html(data.list);

                $("#create-member-diet-log #food_id").select2({
                    matcher: function (term, text, option) {
                        return option.html().toUpperCase().indexOf(term.toUpperCase()) == 0;
                    },
                    sortResults: function (results, container, query) {
                        if (query.term == "") {
                            return results.slice(0, 100);
                        } else {
                            return results;
                        }
                    },
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
    };


    var getDietPlanDetails = function (mid) {
        var actionUrl = adminUrl + '/member-diet-log/get-diet-plan-details/' + mid;
        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: "GET",
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        $("#row_count").val(data.rowCount);
                        $("#diet_plan_id").val(data.memberDietPlan);
                        $("#deviation").html("Total Deviation :" + data.deviation);
                        $("#schedule_dropdown").html(data.list);
                        $("#schedule_type_id").select2({
                            placeholder: 'Select Schedule Type'
                        });

                        var date = $("#diet_date").val();
                        var d = new Date();
                        var curr_date = d.getDate();
                        var curr_month = d.getMonth() + 1;
                        if (curr_month < 10) {
                            curr_month = ('0' + curr_month);
                        }
                        var curr_year = d.getFullYear();

                        var current_date = curr_date + '-' + curr_month + '-' + curr_year;

                        if (current_date != date) {
                            $(".recommendation-text").html('View Recommendations Sent');
                        } else {
                            $(".recommendation-text").html('Send Diet Recommendation');
                        }

                        if (0 == data.memberDietPlan && '' == data.memberDietPlan) {
                            $("#warnig_message").html('No diet plan assigned to this user.');
                            var count = $("#row_count").val();
                            var i = '';
                            for (i = 2; i <= count; i++) {
                                $('table#member-diet-log-recommendation-table tr.child-row-' + i).remove();
                            }
                            $("#select-new-food_1").select2('val', '');
                            $(".calories").html("");
                            $(".measure").html("");
                            $(".diet-recommendation-form").hide();
                            $("#row_count").val(1);
                            $('.help-block-error').html('');
                            $("#schedule_type_id").select2('val', '');
                            $(".recommende-diet-btn").hide();
                        } else {
                            $("#warnig_message").html('');

                            var count = $("#row_count").val();
                            var i = '';
                            for (i = 2; i <= count; i++) {
                                $('table#member-diet-log-recommendation-table tr.child-row-' + i).remove();
                            }
                            $("#select-new-food_1").select2('val', '');
                            $(".calories").html("");
                            $(".measure").html("");
                            $(".diet-recommendation-form").removeAttr("style");
                            $("#row_count").val(1);
                            $('.help-block-error').html('');
                            $("#schedule_type_id").select2('val', '');
                            $(".recommende-diet-btn").removeAttr("style");
                        }
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

        var member_id = $('#customer_select').val();
        var diet_plan_id = $('#diet_plan_id').val();

        if ('' != member_id) {
            form.append('member_id', member_id);
        } else {
            $("#customer_error").html("Please select Customer");
            return false;
        }

        if (diet_plan_id == 0 || diet_plan_id == "") {
            Metronic.alert({
                type: 'danger',
                message: "Diet Plan is not assigned for this user.",
                container: $('#ajax-response-text'),
                place: 'prepend',
                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
            });
            return false;
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

                        var count = $("#row_count").val();
                        var i = '';
                        for (i = 2; i <= count; i++) {
                            $('table#member-diet-log-recommendation-table tr.child-row-' + i).remove();
                        }
                        $("#select-new-food_1").select2('val', '');
                        $(".serving-recommended").val("");
                        $(".calories").html("");
                        $(".measure").html("");
                        $(".diet-recommendation-form").hide();
                        $("#row_count").val(1);
                        $("#schedule_type_id").select2('val', '');
                        //Empty the form fields
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
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'schedule_name', name: 'schedule_name', visible: false, orderable: false},
                    {data: 'food_name', name: 'food_name', orderable: false},
                    {data: 'calories', name: 'calories', orderable: false},
                    {data: 'servings_consumed', name: 'servings_consumed', orderable: false},
                    {data: 'measure', name: 'measure', orderable: false},
                    {data: null, name: 'difference', visible: false},
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
                    api.column(2, {page: 'current'}).data().each(function (group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before('<tr class="group bg-blue-hoki"><td colspan="10"><b>' + group + '</b><span id="deviation_' + i + '"></span></td></tr>');
                            last = group;
                        }
                    });

                    api.column(3, {page: 'current'}).data().each(function (group, i) {
                        if (group == null) {
//                            $(rows).eq(i).html("<td colspan='10' class='text-center'>No foods logged by user.</td>");
                            $(rows).eq(i).remove();
                        }
                    });

                    api.column(7, {page: 'current'}).data().each(function (group, i) {
                        var calories_recommended = (group.calories_recommended != null) ? parseInt(group.calories_recommended) : 0;
                        var calories_consumed = (group.calories_recommended != null) ? parseInt(group.calories_consumed) : 0;
                        var difference = calories_consumed - calories_recommended;
                        if (group.calories_consumed != null) {
                            if (0 != difference) {
                                if ($(rows).eq(i).attr('role') == 'row' && $(rows).eq(i).prev().hasClass('bg-blue-hoki')) {
                                    $(rows).eq(i).prev('tr.bg-blue-hoki').addClass('bg-red-mint');
                                    $("#deviation_" + i).html(" (Deviation : " + difference + ")");
                                }
                            } else {
                                if ($(rows).eq(i).attr('role') == 'row' && $(rows).eq(i).prev().hasClass('bg-blue-hoki')) {
                                    $(rows).eq(i).prev('tr.bg-blue-hoki').addClass('bg-green-seagreen');
                                    $("#deviation_" + i).html(" (No Deviation)");
                                }
                            }
                        }
                    });
                },
                "ajax": {
                    "url": adminUrl + "/member-diet-log/data",
                    "type": "POST",
                    "data": {customerId: customerId, date: date},
                    "dataType": "json",
                },
                "order": [
                    [1, "desc"],
                ],
                "bInfo": false
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
        $('#search_member_diet_log').on('change', function () {
            grid.getDataTable().column($(this).attr('column-index')).search($(this).val()).draw();
        });
    };



    var getDietRecommendation = function (tableId, customerId, date) {
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
                    {data: 'food_type_name', name: 'food_type_name', visible: true},
                    {data: 'food_name', name: 'food', orderable: false},
                    {data: 'servings_recommended', name: 'servings_recommended', orderable: false},
                    {data: 'measure', name: 'measure', orderable: false},
                    {data: 'calories', name: 'calories', orderable: false},
                ],
                "drawCallback": function (settings) {
                },
                "ajax": {
                    "url": adminUrl + "/member-diet-log/get-member-diet-recommendation",
                    "type": "POST",
                    "data": {customerId: customerId, date: date},
                    "dataType": "json",
                },
                "bInfo": false
            }
        });
        $('#data-search').hide();
    };


    var initalizeTable = function () {
        $('#member-diet-log-table').dataTable({
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

        $(".diet-date").datepicker({
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
                    handleTable('member-diet-log-table', client_id, $(this).val());
                    getDietRecommendation('member-diet-log-recommendation-table', client_id, $(this).val());
                }

                var d = new Date();
                var curr_date = d.getDate();
                var curr_month = d.getMonth() + 1;
                if (curr_month < 10) {
                    curr_month = ('0' + curr_month);
                }
                var curr_year = d.getFullYear();

                var current_date = curr_date + '-' + curr_month + '-' + curr_year;

                if ($(this).val() != current_date) {
                    $(".recommendation-text").html('View Recommendations Sent');
                    $(".btn-panel").css('display', 'none');
                    $(".schedule-type").css('display', 'none');
                    $('#member-diet-log-recommendation-table tr.child-row-1').css('display', 'none');
                    $("#deviation").css('display', 'none');
                } else {
                    $(".recommendation-text").html('Send Diet Recommendation');
                    $(".btn-panel").removeAttr('style');
                    $(".schedule-type").css('display', 'block');
                    $('#member-diet-log-recommendation-table tr.child-row-1').removeAttr('style');
                    $("#deviation").removeAttr('style');
                }

            } else
            {
                $('#member-diet-log-table-body').empty("");
            }


        });
    };
    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleDatePicker();
//            siteObjJs.validation.formValidateInit('#create-member-diet-log', handleAjaxRequest);
        }

    };
}();