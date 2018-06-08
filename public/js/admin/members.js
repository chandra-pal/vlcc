siteObjJs.admin.membersJs = function () {
    var pieChartData = [];
    // Initialize all the page-specific event listeners here.
    var initializeListener = function () {
        //code
        var selectedVal = $('#center_select').val();
        if (selectedVal != '')
        {
            handleTable('members-table', selectedVal);
        } else {
            initalizeTable();
        }
        $('body').on('change', '#center_select', function (e) {
            $("#center_error").html('');
            if ($(this).val() != 0)
            {
                handleTable('members-table', $(this).val());
            } else
            {
                $('#members-table-body').empty("");
            }
        });

        //edit link click for slimming head & center head
        $(".edit-link").live("click", function (e) {
            var memberId = $(this).attr('data-id');
            var actionUrl = 'members/' + memberId + '/display-member-details';
           $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            "data": {member_id: memberId},
            
            success: function (data)
            {
                //handleSelectBoxes();
                //bootbox.confirm({
                bootbox.alert({
                //message: 'After confirming record will be edited.'+data.form,
                message: data.form,
                buttons: {
                    ok: {
                    label: 'SUBMIT'
                    }
                },
               // buttons: {submit: {label: 'Submit'}},
               //size: "large",
                callback: function (result) {
                    if (result === false) {
                        return;
                    } else {
                       var dieticianId = $('#dietician_id').val();
                       updateMember(memberId,dieticianId);
                        
                    }
                }
            });
            handleSelectBoxes();
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
        
        var updateMember=function(memberId,dieticianId){
            
        var actionUrl = 'members/' + memberId + '/edit-member';
        var messageType = "success";
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "POST",
            "data": {member_id: memberId,dietician_id: dieticianId},
            success: function (data)
            {
                if (data.status == "success") {
                    
                }
                Metronic.alert({
//                            type: messageType,
//                            icon: icon,
//                            message: data.message,
//                            container: $('#ajax-response-text'),
//                            place: 'prepend',
//                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        type: messageType,
                        message: data.message,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
               // $("#otp_id").val(data.otp_id);
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
        
        
    };

    var initializeMemberDetailView = function () {
        $('.page-group-box').live('click', function (e) {
            var target = $(e.target);
            if (!target.is('a') && !target.is('i')) {
                var dataPageId = $(this).attr('data-page-id');
                $.each($('.roomBox'), function () {
                    if ($(this).hasClass('roomBox-' + dataPageId)) {
                        $(this).toggleClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            }
        });
    };

    // Method to fetch and place edit form with data using ajax call
    var viewClientDetailsPage = function () {
        $('.portlet-body').on('click', '.view-link', function () {
            var cat_id = $(this).attr("id");
            var actionUrl = 'members/' + cat_id + '/display';
            window.location.href = actionUrl;
        });
    };

    var handleTable = function (tableId, centerId) {
        $('#' + tableId).dataTable().fnDestroy();
        grid = new Datatable();
        grid.init({
            src: $('#members-table'),
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
                    {data: 'mobile_number', name: 'mobile_number', visible: false}, //,
                    {data: 'full_name', name: 'full_name'},
                    {data: 'mobile_number', name: 'mobile_number'},
                    {data: 'age', name: 'age'},
                    {data: 'gender', name: 'gender'},
                    {data: 'package_name', name: 'package_name'},
                    {data: 'action', name: 'action'},
                    {data: 'first_name', name: 'first_name', visible: false},
                    //{data: 'start_date', name: 'start_date'},
                    //{data: 'end_date', name: 'end_date'},
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
                    "url": "members/data",
                    "type": "GET",
                    "data": {'centerId': centerId},
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

    var handlePackagesTable = function (tableId, memberId) {
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
                    {data: 'crm_package_id', name: 'crm_package_id'}, //,
                    {data: 'package_title', name: 'package_title'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'total_payment', name: 'total_payment'},
                    {data: 'payment_made', name: 'payment_made'},
                    {data: 'services', name: 'services', visible: false}
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

                    api.column(1, {page: 'current'}).data().each(function (group, i) {
                        $(rows).eq(i).addClass('page-group-box page-group-box-' + group + ' cursor').attr('data-page-id', group);
                    });
                    
                    /*** Code to append Banner Sub details in Table row ***/
                    api.column(8, {page: 'current'}).data().each(function (group, i) {
                        var fullHtml = '';
                        var trWidth = $('#members-packages-table').outerWidth();

                        fullHtml = '<tr><td colspan="4" class="page-table-container roomBox roomBox-' + group[0].package_id + ' hidden cursor"><div class="table-responsive-admin" style="width:' + trWidth + 'px" > <table class="table table-bordered table-inside-td">';
                        var heading = '<th>#</th><th>CRM Service Id</th><th>Name</th><th>Validity (In days)</th><th>Services Booked & Consumed</th><th>Start Date</th><th>End Date</th>';

                        var package_id;
                        fullHtml += '<tr class="bg-grey-steel">' + heading + '</tr>';

                        $.each(group, function (key, value) {

                            package_id = value.package_id;

                            fullHtml += '<tr>';
                            fullHtml += '<td>' + (key + 1) + '</td>';

                            var crmServiceId = '<td>';
                            crmServiceId += value.crm_service_id;
                            crmServiceId += '</td>';

                            var serviceName = '<td>';
                            serviceName += value.service_name;
                            serviceName += '</td>';

                            var serviceValidity = '<td>';
                            serviceValidity += value.service_validity;
                            serviceValidity += '</td>';

                            var servicesBooked = '<td>';
                            servicesBooked += '<b>Booked : </b> ' + value.services_booked;
                            servicesBooked += '<br><b>Consumed : </b> ' + value.services_consumed;
                            servicesBooked += '</td>';

                            var startDate = '<td>';
                            startDate += value.start_date;
                            startDate += '</td>';

                            var endDate = '<td>';
                            endDate += value.end_date;
                            endDate += '</td>';

                            fullHtml += crmServiceId;
                            fullHtml += serviceName;
                            fullHtml += serviceValidity;
                            fullHtml += servicesBooked;
                            fullHtml += startDate;
                            fullHtml += endDate;
                            fullHtml += '</tr>';
                        });
                        fullHtml += '</td></tr></table></div></td></tr>';

                        $('.page-group-box-' + group[0].package_id).after(fullHtml);

                        var editLink = '';

                        $(rows).eq(i).children('td:nth-child(3)').append(editLink);
                    });
                },
                "ajax": {
                    "url": adminUrl + "/member/packages",
                    "type": "POST",
                    "data": {'memberId': memberId},
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
    
    var handleSelectBoxes = function () {
        $('.bs-select').each(function () {
            $(this).selectpicker({iconBase: 'fa', tickIcon: 'fa-check', noneSelectedText: $(this).attr('data-label-text')});
        });
    };

    var initalizeTable = function () {
        $('#members-table').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "bPaginate": false,
            "bSort": false,
            "emptyTable": "No data available in table"
        });
    };
    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            //handleTable();
            viewClientDetailsPage();
            handleSelectBoxes();
        },
        initPieCharts: function () {
            initializeMemberDetailView();
            var data = siteObjJs.admin.membersJs.pieChartData;
            var memberId = siteObjJs.admin.membersJs.memberId;
            var yellow = "#FFCE5D";
            var green = "#1D8945";
            var blue = "#3762BE";
            var purple = "#69398D";
            $.plot('#donut', data, {
                series: {
                    pie: {
                        show: true,
                        innerRadius: 0.7,
                    },
                },
                colors: [yellow, green, blue, purple],
                legend: {
                    show: true,
                },
                grid: {
                    hoverable: true
                }
            });
            handlePackagesTable('members-packages-table', memberId);
        }
    };
}();