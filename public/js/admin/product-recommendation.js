siteObjJs.admin.productRecommendationJs = function () {

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {

        var selectedVal = $('#customer_select').val();
        if (selectedVal != 0)
        {
            handleTable('product-recommendation-table', selectedVal);
        } else {
            initalizeTable();
        }

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
            $("#product_id").val([]).trigger('change');
        });

        $('body').on('change', '#customer_select', function (e) {
            if ($(this).val() != 0)
            {
                handleTable('product-recommendation-table', $(this).val());
                $(".customer_error").html('');
            } else
            {
                $('#product-recommendation-table-body').empty("");
            }
        });

        $('body').on('change', '#center_select', function (e) {
            var centerId = $(this).val();
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

    var initalizeTable = function () {
        $('#product-recommendation-table').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "bPaginate": false,
            "bSort": false,
            "emptyTable": "No data available in table"
        });
    };
// Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var recommendation_id = $(this).attr("id");
            var actionUrl = 'product-recommendation/' + recommendation_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    siteObjJs.validation.formValidateInit('#edit-product-recommendation', handleAjaxRequest);
                    $("#edit_form").find("#product_id").select2({
                        maximumSelectionSize: 1,
                        allowClear: true,
                        formatSelectionTooBig: function (limit) {
                            return 'Maximum 1 products can be selected';
                        }
                    });

                },
                error: function (jqXhr, json, errorThrown)
                {
                    var errors = jqXhr.responseJSON;
                    var errorsHtml = '';
                    $.each(errors, function (key, value) {
                        errorsHtml += value[0] + '<br />';
                    });
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
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";

        var user_type = siteObjJs.admin.productRecommendationJs.user_type_id;


        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });
        if (user_type == '4' || user_type == '8') {
            if (formID === 'create-product-recommendation') {
                var member_id = $('#customer_select').val();
                if ('' != member_id) {
                    form.append('member_id', member_id);
                    $(".customer_error").html("");
                } else {
                    // $("#s2id_customer_select").after('<span class="help-block help-block-error" id="customer_error">Please select Customer</span>');
                    $(".customer_error").html("Please select Customer");
                    return false;
                }
            }
        }
        if (formID === 'edit-product-recommendation') {
            form.append('_method', 'PUT');
        }
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

                        $('.btn-collapse').trigger('click');
                        $("#product_id").val([]).trigger('change');
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

    var handleTable = function (tableId, customerId) {
        //alert(customerId);return false;
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#product-recommendation-table'),
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
                    {data: 'product.product_title', name: 'product_title'},
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
                    "url": "product-recommendation/data/" + customerId,
                    "type": "GET"
                }
            }
        });
        $('#data-search').hide();
    };

    var handleSelector = function () {
        $("#product_id").select2({
            maximumSelectionSize: 6,
            formatSelectionTooBig: function (limit) {
                return 'Maximum 6 products can be selected';
            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            //handleTable();
            fetchDataForEdit();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-product-recommendation', handleAjaxRequest);
            handleSelector();
            $("#product_id").select2('val', 'empty');
        }

    };
}();