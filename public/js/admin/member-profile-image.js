/* global Metronic */

siteObjJs.admin.memberProfileImageJs = function () {

// Initialize all the page-specific event listeners here.
    var maxFileSize, mimes;
    var initializeListener = function () {
        var selectedVal = $('#customer_select').val();
        if (selectedVal != 0)
        {
            handleTable('member-profile-image-table', selectedVal);
        } else {
            initalizeTable();
        }

        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            formElement.find('.fileinput').fileinput('clear');
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#package_id").select2('val', '');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        $('body').on('change', '#customer_select', function (e) {
            if ($(this).val() != 0)
            {
                $("#customer_error").html('');
                if ($('.fileinput').length > 0) {
                    $('.fileinput').fileinput('clear');
                }
                getCustomerPackage(this);
                handleTable('member-profile-image-table', $(this).val());
            } else
            {
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
                            handleTable('member-profile-image-table', customer_id);
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

        $("#s2id_customer_select").after('<div class="error"><span class="help-block help-block-error" id="customer_error"></span></div>');
        $('#before_image').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.memberProfileImageJs.maxFileSize;
                    $('#before-file-error').text(error);
                    return false;
                }

                var ext = $('#before_image').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.memberProfileImageJs.mimes;
                    $('#before-file-error').text(error);
                    return false;
                } else
                {
                    $('#before-file-error').text('');
                }

            }

        });

        $('#after_image').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.memberProfileImageJs.maxFileSize;
                    $('#after-file-error').text(error);
                    return false;
                }

                var ext = $('#after_image').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.memberProfileImageJs.mimes;
                    $('#after-file-error').text(error);
                    return false;
                } else
                {
                    $('#after-file-error').text('');
                }

            }

        });

        //if "remove" button is clicked from input, clear error messages
        $("a.after-fileinput-exists").on('click', function () {
            $('#after-file-error').text('');
        });

        $("a.before-fileinput-exists").on('click', function () {
            $('#before-file-error').text('');
        });

    };

    var getCustomerPackage = function (elet, content) {
        content = content || '';
        var currentForm = $(elet).closest("form");
        var memberID = $(elet).val();
        var actionUrl = 'member-profile-image/get-customer-packages/' + memberID;
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                if (!$.isEmptyObject(data.memberPackages)) {
                    $("#package_msg").html("");
                    $("#member-package-img-container").removeAttr("style");
                    var list = '';
                    list += '<ol>';
                    for (var property in data.memberPackages) {
                        list += '<li>' + data.memberPackages[property] + '</li>';
                    }
                    list += '</ol>'
                    $("#package_list").html(list);
                } else {
                    $("#package_msg").html("No package data found!");
                    $("#member-package-img-container").hide();
                    $("#package_list").html("Package data not found");
                }
                var asset_url = siteObjJs.admin.memberProfileImageJs.assetUrl;
                $("#id").val(data.id);
                if ('' != data.before_image) {
                    var image = asset_url + 'img/package_profile_img/' + data.id + '/' + data.before_image;
                    $("img#before_img").attr("src", image);
                    $("#before_img_avatar").val(image);
                } else {
                    $("img#before_img").attr("src", asset_url + 'images/default-user-icon-profile.png');
                }

                if ('' != data.after_image) {
                    var image = asset_url + 'img/package_profile_img/' + data.id + '/' + data.after_image;
                    $("img#after_img").attr("src", image);
                } else {
                    $("img#after_img").attr("src", asset_url + 'images/default-user-icon-profile.png');
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
    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var member_profile_image_id = $(this).attr("id");
            var actionUrl = 'member-profile-image/' + member_profile_image_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    siteObjJs.validation.formValidateInit('#edit-member-profile-image', handleAjaxRequest);
                    $('#edit_form').find('.select2me').attr('disabled', true);
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
        $('#edit_form').find('.select2me').attr('disabled', false);
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";

        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        var image = $('#' + formID + ' #before_image')[0].files[0];
        if (image) {
            if (image.size > 2097152) {
                var error = siteObjJs.admin.memberProfileImageJs.maxFileSize;
                $('#' + formID + ' #before-file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            var ext = $('#' + formID + ' #before_image').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                var error = siteObjJs.admin.memberProfileImageJs.mimes;
                $('#' + formID + ' #before-file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            form.append('before_image', image);
        }

        var image = $('#' + formID + ' #after_image')[0].files[0];
        if (image) {
            if (image.size > 2097152) {
                var error = siteObjJs.admin.memberProfileImageJs.maxFileSize;
                $('#' + formID + ' #after-file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            var ext = $('#' + formID + ' #after_image').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                var error = siteObjJs.admin.memberProfileImageJs.mimes;
                $('#' + formID + ' #after-file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            form.append('after_image', image);
        }

        if (formID === 'create-member-profile-image' || formID === 'edit-member-profile-image') {
            var member_id = $('#customer_select').val();
            if ('' != member_id) {
                form.append('member_id', member_id);
            } else {
                $("#customer_error").html("Please select Customer");
                return false;
            }
        }

        if (formID === 'edit-member-profile-image') {
            form.append('_method', 'PUT');
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
                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }
                        $("#id").val(data.id);
                        var asset_url = siteObjJs.admin.memberProfileImageJs.assetUrl;
                        if ('' != data.before_image) {
                            var image = asset_url + 'img/package_profile_img/' + data.id + '/' + data.before_image;
                            $("img#before_img").attr("src", image);
                            $("#before_img_avatar").val(image);
                        } else {
                            $("img#before_img").attr("src", asset_url + 'images/default-user-icon-profile.png');
                        }
                        if ('' != data.after_image) {
                            var image = asset_url + 'img/package_profile_img/' + data.id + '/' + data.after_image;
                            $("img#after_img").attr("src", image);
                        } else {
                            $("img#after_img").attr("src", asset_url + 'images/default-user-icon-profile.png');
                        }
                        //Empty the form fields
                        var formElement = $(this).closest("form");
                        $('.fileinput').fileinput('clear');
                        //trigger cancel button click event to collapse form and show title of add page
                        //$('.btn-collapse').trigger('click');

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
        $('#' + tableId).dataTable().fnDestroy();

        grid = new Datatable();
        grid.init({
            src: $('#member-profile-image-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                        "columns": [
                            {data: null, name: 'rownum', searchable: false, orderable: false},
                            {data: 'id', name: 'id', visible: false, orderable: false},
                            {data: 'before_image', name: 'before_image', orderable: false},
                            {data: 'after_image', name: 'after_image', orderable: false},
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
                    "url": "member-profile-image/data/" + customerId,
                    "type": "GET"
                },
                "ordering": false
            }
        });
        $('#data-search').hide();
    };

    var initalizeTable = function () {
        $('#member-profile-image-table').dataTable({
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
    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
//            handleTable();
            fetchDataForEdit();
            handleDatePicker();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-member-profile-image', handleAjaxRequest);
        }

    };
}();