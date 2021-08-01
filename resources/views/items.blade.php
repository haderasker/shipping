@extends('template_drawer_title')

@section('title','Items')
@section('sub-title','All Items')

@section('content-title')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/tables/datatable/datatables.min.css">

    <!-- users list start -->
    <div class="users-list-wrapper">

        <div class="card">
            <div class="card-header">

                <div class="input-group">
                    <a type="button" class="btn btn-primary waves-effect waves-light mr-1" href="<?=url('')?>/item/view">New Item</a>
                    {{-- <button class="btn btn-outline-success waves-effect waves-light" data-toggle="modal" data-target="#import-dialog">Import</a> --}}
                </div>

            </div>
            <hr>

            <div class="card-body">
                <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Variation</th>
                        <th>SKU</th>
                        <th>Wight</th>
                        <th>Stock</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($data))
                    foreach ($data as $row){
                        ?>
                        <tr>
                            <td><?= $row->item_code ?></td>
                            <td><img src="<?= $row->item_image ?>" style="width: 100px;" ></td>
                            <td style="max-width: 450px; overflow: hidden;"><?= $row->item_name ?></td>
                            <td><?= $row->variation_name ?></td>
                            <td><?= $row->sku ?></td>
                            <td><?= $row->item_wight ?></td>
                            <td><?= $row->item_stock ?></td>
                            <td>
                                <div class="btn-group">
                                    <a class="dropdown-item" href="<?=url('')?>/item/view/<?= $row->item_id ?>">
                                        <i class="feather-16" data-feather="edit"></i>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" style="color:red;" onclick="deleteItem(<?= $row->item_id ?>)">
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



    <div class="modal fade" id="import-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import Items</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="import-form" method="POST" enctype="multipart/form-data">
                        <div class="form-group col-12">
                            <label>Shoopy Manifest</label>
                            <input type="file" name="manifest" class="form-control"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="import-dialog-ok">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
            "autoWidth": false,
            "scrollX": true,
        });
    });

    $('#import-dialog-ok').click(function () {

        var _data = new FormData($('#import-form')[0]);

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "POST",
            url: "<?=url('')?>/item/import",
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
        $('#import-dialog .close').click();
    });
</script>
<script>

    function deleteItem(_id) {
        if (!_id)
            return;

        Swal.fire({
            title: 'Delete Item',
            text: "Are You Sure You Want To Delete This Item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
                    url: '<?=url('')?>/item/delete/'+_id,
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

