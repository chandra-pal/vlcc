siteObjJs.admin.centersJs = function () {

// Initialize all the page-specific event listeners here.

    var mapGeoCall = function () {
        if ($('#create-center').parents('.add-form-main').css('display') == 'block') {
            mapGeocoding('add');
        } else if ($('#edit-center').css('display') == 'block') {
            mapGeocoding('edit');
        }
    };
    var initializeListener = function () {
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#country_id").select2('val', '');
            $("#state_id").select2('val', '');
            $("#city_id").select2('val', '');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        $('body').on("click", ".btn-expand-form", function () {
            mapGeocoding('add');
        });

        $('.togglelable').bind("click", function () {
            setTimeout(mapGeoCall, 2500)
        });

        $('body').on('change', '.country_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchStateList(this);
            } else
            {
                $('#state_id').empty();
                $('#state_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectState,
                }));
                $('#state_id').val("");
            }
        });


        $('body').on('change', '.state_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchCityList(this);
            } else
            {
                $('#city_id').empty();
                $('#city_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectState,
                }));
                $('#city_id').val("");
            }
        });

        $('#center-table').on('change', '#country-drop-down-search', function (e) {
            fetchStateList(this, 'search');
        });

        $('#center-table').on('change', '#state-drop-down-search', function (e) {
            fetchCityList(this, 'search');
        });

        $('#center-table').on('click', '.filter-cancel', function (e) {
            $("#country-drop-down-search").select2("val", "");
            $('#state-drop-down-search').select2("val", "");
            $('#city-drop-down-search').select2("val", "");
            $("#status-drop-down-search").val('');
        });

    };


    var fetchStateList = function (elet, content) {

        content = content || '';
        var currentForm = $(elet).closest("form");
        var countryID = $(elet).val();

        var actionUrl = 'center/stateData/' + countryID;

        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                if (content === 'search') {
                    $('#center-table').find("#state-drop-down-search").html(data.list);

                    $('#state-drop-down-search').select2({
                        placeholder: 'Select an option'
                    });
                } else {
                    $(currentForm).find("#state-drop-down").html(data.list);
                    $('#state_id').select2({
                        placeholder: 'Select an option'
                    });
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
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });
    };

    var fetchCityList = function (elet, content) {
        content = content || '';
        var currentForm = $(elet).closest("form");
        var stateID = $(elet).val();

        var actionUrl = 'center/cityData/' + stateID;
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                if (content === 'search') {
                    $('#center-table').find("#city-drop-down-search").html(data.list);
                    $('#city-drop-down-search').select2({
                        placeholder: 'Select an option'
                    });
                } else {
                    $(currentForm).find("#city-drop-down").html(data.list);
                    $('#city_id').select2({
                        placeholder: 'Select an option'
                    });
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
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });
    };
    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var center_id = $(this).attr("data-id");
            var actionUrl = 'center/' + center_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    siteObjJs.validation.formValidateInit('#edit-center', handleAjaxRequest);
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
        var actionType = formElement.attr("method");
        var formData = formElement.serialize();
        var icon = "check";
        var messageType = "success";
        $.ajax(
                {
                    url: actionUrl,
                    cache: false,
                    type: actionType,
                    data: formData,
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
            src: $('#center-table'),
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
                    {
                        data: 'center_name',
                        name: 'center_name',
                        render: function (data, type, row) {
                            if (row.center_name !== null) {
                                return row.center_name;
                            } else {
                               // return '-';
                               //console.log('bhawna');
                                return row.area;
                            }
                        }

                    },
                    {data: 'address', name: 'address'},
                    {data: 'area', name: 'area'},
                    //{data: 'city.name', name: 'city.name'},
                    {
                        data: 'country',
                        name: 'country',
                        //visible: false,
                        render: function (data, type, row) {
                            var str = '';
                            if (!$.isEmptyObject(row.country) || !$.isEmptyObject(row.states) || !$.isEmptyObject(row.city)) {
                                str += (row.city['name'] ? row.city['name'] : '') + ' , ';
                                str += (row.states['name'] ? row.states['name'] : '') + ' (';
                                str += (row.country['name'] ? row.country['name'] : '') + ')';

                            } else {
                                str += '-'
                            }
                            return str;

                        }

                    },
                    //{data: 'states.name', name: 'states.name'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'status', name: 'status'},
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
                    "url": "center/data",
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


    /**
     * plot google map
     */
    var mapGeocoding = function (formType) {
        handleAction(formType, 0);

        $($('body').find('.city_id')).change(function (e) {
            e.preventDefault();
            handleAction(formType, 1);
        });

        //blur on add form
        $($('body').find('input[name="area"]')).blur(function (e) {
            e.preventDefault();
            handleAction(formType, 1);
        });

        //blur on add form
        $($('body').find('input[name="pincode"]')).blur(function (e) {
            e.preventDefault();
            handleAction(formType, 1);
        });
    };

    var markers = [];
    var handleAction = function (formType, onblur) {
        var map_div_id;
        if (formType === 'add') {

            $('#create-center #add-map-container').html('');
            $('#create-center #add-map-container').append('<div id="gmap_geocoding_add" class="gmaps"></div>');
            map_div_id = "gmap_geocoding_add";
        } else if (formType === 'edit') {

            $('#edit-center #add-map-container').html('');
            $('#edit-center #add-map-container').append('<div id="gmap_geocoding_edit" class="gmaps"></div>');
            map_div_id = "gmap_geocoding_edit";
        }
        var infoAddressText, latitude, longitude, streetNameText, zipCodeText, apartmentCityText;
        //check if the attraction name is not set, Then use default latitude and longitude
        if (onblur === 1) {
            if (formType === 'add') {

                apartmentCityText = $('#create-center').find('#city_id option:selected').text();
                streetNameText = $('#create-center').find('input[name="area"]').val();
                zipCodeText = $('#create-center').find('input[name="pincode"]').val();
            } else if (formType === 'edit') {
                apartmentCityText = $('#edit-center').find('#city_id option:selected').text();
                streetNameText = $('#edit-center').find('input[name="area"]').val();
                zipCodeText = $('#edit-center').find('input[name="pincode"]').val();
            }
            apartmentCityText = (apartmentCityText === 'Select City') ? '' : apartmentCityText;

            infoAddressText = streetNameText + ' ' + zipCodeText;
            console.log(infoAddressText);
        } else {
            if (formType === 'add') {
                latitude = c_latitude;
                longitude = c_longitude;
            } else if (formType === 'edit') {
                latitude = $('#edit-center').find('input[name="latitude"]').val();
                longitude = $('#edit-center').find('input[name="longitude"]').val();
            }

            infoAddressText = latitude + ',' + longitude;
        }
        var map, service, infowindow, bounds;

        //function initialize() {
        map = new google.maps.Map(
                document.getElementById(map_div_id), {
            center: new google.maps.LatLng(c_latitude, c_longitude),
            zoom: 19,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var request = {
            query: infoAddressText
        }

        infowindow = new google.maps.InfoWindow();
        service = new google.maps.places.PlacesService(map);
        bounds = new google.maps.LatLngBounds();
        service.textSearch(request, callback);

        function callback(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {

                var place = results[0];
                var position = place.geometry.location;
                var marker = createMarker(position);
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();

                if (formType === 'add') {
                    $('#create-center').find('input[name="latitude"]').val(latitude);
                    $('#create-center').find('input[name="longitude"]').val(longitude);
                } else if (formType === 'edit') {
                    $('#edit-center').find('input[name="latitude"]').val(latitude);
                    $('#edit-center').find('input[name="longitude"]').val(longitude);
                }
                bounds.extend(marker.getPosition());
                map.fitBounds(bounds);
                var listener = google.maps.event.addListener(map, "idle", function () {
                    if (map.getZoom() > 19)
                        map.setZoom(19);
                    google.maps.event.removeListener(listener);
                });
            } else {
                var latitude = c_latitude;
                var longitude = c_longitude;
                var position = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));
                var marker = createMarker(position);

                if (formType === 'add') {
                    $('#create-center').find('input[name="latitude"]').val(latitude);
                    $('#create-center').find('input[name="longitude"]').val(longitude);
                } else if (formType === 'edit') {
                    $('#edit-center').find('input[name="latitude"]').val(latitude);
                    $('#edit-center').find('input[name="longitude"]').val(longitude);
                }
                bounds.extend(marker.getPosition());
                map.fitBounds(bounds);
                var listener = google.maps.event.addListener(map, "idle", function () {
                    if (map.getZoom() > 19)
                        map.setZoom(19);
                    google.maps.event.removeListener(listener);
                });
            }
        }
        function createMarker(position) {
            DeleteMarkers();
            var marker = new google.maps.Marker({
                map: map,
                position: position,
                lat: position.lat(),
                lng: position.lng(),
                draggable: true
            });


            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(place.name);
                infowindow.open(map, this);
            });

            marker.addListener('dragend', function (event) {
                $('input[name="latitude"]').val(event.latLng.lat());
                $('input[name="longitude"]').val(event.latLng.lng());
            });

            return marker;
        }
    };

    function DeleteMarkers() {
        //Loop through all the markers and remove
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
    }
    ;
    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
            handleTable();
            fetchDataForEdit();
            mapGeocoding('add');
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-center', handleAjaxRequest);
        }

    };
}();