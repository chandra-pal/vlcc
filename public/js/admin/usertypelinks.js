siteObjJs.admin.usertypelinks = function () {

    var validateCheckboxes = function () {
        $('#roles-permissions-form').on('change', '.caption input[type="checkbox"]', function () {
            var portlet = $(this).closest("div.portlet");
            if (this.checked) {
                portlet.find('.checker span').addClass('checked');
                portlet.find('input[type="checkbox"]').attr('checked', 'checked');
            } else {
                portlet.find('.checker span').removeClass('checked');
                portlet.find('input[type="checkbox"]').removeAttr('checked');
            }
        });

        $('#roles-permissions-form').on('change', '#selectall', function () {
            if (this.checked) {
                $('div.portlet').find('.checker span').addClass('checked');
                $('div.portlet').find('input[type="checkbox"]').attr('checked', 'checked');
            } else {
                $('div.portlet').find('.checker span').removeClass('checked');
                $('div.portlet').find('input[type="checkbox"]').removeAttr('checked');
            }
        });

        var roleVal = $('#select-role').val();
        selectRole(roleVal);
    };

    $('#roles-permissions-form').on('change', '#select-role', function () {
        var roleVal = $(this).val();
        selectRole(roleVal);
    });

    $('#roles-permissions-form').on('click', '.reset-form', function () {
        $('input:checkbox').removeAttr('checked');
        $('#select-role').prop('selectedIndex', 0);
        $(".select2-chosen").text('Please Select');
    });

    function selectRole(roleVal) {
        if (roleVal) {
            $('.permissions-list').removeClass('hidden');
        } else {
            $('.permissions-list').addClass('hidden');
        }
    }

    $('.portlet-body').on('change', '#select-role', function () {
        var userTypeId = $("#select-role option:selected").val();
        var actionUrl = 'usertype-links/' + userTypeId + '/edit';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data)
            {
                $(".permissions-list").html(data.form);
                $('.permissions-list').removeClass('hidden');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    });

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
                    Metronic.alert({
                        type: 'success',
                        message: data.message,
                        container: $('#errorMessage'),
                        place: 'prepend'
                    });
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
                    container: $('#errorMessage'),
                    place: 'prepend'
                });
            }
        });
        return false;
    };

    return {
        //main function to initiate the module
        init: function () {
            validateCheckboxes();
            siteObjJs.validation.formValidateInit('#roles-permissions-form', ajaxSubmitForm);
        }

    };

}();