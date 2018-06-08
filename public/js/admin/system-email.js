siteObjJs.admin.systemEmailJs = function () {

    var token = $('meta[name="csrf-token"]').attr('content');

// Initialize all the page-specific event listeners here.

    var initializeListener = function () {
        $('body').on('change', '#selectEmailTemplate', function () {
            resetForm();
            var thisVal = $(this).val();
            if (thisVal == "")
            {
                $('.edit-form-main').hide();
                $('.add-form-main').show();
                $('.add-form-main > .form').addClass('display-hide');
            }
            else if (thisVal == "new") {
                $('a.box-expand-form').removeClass('expand');
                $('a.box-expand-form').addClass('collapse');
                $('input[name=_method]').val("POST");
                $('.edit-form-main').hide();
                $('.add-form-main').show();
                $('.add-form-main > .form').removeClass('display-hide');
                $('input[name=name]').removeAttr('readonly');
                $('html, body').animate({scrollTop: 280}, 500);
                $('.add-form-main form :input:visible:enabled:first').focus();

            }
            else
            {
                $('.edit-form-main').hide();
                $('.add-form-main').hide();
                fetchDataForEdit(thisVal);
                $('html, body').animate({scrollTop: 280}, 500);
                $('.edit-form-main form :input:visible:enabled:first').focus();
            }
        });
        $('.toggleable').on('click', function () {
            var titleAnchor = $(this).find(".tools > a.box-expand-form");
            if (titleAnchor.hasClass('expand')) {
                titleAnchor.addClass('collapse');
                titleAnchor.removeClass('expand');
            }
            else if (titleAnchor.hasClass('collapse')) {
                titleAnchor.addClass('expand');
                titleAnchor.removeClass('collapse');
            }
            $('.add-form-main > .form').toggleClass('display-hide');
            $('input[name=name]').removeAttr('readonly');
            $(this).parent().find('.portlet-body form :input:visible:enabled:first').focus();
        });

        $('body').on("click", ".btn-collapse", function () {
            $('#selectEmailTemplate').select2('val', '');
            resetForm();

            $(".edit-form-main").hide();
            $(".add-form-main").show();
            $('input[name=name]').removeAttr('readonly');
            $('.add-form-main > .form').addClass('display-hide');
            $('html, body').animate({scrollTop: 0}, 500);
        });
    };

    function resetForm() {
        $("#ajax-response-text").html("");

        $('.add-form-main > .form > form').find("input[type=text], textarea").val("");
        $('#email_to').select2('val', '');
        $('#email_type').select2('val', '');
        //$('#email_text_1').code(''); //clears summernote editor's text

        $('.form-group').removeClass('has-error');
        $('.form-group').removeClass('has-success');

        var visibleFormId = $('form.system-email-form:visible').attr('id');
        if (visibleFormId) {
            var validator = $('#' + visibleFormId).validate();
            validator.resetForm();
        }
    }
    // Method to fetch and place edit form with data using ajax call

    function fetchDataForEdit(thisVal) {
        //var settings_id = $(this).attr("id");
        var actionUrl = 'system-emails/' + thisVal + '/edit';
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data)
            {
                $("#edit_form").html(data.form);
                $('input[name=name]').attr('readonly', 'readonly');
                siteObjJs.validation.formValidateInit('#edit-system-email', handleAjaxRequest);
                if (typeof (tinymce) !== 'undefined') {
                    var length = tinymce.editors.length;
                    for (var i = length; i > 0; i--) {
                        tinymce.editors[i - 1].remove();
                    }
                    ;
                }
                tinymce.init(editor_config);
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
    ;
    // Common method to handle add and edit ajax request and reponse

    var handleAjaxRequest = function () {
        var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
        var formID = formElement.attr("id");
        var messageType = 'success';
        var icon = 'check';
        var formData = formElement.serializeArray();
        var actionUrl = formElement.attr("action");
        var actionType = formElement.attr("method");
        var count = tinymce.editors.length;

        var form = new FormData();
        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });

        if (formID == 'edit-system-email')
        {
            if (count == 2)
            {
                var content = [];
                form.append('text1', tinymce.editors[0].getContent());
                form.append('text2', tinymce.editors[1].getContent());
            }
            else if (count == 4)
            {
                var content = [];
                form.append('text1', tinymce.editors[2].getContent());
                form.append('text2', tinymce.editors[3].getContent());
            }
        }
        else
        {
            var content = [];
            form.append('text1', tinymce.editors['text1'].getContent());
            form.append('text2', tinymce.editors['text2'].getContent());
        }
        form.append('_token', token);
        if ($('input[name=_method]').val()) {
            form.append('_method', $('input[name=_method]').val());
        }

        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: actionType,
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function (data)
                    {
                        //console.log(data);
                        //data: return data from server
                        if (data.status === "error")
                        {
                            icon = "times";
                            messageType = "danger";
                        }

                        //Empty the form fields
                        resetForm();
                        //trigger cancel button click event to collapse form and show title of add page
                        $('.btn-collapse').trigger('click');
                        $('#selectEmailTemplate').select2('val', '');
                        $('#dropDownForm').html(data.form);

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

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-system-email', handleAjaxRequest);
            tinymce.init(editor_config);
        }
    };
}();
