var User = function () {

    var handleTable = function () {

        var grid = new Datatable();

        grid.init({
            src: $('#users-table'),
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
                "bStateSave": true,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    {data: 'ids', name: 'ids', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'username', name: 'username'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "ajax": {
                    "url": "api/userlist",
                    "type": "GET",
                },
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
            }
        });

        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        })

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            var actionType = action.attr('data-actionType');
            var actionField = action.attr('data-actionField');
            var actionValue = action.val();
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {

                var formdata = {
                    action: 'update',
                    actionField: actionField,
                    actionType: actionType,
                    actionValue: actionValue,
                    ids: grid.getSelectedRows()
                }

                handleGroupAction(grid, formdata);


                //grid.setAjaxParam("customActionType", "group_action");
                //grid.setAjaxParam("customActionName", action.val());
                //grid.setAjaxParam("id", grid.getSelectedRows());

                //grid.getDataTable().ajax.reload();
                //grid.clearAjaxParams();
            } else if (action.val() == "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record (s) selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        var table = grid.getTable();
        var oTable = table.dataTable();

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            bootbox.confirm("Are you sure?", function (result) {
                if (result == false) {
                    return;
                }

                var nRow = $(this).parents('tr')[0];

                var formdata = {
                    action: 'delete',
                    actionField: 'id',
                    actionType: 'group',
                    actionValue: userId,
                    ids: userId
                }
                handleGroupAction(grid, formdata);
            });
        });

        table.on('click', '.view', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            //var redirectUrl = 'user/' + userId;
            //window.location.href = redirectUrl;

            var nTr = $(this).parents('tr')[0];
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }

        });

        table.on('click', '.edit', function (e) {
            e.preventDefault();
            var userId = $(this).attr('data-id');
            var redirectUrl = 'user/' + userId + '/edit';
            window.location.href = redirectUrl;
        });

        /* Formatting function for row details */
        function fnFormatDetails(oTable, nTr) {
            //var aData = oTable.row(nTr).data();
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table>';
            sOut += '<tr><td>Id:</td><td>' + aData.id + '</td></tr>';
            sOut += '<tr><td>Username:</td><td>' + aData.username + '</td></tr>';
            sOut += '<tr><td>Email:</td><td>' + aData.email + '</td></tr>';
            sOut += '<tr><td>Status:</td><td>' + aData.status + '</td></tr>';
            sOut += '<tr><td>Other:</td><td>Could provide a link here</td></tr>';
            sOut += '</table>';

            return sOut;
        }


        function handleGroupAction(grid, data) {

            var token = $('input[name="_token"]').val();
            var form = new FormData();
            form.append("action", data.action);
            form.append("actionType", data.actionType);
            form.append("field", data.actionField);
            form.append("value", data.actionValue);
            form.append("ids", data.ids);
            form.append("_token", token);
            var actionUrl = 'api/user/groupAction';

            jQuery.ajax({
                url: actionUrl,
                cache: false,
                data: form,
                dataType: "json",
                type: "POST",
                processData: false,
                contentType: false,
                success: function (data)
                {
                    grid.getDataTable().ajax.reload();

                    if (data.status == 'success') {
                        Metronic.alert({
                            type: 'success',
                            icon: 'success',
                            message: 'Your action successfully has been completed. Well done!',
                            container: grid.getTableWrapper(),
                            place: 'prepend'
                        });

                    }
                    else if (data.status == 'fail') {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: 'There is some issue to group action please try again!',
                            container: grid.getTableWrapper(),
                            place: 'prepend'
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }
        /*
         
         $('#users-table tbody').on('click', 'tr', function () {
         //$(this).toggleClass('active');
         });
         
         $('#button').click(function () {
         //alert(table.rows('.active').data().length + ' row(s) selected');
         });
         
         $('#users-table').DataTable({
         processing: true,
         serverSide: true,
         ajax: 'api/userlist',
         columns: [
         {data: 'id', name: 'id'},
         {data: 'username', name: 'username'},
         {data: 'email', name: 'email'},
         {data: 'status', name: 'status'},
         {data: 'created_at', name: 'created_at'},
         {data: 'updated_at', name: 'updated_at'},
         {data: 'action', name: 'action', orderable: false, searchable: false}
         ],
         initComplete: function () {
         this.api().columns().every(function () {
         var column = this;
         var input = document.createElement('input');
         $(input).appendTo($(column.footer()).empty())
         .on('change', function () {
         var val = $.fn.dataTable.util.escapeRegex($(this).val());
         column.search(val ? val : '', true, false).draw();
         });
         });
         }
         });
         */
    }

    return {
        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };

}();