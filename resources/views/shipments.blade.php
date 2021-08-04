@extends('template_drawer_title')

@section('title','Shipments')
@section('sub-title','All Shipments')

@section('css')

@endsection

@section('content-title')

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            <div class="card-header">

                <div class="input-group col-md-6">
                    <a type="button" class="btn btn-primary waves-effect waves-light mr-1" href="<?=url('')?>/shipment/view">New Shipment</a>
                    {{-- <button class="btn btn-outline-success waves-effect waves-light" data-toggle="modal" data-target="#import-dialog">Import</a> --}}
                    <div class="btn-group">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="input-group col-md-2">
                <a type="button" class="btn btn-primary waves-effect waves-light mr-1"  onClick="window.location.reload();" >Refresh</a>
                </div>

                <div class="input-group col-md-4">
                    <input type="text" id="fp-filter" class="form-control flatpickr-range mr-1" placeholder="YYYY-MM-DD to YYYY-MM-DD" />
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
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr>
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
                            <td><?= \App\Helpers\AppHelper::getShipmentStatusLabel($row->shipment_status) ?></td>
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

    const fp = flatpickr(document.querySelector("#fp-filter"), {
        mode: 'range',
        <?php
        if (!empty($_GET['range'])){
            $range = explode('|',$_GET['range']);
            echo 'dateFormat: "Y-m-d",defaultDate: ["'.$range[0].'", "'.$range[1].'"]';
        }
        ?>
    });

    $('#import-dialog-ok').click(function () {

        var _data = new FormData($('#import-form')[0]);

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "POST",
            url: "<?=url('')?>/shipment/import",
            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS
            data: _data, // serializes the form's elements.
            success: function(response)
            {
                handleAjaxResponse(response);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        $('#import-dialog .close').click();
    });
</script>
<script>

    function applyFilter(){
        if (fp.selectedDates.length == 0){
            alert ('Please select a range!');
            return;
        }

        var from = fp.selectedDates[0].toISOString();
        from = from.substring(0, from.indexOf("T"));

        var to = fp.selectedDates[1].toISOString();
        to = to.substring(0, to.indexOf("T"));

        location.href = '<?=url('')?>/shipment?range='+from+'|'+to;
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
