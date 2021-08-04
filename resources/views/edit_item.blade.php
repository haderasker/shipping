@extends('template_drawer_title')

@section('title','Items')
@section('sub-title',empty($item->item_id)?"New Item":"Edit Item")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="item-form">
    <input type="hidden" name="item-id" value="<?= empty($item->item_id)?"":$item->item_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <h3 style="margin-bottom: -15px;">General</h3>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <input name="image" type="file" accept="image/*" onchange="loadFile(event)">
                    <img src="<?=empty($item->item_image)?url('').'/img/empty.jpg':$item->item_image?>"  style="width: inherit;" id="output" />
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Item Name</label>
                        <input name="name" type="text" class="form-control" placeholder="Item Name" value="<?= empty($item->item_name)?"":$item->item_name ?>">
                    </div>
                    <div class="form-group">
                        <label>Item Code</label>
                        <input name="code" type="text" class="form-control" placeholder="Item Code" value="<?= empty($item->item_code)?"":$item->item_code ?>">
                    </div>
                    <div class="form-group">
                        <label>Referance</label>
                        <input name="ref" type="text" class="form-control" placeholder="Referance" value="<?= empty($item->item_ref)?"":$item->item_ref ?>">
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input name="sku" type="text" class="form-control" placeholder="SKU" value="<?= empty($item->item_sku)?"":$item->item_sku ?>">
                    </div>
                    <div class="form-group">
                        <label>Wight</label>
                        <input name="wight" type="number" class="form-control" placeholder="Cost" value="<?= empty($item->item_wight)?"":$item->item_wight ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input name="stock" type="number" class="form-control" placeholder="Stock" value="<?= empty($item->item_stock)?"":$item->item_stock ?>">
                    </div>
                </div>

            </div>
            <br><br>

            <h3 style="margin-bottom: -15px;">Variations</h3>
            <hr>

            <button type="button" class="btn btn-outline-primary waves-effect waves-light mb-1" data-toggle="modal" data-target="#variation-dialog">Add New</button>

            <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                <thead>
                <tr>
                    <th>Variation Name</th>
                    <th>Variation SKU</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php
                if (!empty($item->variations))
                foreach ($item->variations as $var){
                    ?>
                    <tr data-variationId="<?= $var->variation_id ?>">
                        <input type="hidden" name="itemVariations[]" value="<?= $var->iv_id.'|'.$var->variation_id.'|'.$var->iv_variationSku ?>"/>
                        <td><?= $var->variation_name ?></td>
                        <td><?= $var->iv_variationSku ?></td>
                        <td><a onclick="removeVariation(this);"><i class="feather icon-trash-2 text-danger" style="font-size:20px;"></i></a></td>
                    </tr>
                    <?php
                }
                ?>

                </tbody>
            </table>


        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveItem();">Submit</button>
        </div>

    </div>
	</form>

 {{-- ================== variation =========================================================== --}}

 <div class="modal fade" id="variation-dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="dialog-course-sessions-name">Add Item Variation</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Variations</label>
                    <select id="variation" class="form-control select2" style="width:100%;">
                        <?php
                        if (!empty($variations))
                        foreach ($variations as $v){
                            echo '<option value="'.$v->variation_id.'">'.$v->variation_name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>SKU</label>
                    <input id="variationSku" type="text" class="form-control" placeholder="Enter Sku">
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="variation-dialog-ok">OK</button>
            </div>
            </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection


@section('scripts')

<script>

$('#variation-dialog-ok').click(function () {

    var variation = $('#variation');
    var sku = $('#variationSku').val();

    if(!variation.val() || !sku){
        alert('Please complete all fields');
        return;
    }

    var existrow = $('#datatable-grid > tbody > tr[data-variationId="' +variation.val()+ '"]');
    if (existrow.length > 0){
        alert('Aleady added!!');
        return;
    }

    // var id = e.attr('data-id');
    var newrow = `<tr data-variationId="${variation.val()}">
                        <input type="hidden" name="itemVariations[]" value="${'|'+variation.val()+'|'+sku}"/>
                        <td>${variation.find('option:selected').text()}</td>
                        <td>${sku}</td>
                        <td><a onclick="removeVariation(this);"><i class="feather icon-trash-2 text-danger" style="font-size:20px;"></i></a></td>
                    </tr>`;

    $('#datatable-grid > tbody:last-child').append(newrow);

    $('#variation-dialog .close').click();
});

function removeVariation(e){
    $(e).parents('tr').remove();
}

function saveItem(){
    var _data = new FormData($('#item-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/item/save",
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
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    };
</script>
@endsection




