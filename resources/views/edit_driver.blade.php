@extends('template_drawer_title')

@section('title','Drivers')
@section('sub-title',empty($driver->driver_id)?"New Driver":"Edit Driver")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="driver-form">
        <input type="hidden" name="driver-id" value="<?= empty($driver->driver_id)?"":$driver->driver_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="row">
                <div class="col-4">
                    <label>Name</label>
                    <input name="name" type="text" class="form-control" placeholder="Driver Name" value="<?= empty($driver->driver_name)?"":$driver->driver_name ?>">
                </div>
                <div class="col-4">
                    <label>National ID</label>
                    <input name="nationalid" type="text" class="form-control" placeholder="National ID" value="<?= empty($driver->driver_nationalId)?"":$driver->driver_nationalId ?>">
                </div>
                <div class="col-4">
                    <label>Phone</label>
                    <input name="phone" type="text" class="form-control" placeholder="Phone" value="<?= empty($driver->driver_phone)?"":$driver->driver_phone ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-4">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email" value="<?= empty($driver->driver_email)?"":$driver->driver_email ?>">
                </div>
                <div class="col-4">
                    <label>Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Password" value="<?= empty($driver->driver_password)?"":$driver->driver_password ?>">
                </div>
                <div class="col-4">
                    <label>Account Status</label>
                    <select name="status" class="form-control">
                        <?php
                        $estatus="";
                        if (!empty($driver->driver_status))
                            $estatus=$driver->driver_status;
                        ?>
                        <option value="1" <?= ($estatus == 1 ? "selected" : "") ?> style="color: green;">Active</option>
                        <option value="2" <?= ($estatus == 2 ? "selected" : "") ?> style="color: red;">Suspended</option>
                    </select>
                </div>
            </div><br>

        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveDriver();">Submit</button>
        </div>

    </div>
	</form>

@endsection


@section('scripts')

<script>

function saveDriver(){
    var _data = new FormData($('#driver-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/driver/save",
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
