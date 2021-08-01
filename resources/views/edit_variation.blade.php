@extends('template_drawer_title')

@section('title','Items')
@section('sub-title',empty($variation->variation_id)?"New Variation":"Edit Variation")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="variation-form">
        <input name="variation-id" type="hidden" value="<?= empty($variation->variation_id)?"":$variation->variation_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" class="form-control" placeholder="Variation ID" value="<?= empty($variation->variation_id)?"":$variation->variation_id ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input name="name" type="text" class="form-control" placeholder="Variation Name" value="<?= empty($variation->variation_name)?"":$variation->variation_name ?>">
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveVariation();">Submit</button>
        </div>

    </div>
	</form>

@endsection

@section('scripts')

<script>

function saveVariation(){
    var _data = new FormData($('#variation-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/variation/save",
        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
        processData: false, // NEEDED, DON'T OMIT THIS
        data: _data, // serializes the form's elements.
        success: function(response)
        {
            handleAjaxResponse(response,"<?=url('')?>/variation");
        },
        fail: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

    </script>
@endsection
