@extends('template_drawer_title')

@section('title','Items')
@section('sub-title','All Variations')

@section('content-title')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">

<div class="content-body">
    <!-- cats list start -->
    <div class="cats-list-wrapper">

        <!-- Ag Grid cats list section start -->

        <div class="card">
            <div class="card-header">
                <a type="button" class="btn btn-primary waves-effect waves-light" href="<?=url('')?>/variation/view">New Variation</a>
            </div>
            <hr>

            <div class="card-body">
                <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Variation Name</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr>
                            <td><?= $row->variation_id ?></td>
                            <td>
                                <?= $row->variation_name ?>
                            </td>

                            <td>
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="<?=url('')?>/variation/view/<?= $row->variation_id ?>">Edit Variation</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="deleteVariation(<?= $row->variation_id ?>)">Delete</a>
                                    </div>
                                </div>
                                <!-- /btn-group -->
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                    </tbody>
                </table>
            </div>

        </div>

        <!-- Ag Grid cats list section end -->
    </div>
    <!-- cats list ends -->
</div>

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
            "autoWidth": true,
            "scrollX": true,
        });
    });
</script>
<script>

    function deleteVariation(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete Variation',
            text: "Are You Sure You Want To Delete This Variation?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/variation/delete/'+_id,
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
