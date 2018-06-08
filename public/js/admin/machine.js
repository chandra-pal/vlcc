siteObjJs.admin.machineJs = function () {
    var grid;
    // Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        $("#center_id").find("option").eq(0).attr('disabled', 'disabled');
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");

            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#" + formId).find("input[type=text], input[type=file], textarea").val("");
            $("#machine_type_id").select2('val', '');
            $("#center_id").select2('val', '');
            $("#" + formId).find("span#sel-machine-type").text("");
            $("#machine-name").css("width", "100%");
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        //reset the search filters on click of cross button

        $('#MachineList').find("tr#search-rec").on('click', '.filter-cancel', function (e) {
            $("#cname").select2("val", "");
            $("#status-drop-down-search").select2("val", "");
        });

    };

    //Adding span to display machine type on dropdown selection

    $('#create-machine').on('change', '#machine_type_id', function (e) {
        $("#machine-name").css("width", "100px");
        $("#sel-machine-type").css("float", "left");
        $("#sel-machine-type").css("padding-left", "15px");
        var machineType = $("#machine_type_id option:selected").text();
        var machineType1 = machineType + ' -';
        if (machineType === 'Select Machine Type') {
            $('#sel-machine-type').text('');
            $("#machine-name").css("width", "100%");
        } else {
            $('#sel-machine-type').text(machineType1);
            
        }
    });

    //Edit machine type & machine name

    $('#edit_form').on('change', '#machine_type_id', function (e) {
        $("#edit_form #machine-name").css("width", "100px");
        $("#edit_form #sel-machine-type").css("float", "left");
        $("#edit_form #sel-machine-type").css("padding-left", "15px");
        //var mdata = $("#edit_form #machine-name").val("");
        var machineTypeE = $("#edit_form #machine_type_id option:selected").text();
        var machineTypeE1 = machineTypeE + ' -';
        if (machineTypeE === 'Select Machine Type') {
            $('#edit_form #sel-machine-type').text('');
            $("#edit_form #machine-name").css("width", "100%");
        } else {
            $('#edit_form #sel-machine-type').text(machineTypeE1);
            $("#edit_form #machine-name").val("");
        }

//          $("#edit_form #machine-name").css("width", "100px");
//          $("#edit_form #sel-machine-type").css("float", "left");
//          $("#edit_form #sel-machine-type").css("padding-left", "15px");
    });

    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var machine_id = $(this).attr("id");
            var actionUrl = 'machines/' + machine_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $("#edit_form").html(data.form);
                    $("#edit-machine #machine_type_id").select2();
                    $("#edit-machine #center_id").select2({
                        allowClear: true,
                        placeholder: $(this).attr('data-label-text'),
                        width: null
                    });
                    $("#edit-machine #center_id").find("option").eq(0).attr('disabled', 'disabled');
                    siteObjJs.validation.formValidateInit('#edit-machine', handleAjaxRequest);
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


    // Common method to handle add and edit ajax request and reponse

    var handleAjaxRequest = function () {
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var formData = formElement.serialize();
        var icon = "check";
        var messageType = "success";
        $.ajax({
            url: actionUrl,
            cache: false,
            type: actionType,
            dataType: "json",
            data: formData,
            success: function (data) {
                //data: return data from server
                if (data.status === "error") {
                    icon = "times";
                    messageType = "danger";
                }

                //Empty the form fields
                formElement.find("input[type=text], textarea").val("");
                $('#center_id').select2('val', '');
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
        }
        );
    }

    var handleTable = function () {
        grid = new Datatable();
        grid.init({
            src: $('#MachineList'),
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
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'cname', name: 'cname'},
                    {data: 'machine_type', name: 'machine_type'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    // {data: 'status_format', name: 'status_format', visible: false},
                    {data: 'status_format', name: 'status_format'},
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
                    "url": "machines/data",
                    "type": "GET"
                }
            }
        });

        function handleGroupAction(grid, data) {

            var token = $('meta[name="csrf-token"]').attr('content');
            var form = new FormData();
            form.append("action", data.action);
            form.append("actionType", data.actionType);
            form.append("field", data.actionField);
            form.append("value", data.actionValue);
            form.append("id", data.id);
            form.append("_token", token);
            var actionUrl = 'machines/group-action';

            jQuery.ajax({
                url: actionUrl,
                cache: false,
                data: form,
                dataType: "json",
                type: "POST",
                processData: false,
                contentType: false,
                success: function (data) {
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

                    } else if (data.status === 'fail') {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: data.message,
                            container: $('#errorMessage'),
                            place: 'prepend'
                        });
                    }
                    siteObjJs.admin.commonJs.showSelectedMenus();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleTable();
            fetchDataForEdit();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-machine', handleAjaxRequest);
        }

    };
}();