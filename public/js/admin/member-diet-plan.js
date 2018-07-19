siteObjJs.admin.memberDietPlanJs = function () {

    setDateRange();
// Initialize all the page-specific event listeners here.
    var add_custom_food = 0;
    var select_custom_food = 0;
    $(".table-group-actions span:first").css("display", "none");
    var initializeListener = function () {
        var customerId = $("#customer_select").val();
        if (customerId != "") {
            fetchPlan(customerId);
        } else {
            initalizeTable();
        }


        $("#s2id_customer_select").after('<div class="error"><span class="help-block help-block-error" id="customer_error"></span></div>');

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
                            fetchPlan(customerId);
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
            $("span.customer_error").html('');
            if ($(this).val() != 0 && $(this).val() != "") {
                fetchPlan($(this).val());
                $("#action-button-box").show();
            } else {
                $('#member-diet-plan-table-body').empty("");
                $("#action-button-box").hide();
            }
        });
        $('body').on('change', '#diet_plan_id', function (e) {
            var customerId = $('#customer_select').val();
            var selectedVal = $(this).val();
            if ($(this).val() != 0 && $(this).val() != "") {
                $(".diet-plan-error").html('');
            } else {
                $(".diet-plan-error").html('Please Select Diet Plan.');
            }
            if (customerId == "") {
                $(".customer_error").html('Please Select Customer.');
                return false;
            } else {
                if (selectedVal != 0 && customerId != 0) {
                    fetchDietPlanCalories(selectedVal);
                    handleTable('member-diet-plan-table', customerId, selectedVal);
                    $("#action-button-box").show();
                } else {
                    $('#member-diet-plan-table-body').empty("");
                    $("#action-button-box").hide();
                }
            }
        });
        $('body').on('change', '.servings_recommended', function (e) {
            if ($(this).val() == '0') {
                $(this).val('');
            } else {
                var className = $(this).attr("class").split(' ').pop();
                var className = className.substring(className.lastIndexOf("_") + 1, className.length);
                var unit_calories = $("input.unit_calories_" + className).val();
                var total_calories = parseInt($(this).val()) * parseInt(unit_calories);
                $(this).closest("tr").find("span.total_calories").html(total_calories);
            }
        });
        $('body').on('change', '.unit_calories', function (e) {
            if ($(this).val() == '0') {
                $(this).val('');
            } else {
                var className = $(this).attr("class").split(' ').pop();
                var className = className.substring(className.lastIndexOf("_") + 1, className.length);
                var total_calories = parseInt($(this).val()) * parseInt(1);
                $(this).closest("tr").find("span.total_calories").html(total_calories);
            }
        });
        $('body').on('click', '.add_existing_food_item', function (e) {
            var myId = $(this).attr("id");
            //$(".child-row-" + myId +":last").after("<tr><td>here</td></tr>");
            var diet_schedule_type_id = myId;
            var actionUrl = 'member-diet-plan/select-new-food';
            var diet_plan_row_id = [];

            $(".unique_diet_plan_id").each(function (i, v) {
                diet_plan_row_id.push($(this).val());
            });

            if (diet_plan_row_id.length == 0) {
                maxDietPlanRowId = 1;
            } else {
                var maxDietPlanRowId = parseInt(Math.max.apply(Math, diet_plan_row_id)) + parseInt(1);
            }

            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {diet_schedule_type_id: diet_schedule_type_id, myRowId: myId, maxDietPlanRowId: maxDietPlanRowId},
                success: function (data)
                {
                    var $element = $(".child-row-" + myId);
                    if ($element.length <= 0) {
                        $element = '<tr role="row" class="child-row-' + myId + '"></tr>';
                    }
                    $("a#" + myId).parents().find('tr.group-' + myId).after($element);
                    $(".child-row-" + myId + ":last").after(data.form);
                    $(".child-row-" + myId + ":last").find("select.select2me").each(function () {
                        $(this).select2({
                            allowClear: true,
                            placeholder: $(this).attr('data-label-text'),
                            width: null
                        });
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
        $('body').on('click', '.close-food-row-btn', function (e) {
            $(this).closest("tr").remove();
        });
        $('body').on('click', '.btn-collapse-form', function (e) {
            $('html, body').animate({
                scrollTop: $("#s2id_customer_select").offset().top
            }, 500);
            //$('html, body').animate({scrollTop: 450}, 500);
            grid.getDataTable().ajax.reload();
        });
        // Display Add New food html while assigning member diet plan
        $('body').on('change', '.select-new-food', function (e) {
            var selectedFoodId = $(this).val();
            var parent_row = $(this).closest("tr");
            var current_row_class = $(this).closest("tr").attr("class");
            var n = current_row_class.lastIndexOf("-");
            var diet_schedule_type_id = current_row_class.substring(n + 1);
            var diet_plan_row_id = [];
            var selected_food_type_id = $(this).closest('tr').find('select.food_type_id').val();
            if (selectedFoodId == 0) {
                $(".unique_diet_plan_id").each(function (i, v) {
                    diet_plan_row_id.push($(this).val());
                });
                var maxDietPlanRowId = parseInt(Math.max.apply(Math, diet_plan_row_id)) + parseInt(1); // 3);
                var actionUrl = 'member-diet-plan/add-new-food';
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {diet_schedule_type_id: diet_schedule_type_id, max_diet_plan_row_id: maxDietPlanRowId, selected_food_type_id: selected_food_type_id},
                    success: function (data)
                    {
                        add_custom_food++;
                        parent_row.attr("id", "custom_food" + add_custom_food);
                        parent_row.html(data.form);
                        $("#custom_food" + add_custom_food).find('.servings_recommended').attr('disabled', 'disabled');
                        $("." + current_row_class).find("select.select2me").each(function () {
                            $(this).select2({
                                allowClear: true,
                                placeholder: $(this).attr('data-label-text'),
                                width: null
                            });
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
            } else {
                select_custom_food++;
                parent_row.attr("id", "select_other_food" + select_custom_food);
                // Call Ajax function to Fetch Food Details
                var actionUrl = 'member-diet-plan/get-food-details/' + selectedFoodId;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    type: "GET",
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        $("tr#select_other_food" + select_custom_food).find("td.measure").html(data.measure);
                        $("tr#select_other_food" + select_custom_food).find("td.calories").html(data.calories);
                        $("tr#select_other_food" + select_custom_food).find(':input.unit_calories').val(data.calories);
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

        // Insert New Food while assigning member diet plan
        $('body').on('click', '.add-new-food', function (e) {
            var parent_row = $(this).closest("tr");
            var customRowId = parent_row.attr("id");
            // Get Food Details
            var food_type_id = $("#" + customRowId).find('select.food_type_id').val();
            var food_name = $("#" + customRowId).find('.food_name').val();
            var measure = $("#" + customRowId).find('.measure').val();
            var calories = $("#" + customRowId).find('.calories').val();
            var serving_size = $("#" + customRowId).find('.serving_size').val();
            var serving_unit = $("#" + customRowId).find('.serving_unit').val();
            var created_by = $("#" + customRowId).find('input[name="created_by"]').val();
            var messageType = "success";
            var icon = "check";
            var actionUrl = adminUrl + "/food";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                "data": {food_type_id: food_type_id, food_name: food_name, measure: measure, calories: calories, serving_size: serving_size, serving_unit: serving_unit, created_by: created_by, created_by_user_type: 4},
                success: function (data)
                {
                    if (data.status == "success") {
                        $("#" + customRowId).find('.food_type_id').attr('disabled', 'disabled');
                        $("#" + customRowId).find('.food_name').attr('disabled', 'disabled');
                        measure = measure.replace(/(?:^|\s)\w/g, function (match) {
                            return match.toUpperCase();
                        });
                        $("#" + customRowId).find('.measure').val(measure)
                        $("#" + customRowId).find('.measure').attr('disabled', 'disabled');
                        $("#" + customRowId).find('.calories').attr('disabled', 'disabled');
                        $("#" + customRowId).find('.serving_size').attr('disabled', 'disabled');
                        $("#" + customRowId).find('.serving_unit').attr('disabled', 'disabled');
                        $("#" + customRowId).find('a.add-new-food').remove();
                        $("#" + customRowId).find('.servings_recommended').prop('disabled', false);
                    } else {
                        icon = "times";
                        messageType = "danger";
                    }

                    //reload the data in the datatable
                    //grid.getDataTable().ajax.reload();
                    Metronic.alert({
                        type: messageType,
                        icon: icon,
                        message: data.message,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                    $("#" + customRowId).find('.food_id').val(data.food_id);
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



        // Display Food List based on selected Food Type
        $('body').on('change', '.food_type_id', function (e) {
            $("#edit-table").find("input:text").val("");
            if ($(this).val() != 0) {
                if ($(this).closest("tr").find("span.food_list_by_food_type").length > 0) {
                    var unique_id = $(this).closest("tr").find("span.food_list_by_food_type").attr("id");
                    fetchFoodList($(this).val(), unique_id, '');
                }
            }
        });

    };
    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            $("#edit-table").empty("");
            var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
            var member_diet_plan_id = $(this).attr("id");
            var actionUrl = 'member-diet-plan/' + member_diet_plan_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                beforeSend: function () {
                    $("#edit-table").html("");
                    $("#edit-table").html("<img class='center-block' src=" + loader_url + " alt='Loading...' />");
                },
                success: function (data) {

                    $("#edit_form").html(data.form);
                    var unique_id = $("span.food_list_by_food_type").attr("id");
                    fetchFoodList($(".food_type_id").val(),unique_id, data.foodId);
                    siteObjJs.validation.formValidateInit('#edit-diet-plan', handleAjaxRequest);
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
    // Function To List Packages Data of selected member
    var fetchFoodList = function (foodTypeId, unique_id, type) {
        var actionUrl = 'member-diet-plan/foodListByFoodType';

        var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {food_type_id: foodTypeId, unique_id: unique_id, type: type},
            beforeSend: function () {
                $("#" + unique_id).html("");
                $("#" + unique_id).html("<img class='center-block' src=" + loader_url + " alt='Loading...' />");
            },
            success: function (data)
            {
                $("#" + unique_id).html("");
                $("#" + unique_id).html(data.food_list);


                $("select.select-new-food").select2({
                    allowClear: true,
                    placeholder: $(this).attr('data-label-text'),
                    width: null,
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

    // Method to fetch Plan on member changed
    var fetchPlan = function (memberID) {
        var dietDate = $("#diet_date").val();
        var actionUrl = 'member-diet-plan/get-member-diet-plan?mId=' + memberID+'&date='+dietDate;
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                $("#plan-drop-down").html(data.list);
                $("#diet_plan_calories").val(data.diet_plan_calories);
                var dietPlan = $('#diet_plan_id').val();

                if ('' == data.diet_plan_id) {
                    $('#member-diet-plan-table-body').empty("");
                    $("#action-button-box").hide();
                    if (dietPlan == "") {
                        $(".diet-plan-error").html('Please Select Diet Plan.');
                        $(".customer_error").html('');
                        return false;
                    }
                } else {
                    handleTable('member-diet-plan-table', memberID, data.diet_plan_id);
                    $("#action-button-box").show();
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

    // Method to fetch Diet Plan calories when diet plan is changes
    var fetchDietPlanCalories = function (dietPlanId) {
        var actionUrl = 'member-diet-plan/get-calories';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {diet_plan_id: dietPlanId},
            success: function (data)
            {
                $("#diet_plan_calories").val(data.diet_plan_calories);
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

    $('body').on('click', '.save-member-diet-plan', function (e) {
        var customerId = $('#customer_select').val();
        if (customerId == "") {
            $(".customer_error").html('Please Select Customer.');
            return false;
        }
        //validateMemberDietPlan();
        var formId = $(this).closest("form").attr('id');
        $('#'+formId).validate({// initialize the plugin
            //focusCleanup: true,
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            errorClass: 'help-block help-block-error',
            highlight: function (element, errorClass, validClass) {
                $(element).next().addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass);
            },
            submitHandler: function (form) {
                // do other things for a valid form
                //form.submit();
                $(".diet-plan-error").html('');
                handleAjaxRequest();
            }
        });

//        $(".servings_recommended").each(function () {
//            $(this).rules("add", {
//                required: true,
//                checkValue: true,
//                messages: {
//                    required: "Please Enter Servings Recommended."
//                }
//            });
//        });

    });


    // Common method to handle add and edit ajax request and reponse
    var handleAjaxRequest = function () {

        if ($(".add-new-food").length != 0) {
            Metronic.alert({
                type: 'danger',
                message: "You have some Unsaved Food Items!",
                container: $('#ajax-response-text'),
                place: 'prepend',
                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
            });
            return false;
        }
        var formId = $(".save-member-diet-plan").closest("form").attr('id');
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formElement = $("#"+formId);
        var formID = formElement.attr("id");
        var serializedForm = formElement.serializeArray();
        //var serializedForm = $('#create-member-diet-plan').serializeArray();
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");

        var member_diet_plan = [];
        var icon = "check";
        var messageType = "success";
        var form = new FormData();

        serializedForm.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        if (formID === formId) {
            var member_id = $('#customer_select').val();
            var diet_plan_id = $('#diet_plan_id').val();
            if ('' != member_id) {
                form.append('id', member_id);
            } else {
                $(".customer_error").html("Please select Customer");
                return false;
            }
            if (diet_plan_id == '') {
                $("#help-block-error").html("Please select diet plan.");
                return false;
            }
        }

        if (formID === 'edit-diet-plan'){
            form.append('_method', 'PUT');
        }

        var checkedFood = 0;
        var dietPlanCalories = $(".diet_plan_calories").val();
        var totalCalories = 0;

        $('.member_diet_plan_items').each(function (i, v) {
            if ($(this).prop('checked') == true) {
                var total = 0;
                var id = $(this).attr("id");
                var last = id.substring(id.lastIndexOf("_") + 1, id.length);

                var servings_recommended = parseInt($(".unit_servings_" + last).val());
                var unit_calories = parseInt($(".unit_calories_" + last).val());
                var total = parseInt(servings_recommended * unit_calories);
                totalCalories = parseInt(totalCalories) + parseInt(total);

                member_diet_plan.push(1);
                checkedFood = 1;

            } else {
                member_diet_plan.push(0);
            }
        });

        if (parseInt(totalCalories) > parseInt(dietPlanCalories)) {
            Metronic.alert({
                type: 'danger',
                message: "You have exceeded calories limit!",
                container: $('#ajax-response-text'),
                place: 'prepend',
                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
            });
            return false;
        }

        // Check if There is not a single Checked Food Item
        if (checkedFood == 0) {
            Metronic.alert({
                type: 'danger',
                message: "You have to check at least one Food.",
                container: $('#ajax-response-text'),
                place: 'prepend',
                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
            });
            return false;
        }

        form.append('member_diet_plan', member_diet_plan);
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

                    //trigger cancel button click event to collapse form and show title of add page
                    //$('.btn-collapse').trigger('click');
                    //reload the data in the datatable
                    if (data.status === "success")
                    {
                        formElement.find("input[type=text], textarea").val("");
                        grid.getDataTable().ajax.reload();
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
        location.reload();
    }

    var handleTable = function (tableId, customerId, dietPlanid) {
        var dietDate = $("#diet_date").val();
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#' + tableId),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': false,
                    'infoEmpty': '',
                    'metronicGroupActions': ""
                },
                "bInfo": false,
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: 'check_food', name: 'check_food', searchable: false, orderable: false},
                    {data: 'schedule_name', name: 'schedule_name', visible: false, orderable: false},
                    {data: 'food_type_name', name: 'food_type_name', orderable: false},
                    {data: 'food_name', name: 'food_name', orderable: false},
                    {data: 'servings_recommended', name: 'servings_recommended', orderable: false},
                    {data: 'measure', name: 'measure', orderable: false},
                    {data: 'calories', name: 'calories'},
                    {data: 'total_calories', name: 'total_calories', searchable: false, orderable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false, orderable: false},
                    {data: 'diet_schedule_type_id', name: 'diet_schedule_type_id', visible: false, orderable: false}
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    //var groupCount = 0;
                    var displayLength = settings._iDisplayLength;

                    api.column(1, {page: 'current'}).data().each(function (group, i) {
                        var schedule_type_id = api.column(9, {page: 'current'}).data()[i];
                        if (last !== group) {
                            if ($("#acl_flag").val() == 2) {
                                $(rows).eq(i).before('<tr class="group group-' + schedule_type_id + ' bg-blue-hoki"><td colspan="9"><b>' + group + '</b><a class="add_existing_food_item"  id=' + schedule_type_id + '></a></td></tr>');
                            } else {
                                $(rows).eq(i).before('<tr class="group group-' + schedule_type_id + ' bg-blue-hoki"><td colspan="9"><b>' + group + '</b><a class="add_existing_food_item btn green"  id=' + schedule_type_id + '>Add Food Item</a></td></tr>');
                            }
                            last = group;
                        }
                        //recNum = ((page * displayLength) + i + 1);
                        //$(rows).eq(i).children('td:first').html(recNum);
                        $(rows).eq(i).children('td:first').parent('tr').addClass('child-row-' + schedule_type_id);
                    });
                    api.column(5, {page: 'current'}).data().each(function (group, i) {
                        if (group == null) {
                            $(rows).eq(i).remove();
                        } else {
                            group = group.replace(/(?:^|\s)\w/g, function (match) {
                                return match.toUpperCase();
                            });
                            $(rows).eq(i).children("td:nth-child(5)").html(group);
                        }
                    });
                },
                "ajax": {
                    "url": "member-diet-plan/data",
                    "type": "POST",
                    "data": {customerId: customerId, dietPlanid: dietPlanid,dietDate:dietDate},
                    "dataType": "json",
                },
                "bPaginate": false,
                "order": [
                    [1, "asc"],
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
    };
    var initalizeTable = function () {
        $('#member-diet-plan-table').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "bPaginate": false,
            "bSort": false,
            "emptyTable": "No data available in table"
        });
    };

    var handleSelectBoxes = function () {
        $('.bs-select').each(function () {
            $(this).selectpicker({iconBase: 'fa',
                tickIcon: 'fa-check',
                noneSelectedText: $(this).attr('data-label-text')});
        });
        $('.select2me').each(function () {
            $(this).select2({allowClear: true, placeholder: $(this).attr('data-label-text'), width: null});
        });
    };


    var memberDietPlanDate = function (dateArray) {
        $('body').on('focus', '#availability_date', function (e) {
            $(this).datepicker({
                format: 'yyyy-mm-dd',
                autoclose: 1,
                datesDisabled: dateArray,
            }).on('changeDate', function (e) {
                setDateRange($(this).val());
            });
        });

        $('#from_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#to_date').datepicker('setStartDate', startDate);
        }).on('clearDate', function (selected) {
            $('#to_date').datepicker('setStartDate', null);
        });

        $('#to_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#from_date').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
            $('#from_date').datepicker('setEndDate', null);
        });
    }


    function setDateRange(date = null) {

        var dateNew;
        var i;
        if (date == null){
            dateNew = $('#availability_date').val();
        } else {
            dateNew = date;
        }

        var today = new Date();
        var currDay = today.getDate();
        var currMonth = today.getMonth()+1;
        var currYear = today.getFullYear();

        var dateObj = new Date(dateNew);

        $(".weekly-diet-plan-range").html('');
        for (i=0;i < 5;i++){

            var day = dateObj.getDate();

            if(day.toString().length < 2){
                day = '0'+day;
            }
            var month = dateObj.getMonth() + 1;
            if(month.toString().length < 2){
                month = '0'+month;
            }
            var year = dateObj.getFullYear();
            var finalDate = day+'-'+month+'-'+year;
            dateObj.setDate(dateObj.getDate() + 1);

            var dateOne = new Date(currYear, currMonth, currDay); //Year, Month, Date
            var dateTwo = new Date(year, month, day); //Year, Month, Date

            var activeClass;
            if (dateOne > dateTwo) {
                var color = '#FF0000';
            } else {
                var color = '#32c5d2';
            }

            if(($("#diet_date").val()) == finalDate){
                activeClass = 'active';
            } else {
                activeClass = '';
            }

            $(".weekly-diet-plan-range").append('<span class="button diet-date '+activeClass+'" data-index="'+finalDate+'" style="cursor: pointer;margin-right: 10px;border-radius: 6px;background: '+color+';padding: 5px;color: #fff6ec;">'+finalDate+'</span>');
        }
    }

    $('body').on('click','.diet-date',function () {
        //alert($(this).data('index'))
        var today = new Date();
        var currDay = today.getDate();
        var currMonth = today.getMonth()+1;
        var currYear = today.getFullYear();
        var selectedDate = $(this).data('index').split('-');
        var dateOne = new Date(currYear, currMonth, currDay); //Year, Month, Date
        var dateTwo = new Date(selectedDate[2], selectedDate[1], selectedDate[0]); //Year, Month, Date

        if (dateOne > dateTwo) {
            $(".save-member-diet-plan").attr('disabled',true);
        } else {
            $(".save-member-diet-plan").attr('disabled',false);
        }

        $("#diet_date").val($(this).data('index'));
        //$('.diet-date').removeClass('active');
        $('.diet-date').css('color','white');
        $(this).css('color','yellow');
        //$(this).addClass('active');
        var customerId = $('#customer_select').val();
        if (customerId == "") {
            $(".customer_error").html('Please Select Customer.');
            $(".diet-plan-error").html('');
            return false;
        }  else {
            $(".diet-plan-error").html('');
            $(".customer_error").html('');
            fetchPlan(customerId);
        }


    });

    $('.btn-expand-download').click(function(e){
        $("#from_date").val("");
        $("#to_date").val("");
        $(".history").css("display","block");
        $(".date-Block").css("display","block");
    });

    $('.date-hide').click(function(e){
        $(".history").css("display","none");
        $("#from_date").val("");
        $("#to_date").val("");
    });

    $('.date-submit').click(function(){
        var customer = $("#customer_select").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        $("#selected_customer").val(customer);
        var arrStartDate = fromDate.split("-");
        var date1 = new Date(arrStartDate[2], arrStartDate[1], arrStartDate[0]);
        var arrEndDate = toDate.split("-");
        var date2 = new Date(arrEndDate[2], arrEndDate[1], arrEndDate[0]);
        if (fromDate == "" || toDate == "") {
            $("#date-error").css('display', 'block');
            return false;
        } else {
            $("#date-error").css('display', 'none');
        }

        if (customer == '' || customer == null){
            $(".customer_error").html('Please select customer');
            return false;
        } else {
            $(".customer_error").html('');
        }

        if (date1 > date2) {
            $("#date-range-error").css('display', 'block');
            return false;
        }
        else {
            $("#date-range-error").css('display', 'none');
        }

    });

    /*var reportDownloadDate = function (dateArray) {

        $('#from_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#to_date').datepicker('setStartDate', startDate);
        }).on('clearDate', function (selected) {
            $('#to_date').datepicker('setStartDate', null);
        });

        $('#to_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#from_date').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
            $('#from_date').datepicker('setEndDate', null);
        });
    };*/

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            memberDietPlanDate();
            //reportDownloadDate();
            fetchDataForEdit();
            //handleTable();
            //initalizeTable();
            //bind the validation method to 'add' form on load
            //siteObjJs.validation.formValidateInit('#create-member-diet-plan', handleAjaxRequest);
        }
    };
}();
