@extends('template_drawer_title')

@section('title','Clients')
@section('sub-title','All Clients')

@section('content-title')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            <div class="card-header">
                <a type="button" class="btn btn-primary waves-effect waves-light" href="<?=url('')?>/client/view">New Client</a>
            </div>
            <hr>

            <div class="card-body">
                <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>National ID</th>
                        <th>Shipments</th>
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
                            <td><?=$row->client_code?></td>
                            <td><?=$row->client_name?></td>
                            <td>
                                <?=$row->client_followupPhone?>
                                <br>
                                <small><?=$row->client_email?></small>
                            </td>
                            <td><?=$row->client_nationalId?></td>
                            <td><?=$row->shipmentCount?></td>
                            <td>
                                <?php
                                if ($row->client_status == 1)
                                    echo "<span style='color: green;'>Active</span>";
                                else
                                    echo "<span style='color: red;'>Suspended</span>";
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="dropdown-item" href="<?=url('')?>/client/view/<?= $row->client_id ?>">
                                        <i class="feather-16" data-feather="edit"></i>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="deleteClient(<?= $row->client_id ?>)">
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

    function deleteClient(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete Client',
            text: "Are You Sure You Want To Delete This Client?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/client/delete/'+_id,
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
