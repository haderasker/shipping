@extends('template_drawer_title')

@section('title','Driver Custody')
@section('sub-title',$driver->driver_name)

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="client-form">
        <input type="hidden" name="client-id" value="<?= empty($client->client_id)?"":$client->client_id ?>">
    <div class="card">
        {{-- <div class="card-header">
            <a type="button" class="btn btn-primary waves-effect waves-light" href="<?=url('')?>/zone/view">New Custody</a>
        </div>
        <hr> --}}

        <div class="card-body">

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="cash-tab" data-toggle="tab" href="#cash-panel" aria-controls="cash" role="tab" aria-selected="true"><i data-feather="home"></i> Cash</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="items-tab" data-toggle="tab" href="#items-panel" aria-controls="items" role="tab" aria-selected="false"><i data-feather="tool"></i> Items</a>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="cash-panel" aria-labelledby="cash-tab" role="tabpanel">

                    {{-- //////////////////////////////////////////////////////////////////////////////////////////// --}}

                    <table id="datatable-cash" class="table table-bordered table-striped dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Order</th>
                            <th>Zone</th>
                            <th>Total Due</th>
                            <th>Adjust</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        if (!empty($cash))
                        foreach ($cash as $row){
                            ?>
                            <tr data-id="<?=$row->shipment_id?>">
                                <td><?=$row->shipment_id?></td>
                                <td><?=date("d-m-Y", strtotime($row->shipment_created))?></td>
                                <td>
                                    <?=$row->client_name?>
                                    <br>
                                    <small><?=$row->client_followupPhone?></small>
                                </td>
                                <td><a href="<?=url('')?>/shipment/view/<?= $row->shipment_id ?>"><?=$row->shipment_ref?></a></td>
                                <td><?=$row->zone_name?></td>
                                <td><?=$row->totalDeliveredItemValue+$row->shipment_fees-$row->shipment_driverPaid?></td>
                                <td><input type="number" class="adjust-cash" value="0"/></td>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                    </table>
                    <br><br>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="adjustCash();" >Adjust Cash</button>
                    {{-- ///////////////////////////////////////////////////////////////////////////////////////////////////// --}}

                </div>
                <div class="tab-pane" id="items-panel" aria-labelledby="items-tab" role="tabpanel">

                    {{-- //////////////////////////////////////////////////////////////////////////////////////////// --}}

                    <table id="datatable-items" class="table table-bordered table-striped dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Total Due</th>
                            <th>Adjust</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        if (!empty($items))
                        foreach ($items as $row){
                            ?>
                            <tr data-sku="<?=$row->sku?>">
                                <td><?=$row->item_id?></td>
                                <td></td>
                                <td>
                                    <?=$row->fullItemName?>
                                    <br>
                                    <small><?=$row->sku?></small>
                                </td>
                                <td><?=$row->remainingQty?></td>
                                <td><input type="number" class="adjust-qty" value="0"/></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <br><br>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="adjustItems();" >Adjust Items</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" onclick="" >Deficit</button>
                    {{-- ///////////////////////////////////////////////////////////////////////////////////////////////////// --}}

                </div>
            </div>

        </div>

        {{-- <div class="card-footer">
            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="adjustItems();" >Adjust Items</button>
        </div> --}}

    </div>
	</form>

@endsection


@section('scripts')

<script>

function adjustCash() {

    var rows = $('#datatable-cash > tbody > tr');

    if (!rows || rows.length <= 0) return;

    Swal.fire({
        title: 'Adjust Cash',
        text: "You are about to adjust cash, Are you sure?",
        icon: 'warning',
        // input: 'number',
        // inputLabel: 'Enter Withdraw QTY',
        // inputPlaceholder: 'withdraw QTY',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, adjust CASH'
    }).then((result) => {
        if (result.isConfirmed) {

            var _data = new FormData();
            _data.append('driverId',<?=$driver->driver_id?>);
            $.each( rows, function( key, element ) {
                _data.append('shipments[]',$(element).attr('data-id')+'|'+$(element).find('.adjust-cash').val())
            });

            $.ajax({
                headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                url: '<?=url('')?>/custody/adjustCash',
                type: 'POST',
                data: _data,
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success: function (response) {
                    if (response === 'true') {
                        location.reload();
                    }
                    else{
                        toastr.error(response, 'Error');
                    }
                },
            });

        }
    });
}

function adjustItems() {

var rows = $('#datatable-items > tbody > tr');

if (!rows || rows.length <= 0) return;

Swal.fire({
    title: 'Withdraw',
    text: "You are about to adjust items, Are you sure?",
    icon: 'warning',
    // input: 'number',
    // inputLabel: 'Enter Withdraw QTY',
    // inputPlaceholder: 'withdraw QTY',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, adjust ITEMS'
}).then((result) => {
    if (result.isConfirmed) {

        var _data = new FormData();
        _data.append('driverId',<?=$driver->driver_id?>);
        $.each( rows, function( key, element ) {
            _data.append('items[]',$(element).attr('data-sku')+'|'+$(element).find('.adjust-qty').val())
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            url: '<?=url('')?>/custody/adjustItems',
            type: 'POST',
            data: _data,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (response) {
                if (response === 'true') {
                    location.reload();
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
