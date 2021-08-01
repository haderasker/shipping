@extends('template_drawer_title')

@section('title','Clients')
@section('sub-title',empty($client->client_id)?"New Client":"Edit Client")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="client-form">
        <input type="hidden" name="client-id" value="<?= empty($client->client_id)?"":$client->client_id ?>">
    <div class="card">

        <div class="card-body card-block">
            <div class="result"></div>

            <div class="row">
                <div class="col-4">
                    <label>Name</label>
                    <input name="name" type="text" class="form-control" placeholder="Client Name" value="<?= empty($client->client_name)?"":$client->client_name ?>">
                </div>
                <div class="col-4">
                    <label>Code</label>
                    <input name="code" type="text" class="form-control" placeholder="Client Code" disabled value="<?= empty($client->client_code)?"":$client->client_code ?>">
                </div>
                <div class="col-4">
                    <label>National ID</label>
                    <input name="nationalid" type="text" class="form-control" placeholder="National ID" value="<?= empty($client->client_nationalId)?"":$client->client_nationalId ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-6">
                    <label>Followup Name</label>
                    <input name="fname" type="text" class="form-control" placeholder="Followup Name" value="<?= empty($client->client_followupName)?"":$client->client_followupName ?>">
                </div>
                <div class="col-6">
                    <label>Followup Phone</label>
                    <input name="fphone" type="text" class="form-control" placeholder="Followup Phone" value="<?= empty($client->client_followupPhone)?"":$client->client_followupPhone ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-4">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email" value="<?= empty($client->client_email)?"":$client->client_email ?>">
                </div>
                <div class="col-4">
                    <label>Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Password" value="<?= empty($client->client_password)?"":$client->client_password ?>">
                </div>
                <div class="col-4">
                    <label>Account Status</label>
                    <select name="status" class="form-control">
                        <?php
                        $estatus="";
                        if (!empty($client->client_status))
                            $estatus=$client->client_status;
                        ?>
                        <option value="1" <?= ($estatus == 1 ? "selected" : "") ?> style="color: green;">Active</option>
                        <option value="2" <?= ($estatus == 2 ? "selected" : "") ?> style="color: red;">Suspended</option>
                    </select>
                </div>
            </div><br><br>

            <h3 style="margin-bottom: -15px;">SMS Misr Integration</h3>
            <hr>
            <div class="row">
                <div class="col-4">
                    <label>SMS User</label>
                    <input name="smsuser" type="text" class="form-control" placeholder="SMS User" value="<?= empty($client->client_smsUser)?"":$client->client_smsUser ?>">
                </div>
                <div class="col-4">
                    <label>SMS Password</label>
                    <input name="smspassword" type="text" class="form-control" placeholder="SMS Password" value="<?= empty($client->client_smsPassword)?"":$client->client_smsPassword ?>">
                </div>
                <div class="col-4">
                    <label>SMS Sender ID</label>
                    <input name="smssender" type="text" class="form-control" placeholder="SMS Sender" value="<?= empty($client->client_smsSenderId)?"":$client->client_smsSenderId ?>">
                </div>
            </div><br><br>

            <?php
            if (!empty($client->client_id)){
            ?>
            <h3 style="margin-bottom: -15px;">WooCommerce Integration</h3>
            <hr>
            <strong>Order Create WebHook:</strong>
            <p><?=url('')?>/api/woocommerce/order_create?client=<?=$client->client_code?></p>

            <strong>Order Update WebHook:</strong>
            <p><?=url('')?>/api/woocommerce/order_update?client=<?=$client->client_code?></p>

            <strong>Order Delete WebHook:</strong>
            <p><?=url('')?>/api/woocommerce/order_delete?client=<?=$client->client_code?></p>
            <?php
            }
            ?>
        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveClient();">Submit</button>
        </div>

    </div>
	</form>

@endsection


@section('scripts')

<script>

function saveClient(){
    var _data = new FormData($('#client-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/client/save",
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
