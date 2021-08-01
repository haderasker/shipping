@extends('template_drawer_title')

@section('title','Users')
@section('sub-title',empty($user->user_id)?"New User":"Edit User")

@section('content-title')

    <?php if (!empty($message)) echo $message; ?>

	<form id="user-form">
        <input type="hidden" name="user-id" value="<?= empty($user->user_id)?"":$user->user_id ?>">
    <div class="card">

        <div class="card-body">
            <div class="result"></div>

            <div class="row">
                <div class="col-12">
                    <label>Full Name</label>
                    <input name="full_name" type="text" class="form-control" placeholder="Full Name" value="<?= empty($user->user_fullName)?"":$user->user_fullName ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-6">
                    <label>User Name</label>
                    <input name="name" type="text" class="form-control" placeholder="User NAme" value="<?= empty($user->user_name)?"":$user->user_name ?>">
                </div>
                <div class="col-6">
                    <label>Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Password" value="<?= empty($user->user_password)?"":$user->user_password ?>">
                </div>
            </div><br>

            <div class="row">
                <div class="col-6">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        <?php
                        $role="";
                        if (!empty($user->user_role))
                            $role=$user->user_role;
                        ?>
                        <option value="1" <?= ($role == 1 ? "selected" : "") ?>>Administrator</option>
                        <option value="2" <?= ($role == 2 ? "selected" : "") ?>>Shipment Operator</option>
                        <option value="3" <?= ($role == 3 ? "selected" : "") ?>>Shipment Review</option>
                    </select>
                </div>
                <div class="col-6">
                    <label>Account Status</label>
                    <select name="status" class="form-control">
                        <?php
                        $estatus="";
                        if (!empty($user->user_status))
                            $estatus=$user->user_status;
                        ?>
                        <option value="1" <?= ($estatus == 1 ? "selected" : "") ?> style="color: green;">Active</option>
                        <option value="2" <?= ($estatus == 2 ? "selected" : "") ?> style="color: red;">Suspended</option>
                    </select>
                </div>
            </div><br>

        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info" onclick="saveUser();">Submit</button>
        </div>

    </div>
	</form>

@endsection


@section('scripts')

<script>

function saveUser(){
    var _data = new FormData($('#user-form')[0]);

    $.ajax({
        headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
        type: "POST",
        url: "<?=url('')?>/user/save",
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
