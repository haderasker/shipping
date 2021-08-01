@extends('template_drawer_title')

@section('title','Custody Withdrawal')
@section('sub-title','')

@section('css')

{{-- <link rel="stylesheet" type="text/css" href="<?=url('')?>/plugins/flatpickr-4.6.9/flatpickr.min.css"> --}}
<!-- DataTables -->
{{-- <link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css"> --}}
@endsection

@section('content-title')

    <!-- users list start -->
    <div class="users-list-wrapper">

        <div class="card">
            <div class="card-header">

                <div class="col-6 input-group-prepend" style="padding-left: 0 !important;">
                    <select id="driver" class="form-control select2" style="width: 80%; margin-right: 10px;">
                        <?php
                            if (!empty($drivers))
                            foreach ($drivers as $driver) {
                                echo '<option value="'.$driver->driver_id.'">'.$driver->driver_name.' - '.$driver->driver_email.'</option>';
                            }
                        ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary waves-effect waves-light" onclick="applyFilter();">Apply</button>
                </div>

            </div>
            <hr>

            <div class="card-body">

                <div class="row">
                    <div class="col-12">
                        <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Required Items</th>
                                <th>Remaining Items</th>
                                <th>Withdraw Items</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

            <div class="card-footer">
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="withdraw();" >Withdraw</button>
            </div>

        </div>

        <!-- Ag Grid users list section end -->
    </div>
    <!-- users list ends -->


  {{-- ================== dirver dialog =========================================================== --}}

  <div class="modal fade" id="driver-dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="dialog-course-sessions-name">Assign To Driver</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Select Driver</label>
                        <select id="driver" class="form-control select2">
                            <?php
                                if (!empty($drivers))
                                foreach ($drivers as $driver) {
                                    echo '<option value="'.$driver->driver_id.'">'.$driver->driver_name.' - '.$driver->driver_email.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="driver-dialog-ok">OK</button>
            </div>
            </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

{{-- <script src="<?=url('')?>/plugins/flatpickr-4.6.9/flatpickr.min.js"></script> --}}
<!-- DataTables -->
{{-- <script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script> --}}

<!--<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/responsive.bootstrap.min.js"></script>-->

<script>

    $(function(){
        applyFilter();
    })

    function applyFilter(){

        var driverId = $('#driver').val();
        if (!driverId){
            // toastr.warn();
            alert('please select a driver');
            return;
        }

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            url: '<?=url('')?>/custody/getDriverItemTotals/'+driverId,
            type: 'GET',
            // data: _data,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (response) {
                // response = JSON.parse(response);
// console.log(response);
                // if (response.nonePlanned > 0){
                //     $('#none-planned-alert div.alert-body').text(response.nonePlanned + " Scheduled Shipment(s) Remaining ")
                //     $('#none-planned-alert').css({ "visibility": "visible"});
                // }
                // else
                //     $('#none-planned-alert').css({ "visibility": "hidden"});

                $('#datatable-grid tbody').empty();
                response.forEach(element => {
                    $('#datatable-grid tbody').append(createRow(element)) ;
                });

                // handleAjaxResponse(response);
            },
        });

    }

    function createRow(e){

        var r = `<tr data-sku="${e.sku}">
                    <td></td>
                    <td>
                        ${e.fullItemName}
                        <br>
                        ${e.sku}
                    </td>
                    <td>${e.client_name}</td>
                    <td>${e.requiredQty}</td>
                    <td>${e.remainingQty}</td>
                    <td><input type="number" class="withdraw-qty" value="0"/></td>
                </tr>`;

        return r;

    }

    function withdraw() {

        var rows = $('#datatable-grid > tbody > tr');

        if (!rows || rows.length <= 0) return;

        Swal.fire({
            title: 'Withdraw',
            text: "You are about to withdraw, Are you sure?",
            icon: 'warning',
            // input: 'number',
            // inputLabel: 'Enter Withdraw QTY',
            // inputPlaceholder: 'withdraw QTY',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Withdraw!'
        }).then((result) => {
            if (result.isConfirmed) {

                var _data = new FormData();
                _data.append('driverId',$('#driver').val());
                $.each( rows, function( key, element ) {
                    _data.append('items[]',$(element).attr('data-sku')+'|'+$(element).find('.withdraw-qty').val())
                });

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/custody/withdraw',
                    type: 'POST',
                    data: _data,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success: function (response) {
                        if (response === 'true') {
                            applyFilter();
                        }
                        else{
                            toastr.error(response, 'Error');
                        }
                    },
                });

            }
        });
    }

</script>

@endsection
