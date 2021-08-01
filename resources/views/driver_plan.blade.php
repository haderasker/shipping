@extends('template_drawer_title')

@section('title','Driver Plan')
@section('sub-title','')

@section('css')

{{-- <link rel="stylesheet" type="text/css" href="<?=url('')?>/plugins/flatpickr-4.6.9/flatpickr.min.css"> --}}
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">
@endsection

@section('content-title')

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            {{-- <div class="card-header">
                <div class="row">
                    <div class="col-6" style="padding-left: 0 !important;">
                        <input type="text" id="fp-filter" class="form-control flatpickr" placeholder="YYYY-MM-DD" />
                    </div>
                    <div id="none-planned-alert" class="alert alert-warning col-5" style="margin-bottom: 0rem !important; visibility: hidden" role="alert">
                        <div class="alert-body"></div>
                    </div>
                </div>

            </div> --}}

            <div class="card-body">

                <div class="row">
                    <div class="col-10">
                        <div id="none-planned-alert" class="alert alert-warning" style="margin-bottom: 0rem !important; visibility: hidden" role="alert">
                            <div class="alert-body"></div>
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-10">
                        <select id="drivers" class="select2 form-control" multiple>
                            <?php
                            if (!empty($drivers))
                                foreach ($drivers as $driver) {
                                    echo '<option value="'.$driver->driver_id.'">'.$driver->driver_name.'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-2">
                        <button type="button" class="btn btn-outline-primary waves-effect waves-light" onclick="applyFilter();">Apply</button>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-12">
                        <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th><a href="javascript:void(0);" onclick="setSelected(this);">select</a></th>
                                <th>#</th>
                                <th>Date</th>
                                <th>Driver</th>
                                <th>Client</th>
                                <th>Order</th>
                                <th>Zone</th>
                                <th>Customer</th>
                                <th>Value</th>
                                <th>Fees</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

            <div class="card-footer">
                <button type="button" class="btn btn-danger waves-effect waves-light" onclick="unassignSelected();">Un-Assign</button>
                <button type="button" class="btn btn-outline-primary waves-effect waves-light" onclick="followupSelected();" >Followup</button>
                <button type="button" class="btn btn-outline-danger waves-effect waves-light" onclick="cancelSelected();" >Cancel</button>
            </div>

        </div>

        <!-- Ag Grid users list section end -->
    </div>
    <!-- users list ends -->

@endsection

@section('scripts')

{{-- <script src="<?=url('')?>/plugins/flatpickr-4.6.9/flatpickr.min.js"></script> --}}
<!-- DataTables -->
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>

<!--<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/responsive.bootstrap.min.js"></script>-->

<script>


    function applyFilter(){
        // var date = $('#fp-filter').val();
        // if (!date){
        //     // toastr.warn();
        //     alert('please select a date');
        //     return;
        // }

        var drivers = $('#drivers>option:selected');
        if (!drivers || drivers.length <= 0){
            // toastr.warn();
            alert('please select at least one driver');
            return;
        }

        var _data = new FormData();
        // _data.append('date', date);

        drivers.each(function() {
            _data.append('drivers[]', $(this).val());
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            url: '<?=url('')?>/plan/getDriversPlan',
            type: 'POST',
            data: _data,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (response) {
                response = JSON.parse(response);

                if (response.nonePlanned > 0){
                    $('#none-planned-alert div.alert-body').text(response.nonePlanned + " Approved Shipment(s) Remaining ")
                    $('#none-planned-alert').css({ "visibility": "visible"});
                }
                else
                    $('#none-planned-alert').css({ "visibility": "hidden"});

                $('#datatable-grid tbody').empty();
                response.data.forEach(element => {
                    $('#datatable-grid tbody').append(createRow(element)) ;
                });

                // handleAjaxResponse(response);
            },
        });

    }

    function unassignSelected(){
        var rows = $('#datatable-grid tr').filter(':has(:checkbox:checked)');
        if (!rows || rows.length <= 0){
            // toastr.warn();
            alert('please select at least one shipment');
            return;
        }

        var _data = new FormData();
        rows.each(function() {
            _data.append('shipments[]', $(this).attr('data-id'));
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "POST",
            url: "<?=url('')?>/plan/unAssignShipments",
            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS
            data: _data, // serializes the form's elements.
            success: function(response)
            {
                applyFilter();
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }

    function createRow(e){

        var total = parseFloat(e.totalItemValue)+parseFloat(e.shipment_fees);
        var planned = (e.isPlanned==1?"alert-success":"");

        var r = `<tr class="${planned}" data-id="${e.shipment_id}">
                    <td><input type="checkbox" class="check-select"></td>
                    <td>${e.shipment_id}</td>
                    <td>${e.shipment_created}</td>
                    <td>${(e.driver_name?e.driver_name:'')}</td>
                    <td>
                        ${e.client_name}
                        <br>
                        <small>${e.client_followupPhone}</small>
                    </td>
                    <td><a href="<?=url('')?>/shipment/view/${e.shipment_id}">${e.shipment_ref}</a></td>
                    <td>${(e.zone_name?e.zone_name:'UnDefined')}</td>
                    <td>
                        ${e.customer_name}
                        <?php
                            //if (!empty($row->customer_address))
                                //  echo "<br><small>".$row->customer_address."</small>";
                        ?>
                        <?php
                        //if (!empty($row->customer_remark))
                            //  echo "<br><small>".$row->customer_remark."</small>";
                        ?>
                    </td>
                    <td>${total}</td>
                    <td>${e.shipment_fees}</td>
                </tr>`;

        return r;

    }

    function followupSelected() {

        var rows = $('#datatable-grid tr').filter(':has(:checkbox:checked)');
        if (!rows || rows.length <= 0){
            alert('please select at least one shipment');
            return;
        }

        var _html = `<style>.flatpickr-calendar.inline{margin:auto;}</style>
        <input id="followup-shipment-date" type="text" class="form-control flatpickr-inline flatpickr-input" placeholder="YYYY-MM-DD" readonly="readonly">`;

        Swal.fire({
            title: 'Schedule Shipment(s)',
            // icon: 'warning',
            html:_html,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            onOpen: function () {
                initWidgets();
            }
        }).then((result) => {
            if (result.isConfirmed) {

                var d = $("#followup-shipment-date").val();

                var _data = new FormData();
                _data.append('date', d);

                rows.each(function() {
                    _data.append('shipments[]', $(this).attr('data-id'));
                });

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/shipment/followup',
                    type: 'POST',
                    data: _data,
                    processData: false,  // tell jQuery not to process the data
                    contentType:false,  // tell jQuery not to set contentType
                    success: function (response) {
                        applyFilter();
                        // handleAjaxResponse(response);
                    },
                });

            }
        });
    }

    function cancelSelected() {
        var rows = $('#datatable-grid tr').filter(':has(:checkbox:checked)');
        if (!rows || rows.length <= 0){
            alert('please select at least one shipment');
            return;
        }

        Swal.fire({
            title: 'Cancel Selected Shipments',
            text: "Are You Sure You Want To Cancel Selected Shipment(s)?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel!'
        }).then((result) => {
            if (result.isConfirmed) {

                //////////////////////////////////////////////////////////////////////////

                var _data = new FormData();
                rows.each(function() {
                    _data.append('shipments[]', $(this).attr('data-id'));
                });

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    type: "POST",
                    url: "<?=url('')?>/shipment/cancel",
                    contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                    processData: false, // NEEDED, DON'T OMIT THIS
                    data: _data, // serializes the form's elements.
                    success: function(response)
                    {
                        applyFilter();
                    },
                    fail: function (jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });

                //////////////////////////////////////////////////////////////////////////

            }
        });
    }

</script>

@endsection
