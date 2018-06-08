siteObjJs.admin.reportsJs = function () {
    var confirmRemoveImage, maxFileSize, mimes, confirmRemoveProductImage;
    var token = $('meta[name="csrf-token"]').attr('content');
// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        var logged_in_user_type = $("#logged_in_user_type").val();
        $("#download_btn_userwise_cpr_count").hide();
        //var city_id = $("#city_id").val();
        if (logged_in_user_type == 4 || logged_in_user_type == 8 || logged_in_user_type == 5 || logged_in_user_type == 7) {
            var city_id = $("#city_id").val();
            var center_id = $("#center_id").val();
            $('#city_id').select2('disable');
            $('#center_id').select2('disable');
            $("#download_btn_cust").show();
            handlleCategoryWiseCustomer(center_id, city_id);
        } else {
            $("#download_btn_cust").hide();
            $("div #logged_users #download_btn_user_logged").hide();
            $("div #centerwise-users #download_btn_centerwise_users").hide();
            $("div #centerwise-escalation-div #download_btn_centerwise_escalation").hide();
            $("div #centerwise-notification-div #download_btn_centerwise_notification").hide();
        }

        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#message_type_dropdown").select2('val', '');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');

        });

        $('#centerwise-notification-table').on('click', '.filter-cancel', function (e) {
            $("#message_type_dropdown").select2("val", "");
        });

//        $('body').on('click', '.view-data', function (e) {
//            handleTable();
//        });

