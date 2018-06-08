siteObjJs.admin.notificationJs = function () {

    // Initialize all the page-specific event listeners here.

    var initializeListener = function () {

        jQuery.fn.shake = function () {
            this.each(function (i) {
                $(this).css({"position": "relative"});
                for (var x = 1; x <= 3; x++) {
                    $(this).animate({left: -5}, 50).animate({left: 0}, 30).animate({left: 5}, 30).animate({left: 0}, 50);
                }
            });
            return this;
        }

        $('body').on("click", ".notification-item", function () {
            var notificationId = $(this).attr("id");
            var redirectUrl = $(this).attr("data-redirect");//.split(' ')[1];
            var actionUrl = adminUrl + '/notifications/read-notifications';
            var className = $(this).attr("class").split(' ')[1];
            if (className == "unread") {
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    "data": {notification_id: notificationId},
                    success: function (data)
                    {
                        if (data.status == "success") {
                            window.location.href = adminUrl + "/" + redirectUrl;
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
    };
    var fetchNotifications = function () {

        var category_id = $(this).attr("id");
        var actionUrl = adminUrl + '/notifications/data';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data)
            {
                var html = "";
                var unread_flag = false;
                var unread_count = 0;
                if (data.length !== 0) {
                    $.each(data, function (key, val) {
                        var className = "unread";
                        if (val.read_status == 1) {
                            className = "read";
                            unread_flag = true;
                            //$('.icon-bell').addClass('button__badge');
                        } else {
                            unread_count++;
                        }
                        //html += "<li><a data-redirect=" + val.deep_linking + " class='notification-item " + className + "'  id=" + val.id + ">" + val.notification_text + "</a></li>";
                        html += '<li><a class="notification-item ' + className + '" data-redirect=' + val.deep_linking + ' href="javascript:;" id=' + val.id + '><span class="details">' + val.notification_text + ' </span></a></li>';
                    });
                    $('#header_notification_bar').find('span.badge.badge-danger').html(unread_count);

                } else {
                    $('.glyphicon-bell').parents('ul.navbar-nav').find('ul.dropdown-menu.notification-parent-ul').css('max-height', '60px');
                    $('.glyphicon-bell').parents('ul.navbar-nav').find('ul.dropdown-menu.notification-parent-ul').css('overflow-y', 'hidden');
                    html = '<li><a href="javascript:;"><span class="details">No notifications. </span></a></li>';
                }
                if (unread_flag == true) {
                    $('.glyphicon-bell').addClass('button__badge');
                    $('.glyphicon-bell').shake();
                }
                $('#notification-list').html(html);
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

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            fetchNotifications();
        }
    };
}();
