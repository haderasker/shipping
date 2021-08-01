@extends('template_drawer_title')

@section('title','Zones')
@section('sub-title',empty($zone->zone_id)?"New Zone":"Edit Zone")


@section('css')
<style>

.card .table tbody tr:last-child td:last-child{
    width: 100px;
}
.btn-group [class*='btn-']:not([class*='btn-outline-']):not([class*='btn-flat-']):not([class*='btn-gradient-']):not([class*='btn-relief-']) {
    border-right-width: 0;
    border-left-width: 0;
}
</style>
@endsection

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="zone-form">
        <input type="hidden" name="zone-id" value="<?= empty($zone->zone_id)?"":$zone->zone_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="row">
                <div class="col-8">
                    <label>Zone Name</label>
                    <input name="name" type="text" class="form-control" placeholder="Zone Name" value="<?= empty($zone->zone_name)?"":$zone->zone_name ?>">
                </div>
                <div class="col-4">
                    <label>Default Fees</label>
                    <input name="fees" type="number" class="form-control" placeholder="Default Fees" value="<?= empty($zone->zone_fees)?"":$zone->zone_fees ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-8">
                    <table id="regions-table" class="table table-striped table-bordered">
                        <thead>
                            {{-- class="thead-dark" --}}
                          <tr>
                            <th>State</th>
                            <th>City</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($regions))
                            foreach ($regions as $region) {
                                echo '<tr>
                                        <td>'.$region->region_state.'</td>
                                        <td>'.$region->region_city.'</td>
                                    </tr>';
                            }
                            ?>
                        </tbody>
                      </table>
                </div>
            </div><br>

            <button type="button" id="add-region" class="btn btn-outline-primary">Add Region</button>

        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveZone();">Submit</button>
        </div>

    </div>
	</form>

@endsection


@section('scripts')
<script src="<?=url('')?>/plugins/bstable.js"></script>

<script>
(function() {

    var editableTable = new BSTable("regions-table",{
        $addButton: $('#add-region'),
        advanced: {
            columnLabel: 'Actions',
            buttonHTML: `<div class="btn-group pull-right">
                <button id="bEdit" type="button" class="btn btn-sm btn-default">
                    <i class="feather-16" data-feather="edit"></i>
                </button>
                <button id="bDel" type="button" class="btn btn-sm btn-default">
                    <i class="feather-16 text-danger" data-feather="trash-2"></i>
                </button>
                <button id="bAcep" type="button" class="btn btn-sm btn-default" style="display:none;">
                    <i class="feather-16" data-feather="check"></i>
                </button>
                <button id="bCanc" type="button" class="btn btn-sm btn-default" style="display:none;">
                    <i class="feather-16" data-feather="x"></i>
                </button>
            </div>`
            },
            onAdd:function() {
                initFeather();
            },

    });

    editableTable.init();

    // editableTable.refresh();

})();

</script>

<script>

function saveZone(){
    var _data = new FormData($('#zone-form')[0]);

    var rows = $('#regions-table tbody>tr');
    rows.each(function() {
        _data.append('regions[]', $(this).find("td:eq(0)").text()+'|'+$(this).find("td:eq(1)").text());
    });

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/zone/save",
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
@endsection
