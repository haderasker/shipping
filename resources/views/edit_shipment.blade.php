@extends('template_drawer_title')

@section('title','Shipments')
@section('sub-title', empty($shipment->shipment_id)?"New Shipment":"Edit Shipment")

@section('css')
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">
<style>
.prepared{
    font-size: 25px;
    text-align: center;
}
</style>
@endsection


@section('content-title')

	<?php
    $role = session('login_info')->user_role;
    $permission = App\Helpers\AppHelper::canEdit('shipment');

    if (!empty($message)) echo $message;

	?>

	<form id="shipment-form">
        <input type="hidden" name="shipment-id" value="<?= empty($shipment->shipment_id)?"":$shipment->shipment_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="row">
                <div class="form-group col-md-3">
                    {{-- <a class="badge badge-pill badge-light-danger">Pending</a> --}}
                    <label>Shipment Status</label>
                    <select id="status" name="status" class="form-control">
                        <?php
                        $status="";
                        if (!empty($shipment->shipment_status))
                            $status=$shipment->shipment_status;
                        ?>
                        <option value="0" <?= ($status == 0 ? "selected" : "") ?> class="text-warning">Pending</option>
                        {{-- <option value="1" <?= ($status == 1 ? "selected" : "") ?> class="text-secondary">Scheduled</option> --}}
                        <option value="1" <?= ($status == 1 ? "selected" : "") ?> class="text-primary">Approved</option>
                        <option value="2" <?= ($status == 2 ? "selected" : "") ?> class="text-secondary">Shipped</option>
                        {{-- <option value="3" class="text-success">Delivered</option>
                        <option value="4" class="text-danger">Returned</option> --}}
                        <option value="3" <?= ($status == 3 ? "selected" : "") ?> class="text-success">Complete</option>
                        <option value="4" <?= ($status == 4 ? "selected" : "") ?> class="text-danger">Cancelled</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <br>
                    <button type="button" class="btn btn-outline-primary waves-effect waves-light mb-1" data-toggle="modal" data-target="#log-dialog">View Log</button>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3">
                    <label>Date</label>
                    <input name="date" type="text" class="form-control" placeholder="Shipment Date" value="<?= empty($shipment->shipment_created)?"":$shipment->shipment_created ?>" disabled>
                </div>
                <div class="form-group col-md-3">
                    <label>Followup Date</label>
                    <input name="fdate" type="text" class="form-control flatpickr" placeholder="Shipment Date" value="<?= empty($shipment->shipment_followupDate)?"":$shipment->shipment_followupDate ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Referance</label>
                    <input name="ref" type="text" class="form-control" placeholder="Enter Shipment Ref" value="<?= empty($shipment->shipment_ref)?"":$shipment->shipment_ref ?>" disabled>
                </div>
                <div class="form-group col-md-3">
                    <label>Tracking No.</label>
                    <input name="tracking" type="text" class="form-control" placeholder="Enter Tracking No." value="<?= empty($shipment->shipment_key)?"":$shipment->shipment_key ?>" disabled>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3">
                    <label>Dimensions</label>
                    <input name="dimensions" type="text" class="form-control" placeholder="Enter Dimensions" value="<?= empty($shipment->shipment_dimensions)?"":$shipment->shipment_dimensions ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Weight</label>
                    <input name="weight" type="text" class="form-control" placeholder="Enter Total Weight" value="<?= empty($shipment->shipment_weight)?"":$shipment->shipment_weight ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Fees</label>
                    <input name="fees" type="text" class="form-control" placeholder="Enter Total Value" value="<?= empty($shipment->shipment_fees)?"":$shipment->shipment_fees ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Payment Method</label>
                    <select name="paymethod" class="form-control">
                        <option value="0">None</option>
                        <?php
                        if (!empty($payMethods))
                        foreach ($payMethods as $method) {
                            echo '<option value="'.$method->method_id.'">'.$method->method_name.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <br><br>


            <h3 style="margin-bottom: -15px;">Client (sender)</h3>
            <hr>

            {{-- <button type="button" <?=($role!=1?'disabled':'')?> class="btn btn-outline-primary waves-effect waves-light mb-1" data-toggle="modal" data-target="#customer-dialog">Add Client</button> --}}

            <div class="row">
                <div class="form-group col-md-6">
                    <label>Name</label>
                    <select name="client" class="form-control select2">
                        <?php
                            if (!empty($clients))
                            foreach ($clients as $client) {
                                // if (!empty($shipment->shipment_clientId))
                                echo '<option value="'.$client->client_id.'">'.$client->client_name.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <br><br>


            <h3 style="margin-bottom: -15px;">Customer (receiver)</h3>
            <hr>

            <button type="button" <?=($role!=1?'disabled':'')?> class="btn btn-outline-primary waves-effect waves-light mb-1" data-toggle="modal" data-target="#customer-dialog">Add Customer</button>

            <div class="row">
                <div class="form-group col-md-6">
                    <label>Name</label>
                    <select name="customer" class="form-control select2">
                        <?php
                            if (!empty($customers))
                            foreach ($customers as $customer) {
                                // if (!empty($shipment->shipment_customerId))
                                echo '<option value="'.$customer->customer_id.'">'.$customer->customer_name.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <br><br>

            <h3 style="margin-bottom: -15px;">Shipping Details</h3>
            <hr>

            <div class="row">
                <div class="form-group col-md-3">
                    <label>Zone</label>
                    <select name="zone" class="form-control select2">
                        <option value="0">Automatic Assigned Zone</option>
                        <?php
                            if (!empty($zones))
                            foreach ($zones as $zone) {
                                if (!empty($shipment->shipment_zoneId) && $zone->zone_id == $shipment->shipment_zoneId)
                                    echo '<option value="'.$zone->zone_id.'" selected>'.$zone->zone_name.' - '.$zone->zone_fees.'</option>';
                                else
                                    echo '<option value="'.$zone->zone_id.'">'.$zone->zone_name.' - '.$zone->zone_fees.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3">
                    <label>Phone</label>
                    <input name="phone" type="text" class="form-control" placeholder="Customer Phone" value="<?= empty($shipment->shipment_customerPhone)?"":$shipment->shipment_customerPhone ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Post Code</label>
                    <input name="postcode" type="text" class="form-control" placeholder="Post Code" value="<?= empty($shipment->shipment_customerPostcode)?"":$shipment->shipment_customerPostcode ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>State</label>
                    <input name="state" type="text" class="form-control" placeholder="State" value="<?= empty($shipment->shipment_customerState)?"":$shipment->shipment_customerState ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>City</label>
                    <input name="city" type="text" class="form-control" placeholder="City" value="<?= empty($shipment->shipment_customerCity)?"":$shipment->shipment_customerCity ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Address</label>
                    <input name="address" type="text" class="form-control" placeholder="Customer Address" value="<?= empty($shipment->shipment_customerAddress)?"":$shipment->shipment_customerAddress ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Remark</label>
                    <input name="remark" type="text" class="form-control" placeholder="Customer Address" value="<?= empty($shipment->shipment_customerRemark)?"":$shipment->shipment_customerRemark ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label>Notes</label>
                    <textarea name="notes" id="notes" class="form-control" style="height:150px;" rows="4"><?= empty($shipment->shipment_notes)?"":$shipment->shipment_notes ?></textarea>
                </div>
            </div>
            <br><br>

            <h3 style="margin-bottom: -15px;">Items</h3>
            <hr>

            <button type="button" <?=($role!=1?'disabled':'')?> class="btn btn-outline-primary waves-effect waves-light mb-1" data-toggle="modal" data-target="#items-dialog">Add Item</button>
            {{-- <button type="button" class="btn btn-outline-success waves-effect waves-light mb-1" data-toggle="modal" data-target="#prepare-dialog">Prepare</button> --}}

            <div style="overflow-x:auto;">

            <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php
                if (!empty($shipment->items))
                foreach ($shipment->items as $row){
                    ?>
                    <tr data-code="<?= $row->item_code ?>" data-sku="<?= $row->sku ?>">
                        <td><img src="<?= $row->item_image ?>" style="width: 100px;" ></td>
                        <td>
                            <?= $row->item_name ?>
                            <br>
                            <small><?= $row->variation_name ?></small>
                            <br>
                            <small><?= $row->shItem_sku ?></small>
                        </td>
                        <td><input type="text" class="item-qty" <?=($role!=1?'disabled':'')?> value="<?= $row->shItem_qty ?>"></td>
                        <td><input type="text" class="item-price" disabled <?=($role!=1?'disabled':'')?> value="<?= $row->shItem_price ?>"></td>
                        <td><?= \App\Helpers\AppHelper::getItemStatusLabel($row->shItem_status) ?></td>
                        <td>
                            <div class="btn-group">
                                <a class="dropdown-item" href="<?=url('')?>/item/view/<?= $row->item_id ?>">
                                    <i class="feather-16" data-feather="edit"></i>
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="removeItem(this)">
                                    <i class="feather-16" data-feather="trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                </tbody>
            </table>

            </div>
        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveShipment();">Submit</button>
        </div>

    </div>
	</form>

    {{-- =========== customer dialog ===================================================================== --}}
    <div class="modal fade" id="customer-dialog" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Customer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group col-12">
                        <label>Customer Name</label>
                        <input id="family-member-dialog_name" type="text" class="form-control"/>
                    </div>

                    <div class="form-group col-12">
                        <label>Phone</label>
                        <input id="family-member-dialog-phone" type="tel" class="form-control"/>
                    </div>

                    <div class="form-group col-12">
                        <label>Zip Code</label>
                        <input id="family-member-dialog_name" type="text" class="form-control"/>
                    </div>

                    <div class="form-group col-12">
                        <label>Address</label>
                        <input id="family-member-dialog_name" type="text" class="form-control"/>
                    </div>

                </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="customer-dialog-ok">OK</button>
                    </div>
                </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    {{-- ================== add item dialog =========================================================== --}}

<div class="modal fade" id="items-dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="dialog-course-sessions-name">Add items</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <table id="items-table-dialog" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Stock</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($items))
		                foreach ($items as $p){
			                ?>
                            <tr>
                                <td><img src="<?= $p->item_image ?>" style="width: 100px;" ></td>
                                <td>
                                    <?= $p->item_name ?>
                                    <br>
                                    <small><?= $p->variation_name ?></small>
                                    <br>
                                    <small><?= $p->sku ?></small>
                                </td>
                                <td><?= $p->item_stock ?></td>
                                <td>
                                    <input class="item-select" type="checkbox"
                                    data-id="<?= $p->item_id ?>"
                                    data-code="<?= $p->item_code ?>"
                                    data-image="<?= $p->item_image ?>"
                                    data-name="<?= $p->item_name ?>"
                                    data-variation="<?= $p->variation_name ?>"
                                    data-sku="<?= $p->sku ?>">
                                </td>
                            </tr>
			                <?php
		                }
		                ?>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="items-dialog-ok">OK</button>
            </div>
            </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


  {{-- ================== log =========================================================== --}}

  <div class="modal fade" id="log-dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="dialog-course-sessions-name">Shipment Log</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <textarea id="txtLog" class="form-control" style="min-height:400px;"></textarea>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@endsection

@section('scripts')
<!-- DataTables -->
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>

<script>

    // function resetCustomerForm() {
    //     // $('#family_children').val(null);
    //     // $('#family-member-dialog_name').val('');
    //     // // $('#family-member-dialog-password').prop( "disabled", !enabled );
    //     // $('#family-member-dialog-phone').val('');
    //     // $('#family-member-dialog-gender').val('');
    //     // $('#family-member-dialog-age').val('');
    //     // $('#family-member-dialog-preftutor').val('');
    //     // $('#family-member-dialog-relation').val('');
    //     return true;
    // }

    function removeItem(e){
        $(e).parents('tr').remove();
    }

    function saveShipment(){

        // var status = $('#status').val();
        var allShipmentItems = $('#datatable-grid > tbody > tr');

        // if (status == 2){
        //     var preparedItems =  $('#datatable-grid > tbody > tr[data-prepared="true"]').length;
        //     if (preparedItems != allShipmentItems.length){
        //         // alert ("You MUST prepare all items!");
        //         // return;
        //     }
        // }

        var _data = new FormData($('#shipment-form')[0]);
        allShipmentItems.each(function(){
            var e = $(this);
            _data.append('items[]', e.attr('data-sku')+'|'+e.find('input.item-price').val()+'|'+e.find('input.item-qty').val());
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "POST",
            url: "<?=url('')?>/shipment/save",
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
    }

</script>

<script>

    $(function () {
        $("select[name='customer']").val(<?=$shipment->shipment_customerId?>).trigger('change');
        $("select[name='client']").val(<?=$shipment->shipment_clientId?>).trigger('change');
    });

    // $('#customer-dialog').on('show.bs.modal', function () {
    //     resetCustomerForm();
    // });


    $('#items-dialog-ok').click(function () {

        $('.item-select:checkbox:checked').each(function(){
            var e = $(this);
            var existrow = $('#datatable-grid > tbody > tr[data-sku="' + e.attr('data-sku')+ '"]');
            console.log (existrow);
            if (existrow.length > 0) return;

            // var id = e.attr('data-id');
            var newrow = `<tr data-code="${e.attr('data-code')}" data-sku="${e.attr('data-sku')}">
                        <td><img src="${e.attr('data-image')}" style="width: 100px;" ></td>
                        <td>
                            ${e.attr('data-name')}
                            <br>
                            ${e.attr('data-variation')}
                            <br>
                            ${e.attr('data-sku')}
                        </td>
                        <td><input type="text" class="item-qty"></td>
                        <td><input type="text" class="item-price"></td>
                        <td><div class="badge badge-pill badge-light-secondary">None</div></td>
                        <td>
                            <div class="btn-group">
                                <a class="dropdown-item" href="<?=url('')?>/item/view/${e.attr('data-id')}">
                                    <i class="feather-16" data-feather="edit"></i>
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="removeItem(this)">
                                    <i class="feather-16" data-feather="trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>`;

            $('#datatable-grid > tbody:last-child').append(newrow);
        });


        $('#items-dialog .close').click();
    });

    // $('#prepare-dialog').on('show.bs.modal', function () {
    //     $('#txtItems').focus();
    // });

    $('#prepare-dialog-ok').click(function () {

        var lines = $('#txtItems').val().split("\n");
        lines.forEach(function(line) {

            if (line.trim() !== ""){
                var row = $('#datatable-grid > tbody > tr[data-code="' + line.trim() + '"]');
                if (row){
                    row.attr('data-prepared',true);
                    row.find('td.prepared').append('<i class="feather icon-check-circle text-success">');
                }
            }

        });

        $('#prepare-dialog .close').click();
    });

    // $('select[name="client"]').on('select2:select', function (e) {
    //     var data = e.params.data;
    //     console.log(data);
    // });

    $('select[name="client"]').on('shown.bs.modal', function (e) {

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            url: '<?=url('')?>/client//'+_id,
            type: 'POST',
            // data: JSON.stringify(rows),
            processData: false,  // tell jQuery not to process the data
            contentType: "application/json",  // tell jQuery not to set contentType
            success: function (response) {

                //=======================================================================
                $('#items-table-dialog').DataTable({
                    "responsive": true,
                    "paging": true,
                    "pageLength": 5,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": false,
                    "autoWidth": true,
                    // "scrollX": true,
                    "columnDefs": [
                        { "searchable": false, "targets": 0 },
                        { "searchable": false, "targets": 2 },
                        { "searchable": false, "targets": 3 },
                    ]
                });
                //=======================================================================





            },
        });

    });

</script>

@endsection
