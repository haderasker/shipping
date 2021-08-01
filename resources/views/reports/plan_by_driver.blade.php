@extends('template_drawer_title')

@section('title','Reports')
@section('sub-title','Plan By Driver')

@section('css')

@endsection

@section('content-title')

    <!-- users list start -->
    <div class="users-list-wrapper">

        <!-- Ag Grid users list section start -->

        <div class="card">
            {{-- <div class="card-header">
                <div class="row">
                    <div class="col-6" style="padding-left: 0 !important;">
                        <input type="text" id="fp-filter" class="form-control flatpickr" placeholder="YYYY-MM-DD" />
                    </div>
                    <div id="none-planned-alert" class="alert alert-warning col-5" style="margin-bottom: 0rem !important; visibility: hidden" role="alert">
                        <div class="alert-body"></div>
                    </div>
                </div>

            </div> --}}

            <div class="card-body">

                <div class="row">
                    <div class="col-10">
                        <select id="drivers" class="select2 form-control">
                            <option value="0">None</option>
                            <?php
                            if (!empty($drivers)){
                                $did = last(request()->segments());
                                foreach ($drivers as $d) {
                                    echo '<option value="'.$d->driver_id.'" '.($d->driver_id==$did?"selected":"").'>'.$d->driver_name.' - '.$d->driver_email.'</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>

                    <div class="col-2">
                        <button type="button" class="btn btn-outline-primary waves-effect waves-light" onclick="applyFilter();">Apply</button>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-12">
                        <table id="datatable-grid" class="table table-bordered table-striped dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Zone</th>
                                <th>Client</th>
                                <th>Customer</th>
                                <th>Zone Fees</th>
                                <th>Total Value</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($data))
                            foreach ($data as $row) {
                                ?>
                                <tr data-id="<?= $row->shipment_id ?>">
                                    <td><?= $row->shipment_ref ?></td>
                                    <td><?= $row->shipment_created ?></td>
                                    <td><?=empty($row->zone_name)?"UnDefined":$row->zone_name ?></td>
                                    <td>
                                        <?= $row->client_name ?>
                                        <br>
                                        <small><?= $row->client_followupPhone ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        echo $row->customer_name.' '.$row->customer_phone;
                                        if (!empty($row->customer_state))
                                            echo "<br><small>".$row->customer_state.' '.$row->customer_city."</small>";
                                        if (!empty($row->customer_address))
                                            echo "<br><small>".$row->customer_address."</small>";
                                        if (!empty($row->customer_remark))
                                            echo "<br><small>".$row->customer_remark."</small>";
                                        ?>
                                    </td>
                                    <td><?= $row->zone_fees ?></td>
                                    <td><?= $row->totalItemValue+$row->shipment_fees ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

            <div class="card-footer">
                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#driver-dialog" >Assign To Driver</button>
            </div>

        </div>

        <!-- Ag Grid users list section end -->
    </div>
    <!-- users list ends -->

@endsection

@section('scripts')

<script>

    function applyFilter(){

        var driver = $('#drivers>option:selected').val();
        if (!driver || driver.length <= 0){
            // toastr.warn();
            alert('please select a driver');
            return;
        }

        location.href = "<?=url('')?>/report/planbydriver/" + driver;

    }


</script>

@endsection
