siteObjJs.admin.locationsJs = function () {

    // Initialize all the page-specific event listeners here.

    $('body').on("click", ".btn-expand-form", function () {
        $('#gmap_geocoding').show();
        mapGeocoding('add');
    });
    $('.togglelable').click(function (e) {
        $('#gmap_geocoding').show();
        mapGeocoding('add');
    });
    var initializeListener = function () {
        $('#gmap_geocoding').hide();
        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");

            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var validator = $('#' + formId).validate();
            validator.resetForm();
            $("#country_id").select2('val', '');
            $('#state_id').empty();
            $('#state_id').append($('<option>', {
                value: '',
                text: siteObjJs.admin.locationsJs.selectState,
            }));
            $('#state_id').val("");
            $("#state_id").select2('val', '');

            $('#city_id').empty();
            $('#city_id').append($('<option>', {
                value: '',
                text: siteObjJs.admin.locationsJs.selectCity,
            }));
            $('#city_id').val("");
            $("#city_id").select2('val', '');

            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        $('#create-locations').on('change', '.country_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchStateList(this);
            }
            else
            {
                $('#state_id').empty();
                $('#state_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectState,
                }));
                $('#state_id').val("");
            }
        });

        $('#create-locations').on('change', '.state_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchCityList(this);
            }
            else
            {
                $('#city_id').empty();
                $('#city_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectCity,
                }));
                $('#city_id').val("");
            }
        });

        $('#edit_form').on('change', '.country_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchStateList(this);
            }
            else
            {
                $('#state_id').empty();
                $('#state_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectState,
                }));
                $('#state_id').val("");
            }
        });

        $('#edit_form').on('change', '.state_id', function (e) {
            if ($(this).val() != 0)
            {
                fetchCityList(this);
            }
            else
            {
                $('#city_id').empty();
                $('#city_id').append($('<option>', {
                    value: '',
                    text: siteObjJs.admin.locationsJs.selectState,
                }));
                $('#city_id').val("");
            }
        });

        $('#LocationsList').on('change', '#country-drop-down-search', function (e) {
            fetchStateList(this, 'search');
        });

        $('#LocationsList').on('change', '#state_id', function (e) {
            fetchCityList(this, 'search');
        });

        $('#LocationsList').on('click', '.filter-cancel', function (e) {
            $("#country-drop-down-search").select2("val", "");
            $('#LocationsList #state_id').html("<option value='' selected>Select State</option>");
            $('#LocationsList #city_id').html("<option value='' selected>Select City</option>");
            $("#status-drop-down-search").val('');
        });

    };


    var fetchStateList = function (elet, content) {

        content = content || '';
        var currentForm = $(elet).closest("form");
        var countryID = $(elet).val();

        var actionUrl = 'locations/stateData/' + countryID;

        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                if (content === 'search') {
                    htm = $($.parseHTML(data.list)).filter('div#state-listing-content');
                    $('#LocationsList').find("#state-drop-down-search").html(htm.html());
                } else {
                    $(currentForm).find("#state-drop-down").html(data.list);
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

        var actionUrl = 'locations/cityData/' + stateID;
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "GET",
            processData: false,
            contentType: false,
            success: function (data)
            {
                if (content === 'search') {
                    htm = $($.parseHTML(data.list)).filter('div#city-listing-content');
                    $('#LocationsList').find("#city-drop-down-search").html(htm.html());
                } else {
                    $(currentForm).find("#city-drop-down").html(data.list);
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
            var locations_id = $(this).attr("id");
            var actionUrl = 'locations/' + locations_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data)
                {
                    $("#edit_form").html(data.form);
                    $('#gmap_geocoding').show();
                    mapGeocoding('edit');
                    siteObjJs.validation.formValidateInit('#edit-locations', handleAjaxRequest);
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
                        $("#country_id").select2('val', '');
                        $("#states_id").select2('val', '');
                        $("#city_id").select2('val', '');

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
                        $('#gmap_geocoding').hide();
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
                }
        );
    }

    var handleTable = function () {

        grid = new Datatable();
        grid.init({
            src: $('#LocationsList'),
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
                    {data: 'country.name', name: 'country.name'},
                    {data: 'states.name', name: 'state.name'},
                    {data: 'city.name', name: 'city.name'},
                    {data: 'location', name: 'location'},
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
                    api.column(6, {page: 'current'}).data().each(function (group, i) {
                        if (group != 0) {
                            group = '<span class="label label-sm label-success">Active</span>';
                        } else {
                            group = '<span class="label label-sm label-danger">Inactive</span>';
                        }
                        $(rows).eq(i).children('td:nth-child(6)').html(group);
                    });
                },
                "ajax": {
                    "url": "locations/data",
                    "type": "GET"
                }
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });
        $('#search_country').on('change', function () {
            grid.getDataTable().column($(this).attr('column-index')).search($(this).val()).draw();
        });
    };
    var markers = [];
    var mapGeocoding = function (type) {
        var lati, longi;

        if (type == 'add') {
            lati = '18.5203';
            longi = '73.8567';
        } else {
            lati = $('#edit-locations').find('input[id="latitude"]').val();
            longi = $('#edit-locations').find('input[name="longitude"]').val();
        }

        var map = new GMaps({
            div: '#gmap_geocoding',
            lat: lati,
            lng: longi,
            height: '300px',
            width: '69%',
        });

        var handleAction = function (handle_type) {
            //var text = $.trim($('#location').val());
            if (type == 'add') {
                var text = $('#create-locations').find('input[name="location"]').val();
            } else {
                var text = $('#edit-locations').find('input[name="location"]').val();
            }
            var infoAddress = text;//text + ', ' + $("#city_id option:selected").text() + ',<br /> ' + $("#state_id option:selected").text() + ', ' + $("#country_id option:selected").text();
            GMaps.geocode({
                address: text,
                callback: function (results, status) {
                    if (status == 'OK') {
                        var latlng = results[0].geometry.location;
                        map.setCenter(latlng.lat(), latlng.lng());
                        if (type == 'add') {
                            $('#create-locations').find('input[id="latitude"]').val(latlng.lat());
                            $('#create-locations').find('input[id="longitude"]').val(latlng.lng());
                        } else {
                            if (handle_type == 2) {
                                $('#edit-locations').find('input[id="latitude"]').val(lati);
                                $('#edit-locations').find('input[name="longitude"]').val(longi);
                            } else if (handle_type == 3) {
                                $('#edit-locations').find('input[id="latitude"]').val(latlng.lat());
                                $('#edit-locations').find('input[name="longitude"]').val(latlng.lng());
                            }
                        }
                        DeleteMarkers();
                        var marker = map.addMarker({
                            lat: latlng.lat(),
                            lng: latlng.lng(),
                            draggable: true,
                            //title: 'Marker with InfoWindow',
//                            infoWindow: {
//                                content: '<span style="color:#000; text-transform:capitalize;">'+infoAddress+'</span>'
//                            }
                        });

                        marker.addListener('dragend', function (event) {
                            if (type == 'add') {
                                $('#create-locations').find('input[id="latitude"]').val(event.latLng.lat());
                                $('#create-locations').find('input[id="longitude"]').val(event.latLng.lng());
                            } else {
                                $('#edit-locations').find('input[id="latitude"]').val(event.latLng.lat());
                                $('#edit-locations').find('input[name="longitude"]').val(event.latLng.lng());
                            }
                            //GetAddress(type);
                        });

                        //Add marker to the array.
                        markers.push(marker);
                    }
                }
            });

        }
        if (type == 'edit') {
            handleAction(2);
        }
        $($('#edit-locations').find('input[id="location"]')).blur(function (e) {
            e.preventDefault();
            handleAction(3);
        });
        $($('#create-locations').find('input[id="location"]')).blur(function (e) {
            e.preventDefault();
            handleAction(1);
        });
    };

    function GetAddress(type) {
        var latitude, longitude;
        if (type == 'add') {
            latitude = $('#create-locations').find('input[id="latitude"]').val();
            longitude = $('#create-locations').find('input[id="longitude"]').val();
        } else {
            latitude = $('#edit-locations').find('input[id="latitude"]').val();
            longitude = $('#edit-locations').find('input[name="longitude"]').val();
        }
        var lat = parseFloat(latitude);
        var lng = parseFloat(longitude);

        var latlng = new google.maps.LatLng(lat, lng);
        var geocoder = geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    console.log("Location: " + results[1].formatted_address);
                }
            }
        });
    }


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
            siteObjJs.validation.formValidateInit('#create-locations', handleAjaxRequest);
        }
    };
}();
