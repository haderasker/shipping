@extends('template_drawer_title')

@section('title','Shipments')
@section('sub-title','Approved Shipments')

@section('css')

@endsection

@section('content-title')

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            <div class="card-header">

                <div class="input-group col-md-6">

                    <div class="btn-group">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu">
                            {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="scheduleShipments();">Schedule</a> --}}
                            <a class="dropdown-item" href="javascript:void(0);" onclick="approveShipments();">Approve</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a>
                        </div>
                    </div>
                </div>

            </div>
            <hr>

            <div class="card-body">
                <table class="table table-bordered table-striped dt-responsive nowrap datatable-default">
                    <thead>
                    <tr>
                        <th><a href="javascript:void(0);" onclick="setSelected(this);">select</a></th>
                        <th>#</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Order</th>
                        <th>Zone</th>
                        <th>Customer</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr data-id="<?=$row->shipment_id?>">
                            <td><input type="checkbox" class="check-select"></td>
                            <td><?=$row->shipment_id?></td>
                            <td><?=date("d-m-Y", strtotime($row->shipment_created))?></td>
                            <td>
                                <?=$row->client_name?>
                                <br>
                                <small><?=$row->client_followupPhone?></small>
                            </td>
                            <td><a href="<?=url('')?>/shipment/view/<?= $row->shipment_id ?>"><?=$row->shipment_ref?></a></td>
                            <td>
                                <?php
                                if ($row->isPlanned)
                                    echo '<img style="width: 15px;height: 15px;" src="'.url('').'/img/check.png"> ';
                                if (empty($row->zone_name)){
                                    echo "UnDefined";
                                }
                                else{
                                    echo $row->zone_name.' - <small>'.$row->zone_fees.'</small>';
                                }
                                ?>
                            </td>
                            <td>
                                <?=$row->customer_name.' '.$row->customer_phone?>
                                <?php
                                if (!empty($row->shipment_customerState))
                                    echo "<br><small>".$row->shipment_customerState.' '.$row->shipment_customerCity."</small>";
                                if (!empty($row->shipment_customerAddress))
                                    echo "<br><small>".$row->shipment_customerAddress."</small>";
                                if (!empty($row->shipment_customerRemark))
                                    echo "<br><small>".$row->shipment_customerRemark."</small>";
                                ?>
                            </td>
                            <td><?=$row->totalItemValue+$row->shipment_fees?></td>
                        </tr>
                        <?php
                    }
                    ?>

                    </tbody>
                </table>
            </div>

        </div>

        <!-- Ag Grid users list section end -->
    </div>
    <!-- users list ends -->

    <div class="modal fade" id="import-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import Orders</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="import-form" method="POST" enctype="multipart/form-data">
                        <div class="form-group col-12">
                            <label>Shoopy Manifest</label>
                            <input type="file" name="manifest" class="form-control"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="import-dialog-ok">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection

@section('scripts')

<script>

function scheduleShipments() {

        var _html = `<style>.flatpickr-calendar.inline{margin:auto;}</style>
        <input id="schedule-shipment-date" type="text" class="form-control flatpickr-inline flatpickr-input" placeholder="YYYY-MM-DD" readonly="readonly">`;

        Swal.fire({
            title: 'Schedule Shipment(s)',
            // icon: 'warning',
            html:_html,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            onOpen: function () {
                // $('.flatpickr-inline').flatpickr({
                //     inline: true
                // });
                initWidgets();
            }
        }).then((result) => {
            if (result.isConfirmed) {

                var d = $("#schedule-shipment-date").val();
                var _data = new FormData();
                _data.append('date', d);

                $('.datatable-default tr').filter(':has(:checkbox:checked)').each(function() {
                    _data.append('shipments[]', $(this).attr('data-id'));
                });

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/shipment/schedule',
                    type: 'POST',
                    data: _data,
                    processData: false,  // tell jQuery not to process the data
                    contentType:false,  // tell jQuery not to set contentType
                    success: function (response) {
                        handleAjaxResponse(response);
                    },
                });

            }
        });
    }


function approveShipments() {

Swal.fire({
    icon: 'warning',
    title: 'Approve',
    text: "Approve Selected Shipment(s)",
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, Approve'
}).then((result) => {
    if (result.isConfirmed) {

        $('.datatable-default tr').filter(':has(:checkbox:checked)').each(function() {
            _data.append('shipments[]', $(this).attr('data-id'));
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            url: '<?=url('')?>/shipment/approve',
            type: 'POST',
            data: _data,
            processData: false,  // tell jQuery not to process the data
            contentType:false,  // tell jQuery not to set contentType
            success: function (response) {
                handleAjaxResponse(response);
            },
        });

    }
});
}

    function deleteShipment(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete Shipment',
            text: "Are You Sure You Want To Delete This Shipment?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/shipment/delete/'+_id,
                    type: 'POST',
                    // data: JSON.stringify(rows),
                    processData: false,  // tell jQuery not to process the data
                    contentType: "application/json",  // tell jQuery not to set contentType
                    success: function (response) {
                        handleAjaxResponse(response);
                    },
                });

            }
        });
    }



</script>

@endsection
