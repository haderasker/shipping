@extends('template_drawer_title')

@section('title','Customers')
@section('sub-title','All Customers')

@section('content-title')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            <div class="card-header">
                <a type="button" class="btn btn-primary waves-effect waves-light" href="<?=url('')?>/customer/view">New Customer</a>
            </div>
            <hr>

            <div class="card-body">
                <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Post Code</th>
                        <th>Address</th>
                        <th>Shipments</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr>
                            <td><?= $row->customer_id ?></td>
                            <td><?= $row->customer_name ?></td>
                            <td><?= $row->customer_phone ?></td>
                            <td><?= $row->customer_postcode ?></td>
                            <td style="max-width: 300px; overflow: hidden;">
                                <?= $row->customer_address ?>
                                <?php
                                    if (!empty($row->customer_remark))
                                        echo "<br>".$row->customer_remark;
                                ?>
                                <br>
                                <small><?= $row->customer_city.", ".$row->customer_state.", ".$row->customer_country ?></small>
                            </td>
                            <td><?= $row->shipmentCount ?></td>
                            <td>
                                <div class="btn-group">
                                    <a class="dropdown-item" href="<?=url('')?>/customer/view/<?= $row->customer_id ?>">
                                        <i class="feather-16" data-feather="edit"></i>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="deleteClient(<?= $row->customer_id ?>)">
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
            "scrollX": true,
        });
    });
</script>
<script>

    function deleteCustomer(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete Customer',
            text: "Are You Sure You Want To Delete This Customer?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/customer/delete/'+_id,
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
