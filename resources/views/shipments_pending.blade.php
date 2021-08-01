@extends('template_drawer_title')

@section('title','Shipments')
@section('sub-title','Pending Shipments')

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
                            <a class="dropdown-item" href="javascript:void(0);" onclick="followupSelected();">Followup</a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="approveSelected();">Approve</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a>
                        </div>
                    </div>
                </div>

                <div class="col-6 input-group-prepend" style="padding-left: 0 !important;">
                    <select id="shipment-type" class="form-control" style="width: 80%; margin-right: 10px;">
                        <option value="1">New Shipments</option>
                        <option value="2">Followup Shipments</option>
                        <option value="0">All Pending Shipments</option>
                    </select>
                    <button type="button" class="btn btn-outline-primary waves-effect waves-light" onclick="applyFilter();">Apply</button>
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
                            <td>
                                <?=date("d-m-Y", strtotime($row->shipment_created))?>
                                <?php
                                if (!empty($row->shipment_followupDate))
                                echo '<br><i class="feather-16 text-success" data-feather="calendar"></i> '.$row->shipment_followupDate
                                ?>
                            </td>
                            <td>
                                <?=$row->client_name?>
                                <br>
                                <small><?=$row->client_followupPhone?></small>
                            </td>
                            <td><a href="<?=url('')?>/shipment/view/<?= $row->shipment_id ?>"><?=$row->shipment_ref?></a></td>
                            <td>
                                <?php
                                if (empty($row->zone_name)){
                                    echo '<a href="javascript:void(0);" onclick="assignZone('.$row->shipment_id.');">UnDefined</a>';
                                }
                                else{
                                    echo $row->zone_name.' - <small>'.$row->zone_fees.'</small>';
                                }
                                ?>
                            </td>
                            <td>
                                <?=$row->customer_name.' <small>'.$row->customer_phone.'</small>'?>
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


  {{-- ================== zone dialog =========================================================== --}}

  <div class="modal fade" id="zone-dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="dialog-course-sessions-name">Assign Zone</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Select Zone</label>
                        <select id="zone" class="form-control select2">
                            <?php
                                if (!empty($zones))
                                foreach ($zones as $zone) {
                                    echo '<option value="'.$zone->zone_id.'">'.$zone->zone_name.' - '.$zone->zone_fees.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="zone-dialog-ok">OK</button>
            </div>
            </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


@endsection

@section('scripts')
{{-- <script>
$('#shipment-type').on('change', function() {

    // var newURL = window.location.protocol + "//" + window.location.host + "/" + window.location.pathname + window.location.search
    var newURL = window.location.protocol + "//" + window.location.host + "/" + window.location.pathname + '?followup='+this.value;
    Location.href=newURL;
});
</script> --}}

<script>

var _selectedShipment;

$('#zone-dialog-ok').click(function () {
    var _data = new FormData();
    _data.append('shipmentId',_selectedShipment);
    _data.append('zoneId', $('#zone').val());
    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        url: '<?=url('')?>/shipment/assignZone',
        type: 'POST',
        data: _data,
        processData: false,  // tell jQuery not to process the data
        contentType:false,  // tell jQuery not to set contentType
        success: function (response) {
            handleAjaxResponse(response);
        },
    });
    $('#zone-dialog .close').click();
});

function assignZone(_id){
    _selectedShipment = _id;
    $('#zone-dialog').modal('show');
}

function applyFilter(){
    var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname + '?followup='+ $('#shipment-type').val();
    window.location.href = newURL;
}

function followupSelected() {

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

                $('.datatable-default tr').filter(':has(:checkbox:checked)').each(function() {
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
                        handleAjaxResponse(response);
                    },
                });

            }
        });
    }


function approveSelected() {

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

            var _data = new FormData();
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
