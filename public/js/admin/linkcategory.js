siteObjJs.admin.linkCategory = function () {
    var grid;

    var initializeListener = function () {
        $('body').on("click", ".btn-collapse", function () {
            $("#errorMessage").html("");

            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $(".category-icon").html("");
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });
        $('#linkcategory-table').on('click', '.filter-cancel', function (e) {
            $("#menu-group-id-search").select2("val", "");
        });
    };

    var handleTable = function () {
        grid = new Datatable();

        grid.init({
            src: $('#linkcategory-table'),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
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
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'group_name', name: 'group_name'},
                    {data: 'category', name: 'category'},
                    {data: 'header_text', name: 'header_text'},
                    {data: 'position', name: 'position'},
                    {data: 'status_format', name: 'status_format', visible: false},
                    {data: 'status', name: 'status'},
                    {data: 'categoryicon', name: 'categoryicon', orderable: false, searchable: false, visible: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    api.column(5, {page: 'current'}).data().each(function (group, i) {
                        $(rows).eq(i).children('td:nth-child(6)').html(group);
                    });
                    api.column(7, {page: 'current'}).data().each(function (group, i) {
                        $(rows).eq(i).children('td:nth-child(3)').html(group);
                    });
                },
                "ajax": {
                    "url": "link-category/data",
                    "type": "GET"
                }
            }
        });

        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            var actionType = action.attr('data-actionType');
            var actionField = action.attr('data-actionField');
            var actionValue = action.val();
            if (action.val() !== "" && grid.getSelectedRowsCount() > 0) {

                var formdata = {
                    action: 'update',
                    actionField: actionField,
                    actionType: actionType,
                    actionValue: actionValue,
                    ids: grid.getSelectedRows()
                };

                handleGroupAction(grid, formdata);

            } else if (action.val() === "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            } else if (grid.getSelectedRowsCount() === 0) {
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
            bootbox.confirm("Are you sure?", function (result) {
                if (result === false) {
                    return;
                }

                var nRow = $(this).parents('tr')[0];

                var formdata = {
                    action: 'delete',
                    actionField: 'id',
                    actionType: 'group',
                    actionValue: userId,
                    ids: userId
                };
                handleGroupAction(grid, formdata);
            });
        });

        table.on('click', '.view', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');

            var nTr = $(this).parents('tr')[0];
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }

        });

        table.on('click', '.edit', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            var redirectUrl = 'linkcategory/' + userId + '/edit';
            window.location.href = redirectUrl;
        });

        /* Formatting function for row details */
        function fnFormatDetails(oTable, nTr) {
            //var aData = oTable.row(nTr).data();
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table>';
            sOut += '<tr><td>Id:</td><td>' + aData.id + '</td></tr>';
            sOut += '<tr><td>Category:</td><td>' + aData.category + '</td></tr>';
            sOut += '<tr><td>Description:</td><td>' + aData.header_text + '</td></tr>';
            sOut += '<tr><td>Status:</td><td>' + aData.status + '</td></tr>';
            sOut += '<tr><td>Other:</td><td>Could provide a link here</td></tr>';
            sOut += '</table>';

            return sOut;
        }


        function handleGroupAction(grid, data) {

            var token = $('meta[name="csrf-token"]').attr('content');
            var form = new FormData();
            form.append("action", data.action);
            form.append("actionType", data.actionType);
            form.append("field", data.actionField);
            form.append("value", data.actionValue);
            form.append("ids", data.ids);
            form.append("_token", token);
            var actionUrl = 'link-category/group-action';

            jQuery.ajax({
                url: actionUrl,
                cache: false,
                data: form,
                dataType: "json",
                type: "POST",
                processData: false,
                contentType: false,
                success: function (data)
                {
                    grid.getDataTable().ajax.reload();
                    if (data.status === 'success') {
                        $('#sidebar-menu').html(data.sidebar);
                        Metronic.alert({
                            type: 'success',
                            icon: 'success',
                            message: data.message,
                            container: $('#errorMessage'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });

                    }
                    else if (data.status === 'fail') {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: data.message,
                            container: grid.getTableWrapper(),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                    siteObjJs.admin.commonJs.showSelectedMenus();
//                    $('.table-group-action-input').val('');
//                    $('.table-group-actions span').html('');
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }
    };

    var handleInputMasks = function () {
        $.extend($.inputmask.defaults, {
            'autounmask': true
        });

        $("#position").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

    };

    var handleBootstrapMaxlength = function () {
        $('#category-form').find("textarea").maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom-left',
            threshold: 10
        });
        $('#category-form-update').find("textarea").maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom-left',
            threshold: 10
        });
    };

    //common method for create and update of category
    var ajaxSubmitForm = function () {
        var formElement = $(this.currentForm);
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serialize();

        $.ajax({
            url: actionUrl,
            cache: false,
            data: formData,
            dataType: "json",
            type: actionType,
            success: function (data)
            {

                if (data.status === 'success') {
                    $('#sidebar-menu').html(data.sidebar);
                    grid.getDataTable().ajax.reload();
                    formElement[0].reset();
                    $('.btn-collapse').trigger('click');
                    Metronic.alert({
                        type: 'success',
                        message: data.message,
                        container: $('#errorMessage'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                    siteObjJs.admin.commonJs.showSelectedMenus();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                var errors = jqXHR.responseJSON;
                var errorsHtml = '';
                $.each(errors, function (key, value) {
                    errorsHtml += value[0] + '<br />';
                });

                Metronic.alert({
                    type: 'danger',
                    message: errorsHtml,
                    container: grid.getTableWrapper(),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });

    };

    $('#showCategoryIcons').on('shown.bs.modal', function () {
        $('span[id^="item-box_"]').on('click', function () {
            $('.link-category-form input[id="category_icon"]').val($.trim($(this).text()));
            $('.link-category-form .category-icon').html('<i class="' + $.trim($(this).text()) + '"></i>');
            $('#showCategoryIcons').modal('hide');
            $('#edit_form #showCategoryIcons').modal('hide');
        });
    });

    var showPopup = function (selector) {
        $('.portlet-body').on('click focus', '#category_icon', function () {
            $(selector + ' #showCategoryIcons').modal('show');
        });
    };


    $('.portlet-body').on('click', '.edit-form-link', function () {
        var category_id = $(this).attr("id");
        var actionUrl = 'link-category/' + category_id + '/edit';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data)
            {
                showPopup('#edit_form');
                $("#edit_form").html(data.form);
                handleBootstrapMaxlength();
                siteObjJs.validation.formValidateInit('#category-form-update', ajaxSubmitForm);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    });

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleTable();
            //handleInputMasks();
            handleBootstrapMaxlength();
            showPopup('.add-form-main');
            //create form - client side validation
            siteObjJs.validation.formValidateInit('.link-category-form', ajaxSubmitForm);
        }

    };

}();