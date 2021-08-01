@extends('template_drawer_title')

@section('title','Customers')
@section('sub-title', empty($customer->customer_id)?"New Customer":"Edit Customer")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="customer-form">
        <input type="hidden" name="customer-id" value="<?= empty($customer->customer_id)?"":$customer->customer_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="form-group">
                <label>Name</label>
                <input name="name" type="text" class="form-control" placeholder="Full Name" value="<?= empty($customer->customer_name)?"":$customer->customer_name ?>">
            </div>

            <div class="row">
                <div class="col-6">
                    <label>Phone</label>
                    <input name="phone" type="text" class="form-control" placeholder="Customer Phone" value="<?= empty($customer->customer_phone)?"":$customer->customer_phone ?>">
                </div>
                <div class="col-6">
                    <label>Email</label>
                    <input name="email" type="text" class="form-control" placeholder="customer Email" value="<?= empty($customer->customer_email)?"":$customer->customer_email ?>">
                </div>
            </div><br>

            <div class="form-group">
                <label>Address</label>
                <input name="address" type="text" class="form-control" placeholder="Customer Address" value="<?= empty($customer->customer_address)?"":$customer->customer_address ?>">
            </div>
            <div class="form-group">
                <label>Remary</label>
                <input name="remark" type="text" class="form-control" placeholder="Address Remark" value="<?= empty($customer->customer_remark)?"":$customer->customer_remark ?>">
            </div>

            <div class="row">
                <div class="col-3">
                    <label>Post Code</label>
                    <input name="postcode" type="text" class="form-control" placeholder="Customer Post Code" value="<?= empty($customer->customer_postcode)?"":$customer->customer_postcode ?>">
                </div>
                <div class="col-3">
                    <label>Area</label>
                    <input name="area" type="text" class="form-control" placeholder="Area" value="<?= empty($customer->customer_area)?"":$customer->customer_area ?>">
                </div>
                <div class="col-3">
                    <label>State</label>
                    <input name="state" type="text" class="form-control" placeholder="State" value="<?= empty($customer->customer_state)?"":$customer->customer_state ?>">
                </div>
                <div class="col-3">
                    <label>Country</label>
                    <input name="country" type="text" class="form-control" placeholder="Country" value="<?= empty($customer->customer_country)?"":$customer->customer_country ?>">
                </div>
            </div>

        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveCustomer();">Submit</button>
        </div>

    </div>
	</form>

@endsection

@section('scripts')

<script>

function saveCustomer(){
    var _data = new FormData($('#customer-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/customer/save",
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