//        $('body').on('click', '.view-center-user-login', function (e) {
//            handleCenterUserTable();
//        });

        $('#category-customer-table').on('click', '.filter-cancel', function (e) {
            $("#customer_category").select2("val", "");
        });

        // Display Centers List based on Selected City
        $('body').on('change', '#city_id', function (e) {
            $("#download_btn_cust").hide();
            $("div #logged_users #download_btn_user_logged").hide();
            $("div #centerwise-users #download_btn_centerwise_users").hide();
            $("div #centerwise-escalation-div #download_btn_centerwise_escalation").hide();
            $("div #centerwise-notification-div #download_btn_centerwise_notification").hide();
            //handlleCenterWiseCustomer(0, 0);
            $("#download_btn_userwise_cpr_count").show();
            $("div #logged_users #download_btn_user_logged").show();
            var city_id = $(this).val();
            if (city_id != 0) {
                $("#download_btn_cust").show();
                $("div #logged_users #download_btn_user_logged").show();
                $("div #centerwise-users #download_btn_centerwise_users").show();
                $("div #centerwise-escalation-div #download_btn_centerwise_escalation").show();
                $("div #centerwise-notification-div #download_btn_centerwise_notification").show();
                fetchCentersList(city_id);
            } else {
                $("#download_btn_cust").hide();
                //$("div #logged_users #download_btn_user_logged").hide();
                //$("div #centerwise-users #download_btn_centerwise_users").hide();
                fetchCentersList(0);
            }
            $("#username").val("");
            $("#fullname").val("");
            $("#contact").val("");
            $("#designation").val("");
            var currentUrl = document.location.href;
            var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
            if (lastId == "categorywise-customers") {
                handlleCategoryWiseCustomer(0, city_id);
            } else if (lastId == "centerwise-logged-users") {
                handleCenterUserTable(0, city_id);
            } else if (lastId == "centerwise-users") {
                handleTable(0, city_id);
            } else if (lastId == "centerwise-escalation") {
                handleCenterwiseEscalation(0, city_id);
            } else if (lastId == "userwise-cpr-count") {
                handleUserwiseCPRCount(0, city_id);
            } else if (lastId == "centerwise-notification") {
                handleCenterwiseNotification(0, city_id);
            } else {
                handlleCenterWiseCustomer(0, city_id);
            }

            if (city_id == -1) {
                $("#download_btn_userwise_cpr_count").hide();
            }
        });

        $('body').on('change', '#center_id', function (e) {
            var cityId = $("#city_id").val();
            var centerId = $(this).val();
            if (centerId) {
                $("#download_btn_cust").show();
                $("div #logged_users #download_btn_user_logged").show();
                $("div #centerwise-users #download_btn_centerwise_users").show();
            } else {
                $("#download_btn_cust").hide();
                $("div #logged_users #download_btn_user_logged").hide();
                $("div #centerwise-users #download_btn_centerwise_users").hide();
            }
            $("#username").val("");
            $("#fullname").val("");
            $("#contact").val("");
            $("#designation").val("");
            var currentUrl = document.location.href;
            var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
            if (lastId == "categorywise-customers") {
                handlleCategoryWiseCustomer(centerId, cityId);
            } else if (lastId == "centerwise-logged-users") {
                handleCenterUserTable(centerId, cityId);
            } else if (lastId == "centerwise-users") {
                handleTable(centerId, cityId);
            } else if (lastId == "userwise-cpr-count") {
                handleUserwiseCPRCount(centerId, cityId);
            } else if (lastId == "centerwise-escalation") {
                handleCenterwiseEscalation(centerId, cityId);
            } else if (lastId == "centerwise-notification") {
                handleCenterwiseNotification(centerId, cityId);
            } else {
                handlleCenterWiseCustomer(centerId, cityId);
            }
        });
    };

    $("#centerwise-users-table").on('click', '.filter-submit', function (e) {
        var cityId = $("#city_id").val();
        var centerId = $("#center_id").val();
        var username = $("#username").val();
        var fullname = $("#fullname").val();
        var contact = $("#contact").val();
        var designation = $("#designation").val();
        handleTable(centerId, cityId, username, fullname, contact, designation);
    });
    
    //action on click search button for centerwise escalation
    $("#centerwise-escalation-table").on('click', '.filter-submit', function (e) {
        var cityId = $("#city_id").val();
        var centerId = $("#center_id").val();
        var ATHfullname = $("#ATHfullname").val();
        var Dieticianfullname = $("#Dieticianfullname").val();
        var Memberfullname = $("#Memberfullname").val();
        var mobile_number = $("#mobile_number").val();
        handleCenterwiseEscalation(centerId, cityId, ATHfullname, Dieticianfullname, Memberfullname, mobile_number);
    });
        
    //action on click search button for centerwise notification
    $("#centerwise-notification-table").on('click', '.filter-submit', function (e) {
        var cityId = $("#city_id").val();
        var centerId = $("#center_id").val();
        var Dieticianfullname = $("#Dieticianfullname").val();
        var Memberfullname = $("#Memberfullname").val();
        var mobile_number = $("#mobile_number").val();
        //var NotiType = $("#NotiType").val();
        var NotiType = $('#message_type_dropdown :selected').text();
        handleCenterwiseNotification(centerId, cityId, Dieticianfullname, Memberfullname, mobile_number, NotiType);
    });

    // Function To List Centers Data of selected city
    var fetchCentersList = function (cityId) {
        var currentUrl = document.location.href;
        var lastId = currentUrl.substring(currentUrl.lastIndexOf("/") + 1, currentUrl.length);
        if (lastId == 'reports') {
            var actionUrl = 'reports/centersListByCityType';
        } else {
            var actionUrl = 'centersListByCityType';
        }

        var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {city_id: cityId, report_type: lastId},
            beforeSend: function () {
                //$("#" + unique_id).html("");
                //$("#" + unique_id).html("<img class='center-block' src=" + loader_url + " alt='Loading...' />");
            },
            success: function (data)
            {
                $(".select_center").html("");
                $(".select_center").html(data.centers_list);
                $("select.center_id").select2({
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
                $(".select_center").find("option").eq(0).attr('disabled', 'disabled');
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

    var handleTable = function (centerId, cityId, username, fullname, contact, designation) {
        $('#centerwise-users-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#centerwise-users-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();

                    if (cityId != 0 && centerId == 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-users #download_btn_centerwise_users").hide();
                    } else if (cityId != 0 && centerId != 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-users #download_btn_centerwise_users").hide();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'username', name: 'username'},
                    {data: 'fullname', name: 'fullname'},
                    {data: 'contact', name: 'contact'},
                    {data: 'designation', name: 'designation'},
                    {data: 'action', name: 'action'}
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
                    "url": "view-centerwise-users",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    // "data": {center_id: centerId, city_id: cityId}
                    "data": function (d) {
                        city_id: cityId;
                        center_id: centerId;
                        username:username;
                        fullname:fullname;
                        contact:contact;
                        designation:designation;
                        d.center_id = centerId;
                        d.city_id = cityId;
                        d.username = $('.usernamee').val();
                        d.fullname = $('.fullname').val();
                        d.contact = $('.contact').val();
                        d.designation = $('.designation').val();
                    }

                }
            }

        });
        $('#data-search').hide();
    };

    var handleCenterUserTable = function (centerId, cityId) {
        $('#centerwise-users-logged-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#centerwise-users-logged-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();

                    if (cityId != 0 && centerId == 0 && recCount.recordsTotal == 0) {
                        $("div #logged_users #download_btn_user_logged").hide();
                    } else if (cityId != 0 && centerId != 0 && recCount.recordsTotal == 0) {
                        $("div #logged_users #download_btn_user_logged").hide();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'username', name: 'username'},
                    {data: 'fullname', name: 'fullname'},
                    {data: 'contact', name: 'contact'},
                    {data: 'designation', name: 'designation'},
                    {data: 'login_count', name: 'login_count'},
                    {data: 'last_login_datetime', name: 'last_login_datetime'}
                ],
                rowsGroup: [
                    'name:name'
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
                    "url": "view-centerwise-logged-users",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {center_id: centerId, city_id: cityId}
                }
            }
        });
        $('#data-search').hide();
    };

    var handlleCenterWiseCustomer = function (centerId, cityId) {
        $('#center-customer-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#center-customer-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();

                    if (cityId !== 0 && centerId === 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    } else if (cityId !== 0 && centerId !== 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'dietician_name', name: 'dietician_name'},
                    {data: 'dietician_username', name: 'dietician_username'},
                    {data: 'designation', name: 'designation'}, // designation
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'mobile_number', name: 'mobile_number'}
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
                    "url": "view-centerwise-customer",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {center_id: centerId, city_id: cityId},
                }
            }
        });
        $('#data-search').hide();
    };

    var handlleCategoryWiseCustomer = function (centerId, cityId) {
        $('#category-customer-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#category-customer-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();

                    if (cityId !== 0 && centerId === 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    } else if (cityId !== 0 && centerId !== 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'category', name: 'category'},
                    {data: 'action', name: 'action'}
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
                    "url": "view-categorywise-customer",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    // "data": {center_id: centerId, city_id: cityId},
                    "data": function (d) {
                        d.center_id = centerId;
                        d.city_id = cityId;
                        d.customer_name = $('.customer_name').val();
                        d.mobile_number = $('.mobile_number').val();
                        d.customer_category = $('#customer_category').val();
                    }
                }
            }
        });
        $('#data-search').hide();
    };

    var handleNewUser = function (date) {
        $('#new-user-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#new-user-table'),
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
                    {data: 'first_name', name: 'first_name'},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'created_at', name: 'created_at'},
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
                    "url": "get-new-users",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {date: date},
                },
                fnInitComplete: function () {

                    var datable_length = $("#new-user-table").DataTable().page.info().recordsDisplay
                    if (datable_length > 0) {
                        $("#download_new_users").show();
                    } else {
                        $("#download_new_users").hide();
                    }

                }
            }
        });

        $('#data-search').hide();


    };

    var handleUserwiseCPRCount = function (centerId, cityId) {
        $('#userwise-cpr-count').dataTable().fnDestroy();
        var loader_url = $('.assets_url').val() + "/loading-spinner-default.gif";
        grid = new Datatable();
        grid.init({
            src: $('#userwise-cpr-count'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                    //"processing": "Hang on. Waiting for response..." //add a loading image,simply putting <img src="loader.gif" /> tag.
                    "processing": "<img src=" + loader_url + ">"
                },
                "processing": true, // enable/disable display message box on record load
                "serverSide": true, // enable/disable server side ajax loading
                'initComplete': function () {
                    var recCount = this.api().page.info();

                    if (cityId !== 0 && centerId === 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    } else if (cityId !== 0 && centerId !== 0 && recCount.recordsTotal == 0) {
                        $("#download_btn_cust").hide();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'city_name', name: 'city_name'},
                    {data: 'center_name', name: 'center_name'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'designation', name: 'designation'},
                    {data: 'customer_count', name: 'customer_count'}, // customer_count
                    {data: 'cpr_usage_count', name: 'cpr_usage_count'},
                    {data: 'percentage', name: 'percentage', orderable: false, searchable: false}
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
                    "url": "view-userwise-cpr-count",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {center_id: centerId, city_id: cityId},
                }
            }
        });
        $('#data-search').hide();
    };

    var handleDatepicker = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $(".date").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            endDate: '+0d',
        }).on('changeDate', function (selected) {
            handleNewUser($(this).val());
        });
    };

    var handleCenterwiseEscalation = function (centerId, cityId, ATHfullname = '', Dieticianfullname = '', Memberfullname = '', mobile_number = '') {
        $('#centerwise-escalation-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#centerwise-escalation-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();
                  
                    if (cityId != 0 && centerId == 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-escalation-div #download_btn_centerwise_escalation").hide();
                    } else if (cityId != 0 && centerId != 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-escalation-div #download_btn_centerwise_escalation").hide();
                    } else if((ATHfullname !== ' ' || Dieticianfullname !== ' ' || Memberfullname !== ' ' || mobile_number !== ' ') && recCount.recordsDisplay === 0){
                        $("div #centerwise-escalation-div #download_btn_centerwise_escalation").hide();
                    }else if(recCount.recordsDisplay !== 0){
                        $("div #centerwise-escalation-div #download_btn_centerwise_escalation").show();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'ATHfullname', name: 'ATHfullname'},
                    {data: 'Dieticianfullname', name: 'Dieticianfullname'},
                    {data: 'Memberfullname', name: 'Memberfullname'},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'EDate', name: 'EDate'},
                    {data: 'action', name: 'action'}
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
                    "url": "view-centerwise-escalation",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    //"data": {center_id: centerId, city_id: cityId}
                    "data": function (d) {
                        city_id: cityId;
                        center_id: centerId;
                        ATHfullname:ATHfullname;
                        Dieticianfullname:Dieticianfullname;
                        Memberfullname:Memberfullname;
                        mobile_number:mobile_number;
                        d.center_id = centerId;
                        d.city_id = cityId;
                        d.ATHfullname = $('.ATHfullname').val();
                        d.Dieticianfullname = $('.Dieticianfullname').val();
                        d.Memberfullname = $('.Memberfullname').val();
                        d.mobile_number = $('.mobile_number').val();
                    }

                }
            }

        });
        $('#data-search').hide();
    };
    
    var handleCenterwiseNotification = function (centerId, cityId, Dieticianfullname = '', Memberfullname = '', mobile_number = '', NotiType = '') {
        $('#centerwise-notification-table').dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#centerwise-notification-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                'initComplete': function () {
                    var recCount = this.api().page.info();
                    
                    if (cityId != 0 && centerId == 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-notification-div #download_btn_centerwise_notification").hide();
                    } else if (cityId != 0 && centerId != 0 && recCount.recordsTotal == 0) {
                        $("div #centerwise-notification-div #download_btn_centerwise_notification").hide();
                    }else if((Dieticianfullname !== ' ' || Memberfullname !== ' ' || mobile_number !== ' ' || NotiType !== ' ') && recCount.recordsDisplay === 0){
                        $("div #centerwise-notification-div #download_btn_centerwise_notification").hide();
                    }else if(recCount.recordsDisplay !== 0){
                        $("div #centerwise-notification-div #download_btn_centerwise_notification").show();
                    }
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: null, name: 'rownum', searchable: false},
                    {data: 'name', name: 'name'}, //city name
                    {data: 'center_name', name: 'center_name'},
                    {data: 'Dieticianfullname', name: 'Dieticianfullname'},
                    {data: 'Memberfullname', name: 'Memberfullname'},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'NotiType', name: 'NotiType'},
                    {data: 'notification_count', name: 'notification_count'},
                    {data: 'action', name: 'action'}
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
                    "url": "view-centerwise-notification",
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    //"data": {center_id: centerId, city_id: cityId}
                    "data": function (d) {
                        city_id: cityId;
                        center_id: centerId;
                        Dieticianfullname:Dieticianfullname;
                        Memberfullname:Memberfullname;
                        mobile_number:mobile_number;
                        NotiType:NotiType;
                        d.center_id = centerId;
                        d.city_id = cityId;
                        d.Dieticianfullname = $('.Dieticianfullname').val();
                        d.Memberfullname = $('.Memberfullname').val();
                        d.mobile_number = $('.mobile_number').val();
                        d.NotiType = $('#message_type_dropdown').val();
                    }

                }
            }

        });
        $('#data-search').hide();
    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleDatepicker();
        }
    };

}();
