@extends('template_drawer_title')

@section('title','Users')
@section('sub-title','All Users')

@section('content-title')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            <div class="card-header">
                <a type="button" class="btn btn-primary waves-effect waves-light" href="<?=url('')?>/user/view">New User</a>
            </div>
            <hr>

            <div class="card-body">
                <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr>
                            <td><?= $row->user_id ?></td>
                            <td>
                                <?= $row->user_name ?>
                            </td>
                            <td><?= $row->user_fullName ?></td>
                            <td>
                                <?php
                                        switch ($row->user_role) {
                                            case 1:
                                            print 'Administrator';
                                            break;
                                            case 2:
                                            print 'Shipment Operator';
                                            break;
                                            case 3:
                                            print 'Shipment Review';
                                            break;
                                        }
                                ?>
                            </td>
                            <td>
                                <?= ($row->user_status==1?"<span style='color: green;'>Active</span>" : "<span style='color: red;'>Suspended</span>") ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="dropdown-item" href="<?=url('')?>/user/view/<?= $row->user_id ?>">
                                        <i class="feather-16" data-feather="edit"></i>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="deleteUser(<?= $row->user_id ?>)">
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

        <!-- Ag Grid users list section end -->
    </div>
    <!-- users list ends -->

@endsection

@section('scripts')


<!-- DataTables -->
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script src="<?=url('')?>/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>

<!--<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.5/js/responsive.bootstrap.min.js"></script>-->

<script>
    $(function () {
        $('#datatable-grid').DataTable({
            responsive: true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
        });
    });
</script>
<script>

    function deleteUser(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete User',
            text: "Are You Sure You Want To Delete This User?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/user/delete/'+_id,
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
