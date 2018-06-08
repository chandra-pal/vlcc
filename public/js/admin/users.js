siteObjJs.admin.usersJs = function () {
    var confirmRemoveImage, maxFileSize, mimes;
    var initializeListener = function () {
        //bind to onchange event of avatar input field
        $('#avatar').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.validateUserJs.maxFileSize;
                    $('#file-error').text(error);
                    return false;
                }

                var ext = $('#avatar').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.validateUserJs.mimes;
                    $('#file-error').text(error);
                    return false;
                } else
                {
                    $('#file-error').text('');
                }

            }

        });

        //if "remove" button is clicked from input, clear error messages
        $("a.fileinput-exists").on('click', function () {
            $('#file-error').text('');
        });

        $('#users_datatable_ajax').on('click', '.filter-cancel', function (e) {
            $("#status_search").select2("val", "");
        });

        $('body').on('click', '.sync_user', function (e) {
            var messageType = 'success';
            var icon = 'check';
            var actionUrl = "user/sync-user";
            var user_name = $(this).attr("data-id");
            var user_id = $(this).attr("data-value");
            var current_object = $(this).data("data-id", user_name);
            current_object.css('display', 'none');
            var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "POST",
                async:false,
                "data": {user_name: user_name, user_id: user_id},
                beforeSend: function () {
                    $(".loading_message").after('<div id="prefix_336623526038" class="Metronic-alerts alert alert-success fade in"><img style="display: block; float: left" src='+loader_url+' alt="Loading..." /><span style="display:block;margin-left:5%; font-weight: bold;">Please do not refresh page, Importing User ' + user_name + '....</span></div>');
                    $("html, body").animate({scrollTop: 0}, 1000);
                },
                success: function (data)
                {
                    current_object.css('display', 'block');
                    $(".loading_message").html("");
                    if (data.status === 'error') {
                        icon = 'times';
                        messageType = 'danger';
                    }
                    Metronic.alert({
                        type: messageType,
                        icon: icon,
                        message: data.message,
                        container: ajaxMessageIdentifier,
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
    };

    var grid;

    //Identifiers
    var tableIdentifier = '#users_datatable_ajax';
    var formIdentifier = '#admin-user-form';
    var ajaxMessageIdentifier = $('#ajax-response-text');

    //urls
    var gridDataUrl = adminUrl + '/user/data';
    var groupActionUrl = adminUrl + '/user/group-action';
    var fieldCheckUrl = adminUrl + '/user/check-avalability';

    //other
    var token = $('meta[name="csrf-token"]').attr('content');
    var passUsername = false;
    var deleteMessage, restoreMessage;

    var initPickers = function () {
//init date pickers
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            autoclose: true
        });
    };

    var handleTable = function () {
        grid = new Datatable();
        grid.init({
            src: $(tableIdentifier),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'bStateSave': true,
                'lengthMenu': siteObjJs.admin.commonJs.constants.gridLengthMenu,
                'pageLength': siteObjJs.admin.commonJs.constants.recordsPerPage,
                'columns': [
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'id', name: 'id', orderable: false},
                    {data: 'avatar', name: 'avatar', orderable: false, searchable: false},
                    {data: 'username', name: 'username'},
                    {data: 'email', name: 'email'},
                    {data: 'admin_center_name', name: 'admin_center_name', orderable: false, searchable: false},
                    {data: 'links', name: 'links', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                'drawCallback': function (settings) {
                    var api = this.api();
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    var displayLength = settings._iDisplayLength;
                    api.column(1, {page: 'current'}).data().each(function (group, i) {
                        recNum = ((page * displayLength) + i + 1);
                        $(rows).eq(i).children('td:nth-child(2)').html(recNum);
                    });
                },
                'ajax': {
                    'url': gridDataUrl,
                    'type': 'GET'
                },
                'order': []
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $('.table-group-action-input', grid.getTableWrapper());
            var actionType = action.attr('data-actionType');
            var actionField = action.attr('data-actionField');
            var actionValue = action.val();
            var formAction = 'update';
            var message = siteObjJs.admin.usersJs.deleteMessage;
            if (actionValue == 'restore') {
                message = siteObjJs.admin.usersJs.restoreMessage;
            }

            if (actionValue !== '' && grid.getSelectedRowsCount() > 0) {

                var formdata = {
                    action: formAction,
                    actionField: actionField,
                    actionType: actionType,
                    actionValue: actionValue,
                    ids: grid.getSelectedRows()
                };

                if ((actionValue == 'delete' || actionValue == 'delete-hard') || actionValue == 'restore') {
                    formdata['action'] = actionValue;

                    bootbox.confirm({buttons: {confirm: {label: 'CONFIRM'}}, message: message, callback: function (result) {
                            if (result === false) {
                                return;
                            }
                            handleGroupAction(grid, formdata);
                        }});
                } else {
                    handleGroupAction(grid, formdata);
                }

            } else if (action.val() === '') {
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
            var action = $(this).attr('data-action');
            var message = $(this).attr('data-message');
            bootbox.confirm(
                    {
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
        table.on('click', '.edit', function (e) {
            e.preventDefault();
            window.location.href = adminUrl + '/user/' + $(this).attr('data-id') + '/edit';
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
                success: function (data)
                {
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
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }
    };

    var checkUsername = function () {
        $("#center_id").find("option").eq(0).attr('disabled', 'disabled');
        $('form' + formIdentifier).on('click', '#username1_checker', function (e) {
            validateUsername();
        });

        $('form' + formIdentifier).on('keyup blur focus', '#user_name', function (e) {
            validateUsername();
        });

    };

    var validateSubmit = function () {
        if (passUsername) {
            handleAjaxRequest();
        } else {
            $('html, body').animate({scrollTop: 10}, 600, function () {
                $('#username1_checker').click();
            });
        }
        return false;
    };

    var handleAjaxRequest = function () {

        var formElement = $(formIdentifier);
        var icon = 'check';
        var messageType = 'success';
        var formData = formElement.serializeArray();
        var actionUrl = formElement.attr('action');
        var actionType = formElement.attr('method');
        var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        var image = $('#avatar')[0].files[0];
        if (image) {
            if (image.size > 2097152) {
                var error = siteObjJs.admin.validateUserJs.maxFileSize;
                $('#file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            var ext = $('#avatar').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                var error = siteObjJs.admin.validateUserJs.mimes;
                $('#file-error').text(error);
                $('html, body').animate({scrollTop: 450}, 500);
                return false;
            }
            form.append('avatar', image);
        }
        form.append('_token', token);
        if ($('input[name=_method]').val()) {
            form.append('_method', $('input[name=_method]').val());
        }
        $(".create-user-submit").attr('disabled', 'disabled');

        $.ajax({
            url: actionUrl,
            type: actionType,
            data: form,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loader").html("");
                $("#loader").html("<img class='center-block' src=" + loader_url + " alt='Loading...' />");
                $("html, body").animate({scrollTop: $('.page-title').offset().top}, 2000);
            },
            success: function (data)
            {
                if (data.status === 'error')
                {
                    icon = 'times';
                    messageType = 'danger';

                }

                Metronic.alert({
                    type: messageType,
                    icon: icon,
                    message: data.message,
                    container: ajaxMessageIdentifier,
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });

                if (data.status === 'success')
                {
                    setTimeout(function () {
                        window.location.href = data.redirect
                    }, 3000);
                }

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
                    container: ajaxMessageIdentifier,
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        }
        );
    };
    function validateUsername() {
        var input = $('#user_name');
        var lengthAllow = input.attr('minlength');
        var length = input.val().length;
        var pop = $('#username1_checker');
        var btn = $('#username1_checker');
        btn.attr('disabled', true);
        if (lengthAllow <= (length)) {

            input.attr('readonly', false).addClass('spinner');
            $('#username1_checker').removeClass('disabled');
            var form = new FormData();
            form.append('action', '');
            form.append('actionType', '');
            form.append('field', 'username');
            form.append('value', input.val());
            form.append('_token', token);

            $.ajax({
                url: fieldCheckUrl,
                cache: false,
                dataType: 'json',
                type: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function (data)
                {
                    btn.attr('disabled', false);
                    input.attr('readonly', false).removeClass('spinner');

                    if (data.status === 'success') {
                        input.closest('.form-group').removeClass('has-error').addClass('has-success');
                        pop.popover('destroy');
                        pop.popover({
                            'html': true,
                            'placement': (Metronic.isRTL() ? 'left' : 'right'),
                            'container': 'body',
                            'content': data.message
                        });
                        pop.popover('show');
                        pop.data('bs.popover').tip().removeClass('error').addClass('success');

                        passUsername = true;

                    } else if (data.status === 'fail') {
                        input.closest('.form-group').removeClass('has-success').addClass('has-error');
                        pop.popover('destroy');
                        pop.popover({
                            'html': true,
                            'placement': (Metronic.isRTL() ? 'left' : 'right'),
                            'container': 'body',
                            'content': data.message,
                        });
                        pop.popover('show');
                        pop.data('bs.popover').tip().removeClass('success').addClass('error');
                        Metronic.setLastPopedPopover(pop);

                        passUsername = false;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    input.closest('.form-group').removeClass('has-success').addClass('has-error');
                    pop.popover('destroy');
                    pop.popover({
                        'html': true,
                        'placement': (Metronic.isRTL() ? 'left' : 'right'),
                        'container': 'body',
                        'content': errorThrown,
                    });
                    pop.popover('show');
                    pop.data('bs.popover').tip().removeClass('success').addClass('error');
                    Metronic.setLastPopedPopover(pop);

                    passUsername = false;
                }
            });
        } else {
            input.closest('.form-group').removeClass('has-success').addClass('has-error');
            pop.popover('destroy');
            pop.popover({
                'html': true,
                'placement': (Metronic.isRTL() ? 'left' : 'right'),
                'container': 'body',
                'content': 'Please enter a username to check its availability.',
            });
            pop.popover('show');
            pop.data('bs.popover').tip().removeClass('success').addClass('error');
            Metronic.setLastPopedPopover(pop);
        }

        return passUsername;
    }


    return {
        init: function (identifier, url) {
            initializeListener();
            initPickers();
            gridDataUrl = url || gridDataUrl;
            tableIdentifier = identifier || tableIdentifier;
            handleTable();
        },
        initCreate: function () {
            siteObjJs.validation.formValidateInit(formIdentifier, validateSubmit);
            checkUsername();
        },
        initEdit: function () {
            initializeListener();
            siteObjJs.validation.formValidateInit(formIdentifier, handleAjaxRequest);
        }

    };
}();