siteObjJs.admin.offersJs = function () {
    var confirmRemoveImage, maxFileSize, mimes, confirmRemoveOfferImage;
    var token = $('meta[name="csrf-token"]').attr('content');
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
        $('body').on("click", ".remove-image", function () {
            var formElement = $(this.closest('form'));
            bootbox.confirm({
                buttons: {confirm: {label: 'CONFIRM'}},
                message: siteObjJs.admin.offersJs.confirmRemoveOfferImage,
                callback: function (result) {
                    if (result === false) {
                        return;
                    }
                    $('#thefile')[0].files[0] = "";
                    $('#remove').val('remove');
                    formElement.submit();
                }
            });
        });

    };
    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var cat_id = $(this).attr("id");
            var actionUrl = 'offers/' + cat_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    siteObjJs.validation.formValidateInit('#edit-offers', handleAjaxRequest);
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
        var formid = formElement.attr("id");
        var actionType = formElement.attr("method");
        var formData = formElement.serializeArray();
        var icon = "check";
        var messageType = "success";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        var image = $('#' + formid + ' #thefile')[0].files[0];
        if (image) {
            if (image.size > 2097152) {
                var error = siteObjJs.admin.offersJs.maxFileSize;
                $('#' + formid + ' #file-error').text(error);
                return false;
            }
            var ext = $('#' + formid + ' #thefile').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                var error = siteObjJs.admin.offersJs.mimes;
                $('#' + formid + ' #file-error').text(error);
                return false;
            }
            form.append('thefile', image);
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
                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }

                        //Empty the form fields
                        formElement.find("input[type=text], textarea").val("");
                        //trigger cancel button to go to initinal state of image upload
                        $("#"+formid+" a.fileinput-exists").trigger('click');
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

    var handleTable = function () {

        grid = new Datatable();
        grid.init({
            src: $('#offers-table'),
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
                    {data: 'offer_title', name: 'offer_title'},
                    {data: 'offer_description', name: 'offer_description'},
                    {data: 'offer_image', name: 'offer_image'},
                    {data: 'offer_detail_page_url', name: 'offer_detail_page_url'},
                    {data: 'status', name: 'status', orderable: false},
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
                    "url": "offers/data",
                    "type": "GET"
                },
                "order": [
                    [2, "asc"],
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
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-offers', handleAjaxRequest);
        }
    };
}();