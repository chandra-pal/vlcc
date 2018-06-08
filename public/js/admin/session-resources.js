siteObjJs.admin.sessionResourcesJs = function () {

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

        $('body').on("change", "#center_select", function () {
            var d = new Date($("#availability_date").val());
            var availability_date = $("#availability_date").val();
            var center_id = $(this).val();
            var flag = $("ul li.active").attr("id");
            handleAvailabilityCalendar(availability_date, center_id, flag);
        });

//        $('body').on("click", ".tab-click", function () {
//            var flag = $(this).attr("id");
//            if ($("#center_select").val() != '' && $("#availability_date").val() != '') {
//                handleAvailabilityCalendar($("#availability_date").val(), $("#center_select").val(), flag);
//            }
//        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var flag = $(e.target).attr("data-flag");
            if ($("#center_select").val() != '' && $("#availability_date").val() != '') {
                handleAvailabilityCalendar($("#availability_date").val(), $("#center_select").val(), flag);
            }
        });
    };

    var handleAvailabilityCalendar = function (date, center, flag) {
        $('#resource_calender_' + flag).fullCalendar('destroy');
        //$('#resource_calender_' + flag).fullCalendar('removeEvents')

        var dateAr = $("#availability_date").val().split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];

        $('#resource_calender_' + flag).fullCalendar({
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            defaultView: 'agendaDay',
            defaultDate: newDate,
            editable: false,
            selectable: true,
            eventLimit: true, // allow "more" link when too many events
            header: {
                //left: 'today',
                left:'prev title next',
                center: '',
                right: 'today'
            },
            "minTime": $("#minTime").val(),
            "maxTime": $("#maxTime1").val(),
            "height": "auto",
            views: {
                agendaTwoDay: {
                    type: 'agenda',
                    groupByResource: true
                }
            },
            "resources": function (callback) {
                $.ajax({
                    url: "session-resources/fetch-resource",
                    dataType: 'json',
                    type: "POST",
                    "data": {center_id: center, flag: flag},
                    success: function (data) {
                        callback(data);
                    }
                });
            },

            "events": function (start, end, timezone, callback) {
                $.ajax({
                    url: "session-resources/resource-availability",
                    dataType: 'json',
                    type: "POST",
                    "data": {center_id: center, availability_date: date, flag: flag},
                    success: function (data) {
                        callback(data);
                        // $('#resource_calender_' + flag).fullCalendar('refetchEvents');
                    }
                });
            },
        });
    };

    /*var sessionResourceDate = function (dateArray) {

        $('#availability_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: 1,
            datesDisabled: dateArray,
        }).on('changeDate', function (e) {
            var flag = $("ul li.active").attr("id");
            if ($("#center_select").val() != '' && $(this).val() != '') {
                handleAvailabilityCalendar($(this).val(), $("#center_select").val(), flag);
            }

        });
    };*/

    var sessionResourceDate = function (dateArray) {
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
    };

    $('body').on("click", ".download-resource-report", function () {

        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var center_id = $("#center_select").val();
        var flag = $("ul li.active").attr("id");
        var arrStartDate = fromDate.split("-");
        var date1 = new Date(arrStartDate[2], arrStartDate[1], arrStartDate[0]);
        var arrEndDate = toDate.split("-");
        var date2 = new Date(arrEndDate[2], arrEndDate[1], arrEndDate[0]);
        if (date1 > date2){
            $("#date-range-error").css('display','block');
            $("#select-center-error").css('display','none');
            return false;
        } else if(center_id ==null || center_id == ''){
            $("#select-center-error").css('display','block');
            $("#date-range-error").css('display','none');
            return false;
        } else {
                $("#date-range-error").css('display','none');
                $("#select-center-error").css('display','none');
                $.ajax({
                        url: "session-resources/data-download",
                        dataType: 'json',
                        type: "POST",
                        "data": {center_id: center_id, flag: flag, from_date: fromDate, to_date: toDate},
                    success: function (data) {
                           if (data.msg == 'success') {
                                    var url = window.location.href + '/download';
                                   window.location.replace(url);
                               }
                        }
               });
          }
    });

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            sessionResourceDate();
        }

    };
}();