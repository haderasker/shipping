@extends('template_drawer_title')

@section('title','Dashboard')

@section('content-title')

@section('css')
<link rel="stylesheet" type="text/css" href="<?=url('')?>/app-assets/vendors/css/charts/apexcharts.css">
<!--<link rel="stylesheet" type="text/css" href="--><?//= base_url(); ?><!--/app-assets/css/pages/dashboard-ecommerce.css">-->
<!--<link rel="stylesheet" type="text/css" href="--><?//= base_url(); ?><!--/app-assets/css/pages/card-analytics.css">-->
@endsection

    <!-- Statistics card section start -->
    <section id="statistics-card">

        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0"><?=0?></h2>
                            <p>test</p>
                        </div>
                        <div class="avatar bg-rgba-primary p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-cpu text-primary font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0"><?=0?></h2>
                            <p>test</p>
                        </div>
                        <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-server text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0"><?=0?></h2>
                            <p>test</p>
                        </div>
                        <div class="avatar bg-rgba-danger p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-activity text-danger font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0"><?=0?></h2>
                            <p>test</p>
                        </div>
                        <div class="avatar bg-rgba-warning p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-alert-octagon text-warning font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-eye text-info font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-message-square text-warning font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-shopping-bag text-danger font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-heart text-primary font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-award text-success font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                <div class="avatar-content">
                                    <i class="feather icon-truck text-danger font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700"><?=0?></h2>
                            <p class="mb-0 line-ellipsis">test</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4>test</h4>
<!--                        <div class="dropdown chart-dropdown">-->
<!--                            <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--                                Last 7 Days-->
<!--                            </button>-->
<!--                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem1">-->
<!--                                <a class="dropdown-item" href="#">Last 28 Days</a>-->
<!--                                <a class="dropdown-item" href="#">Last Month</a>-->
<!--                                <a class="dropdown-item" href="#">Last Year</a>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="card-content">
                        <div class="card-body pt-0">
                            <div id="gender-chart" class="mb-1"></div>
                            <div class="chart-info d-flex justify-content-between mb-1">
                                <div class="series-info d-flex align-items-center">
                                    <i class="feather icon-monitor font-medium-2 text-primary"></i>
                                    <span class="text-bold-600 mx-50">test</span>
                                    <span><?=0?></span>
                                </div>
<!--                                <div class="series-result">-->
<!--                                    <span>2%</span>-->
<!--                                    <i class="feather icon-arrow-up text-success"></i>-->
<!--                                </div>-->
                            </div>
                            <div class="chart-info d-flex justify-content-between mb-1">
                                <div class="series-info d-flex align-items-center">
                                    <i class="feather icon-tablet font-medium-2 text-warning"></i>
                                    <span class="text-bold-600 mx-50">test</span>
                                    <span><?=0?></span>
                                </div>
<!--                                <div class="series-result">-->
<!--                                    <span>8%</span>-->
<!--                                    <i class="feather icon-arrow-up text-success"></i>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <h4>test</h4>
<!--                        <div class="dropdown chart-dropdown">-->
<!--                            <button class="btn btn-sm border-0 dropdown-toggle p-0" type="button" id="dropdownItem2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--                                Last 7 Days-->
<!--                            </button>-->
<!--                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem2">-->
<!--                                <a class="dropdown-item" href="#">Last 28 Days</a>-->
<!--                                <a class="dropdown-item" href="#">Last Month</a>-->
<!--                                <a class="dropdown-item" href="#">Last Year</a>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="courses-chart" class="mb-1"></div>





                            <div class="chart-info d-flex justify-content-between mb-1">
                                <div class="series-info d-flex align-items-center">
                                    <i class="fa fa-circle-o text-bold-700 text-primary"></i>
                                    <span class="text-bold-600 ml-50"><?='$s->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </div>
                            <div class="chart-info d-flex justify-content-between mb-1">
                                <div class="series-info d-flex align-items-center">
                                    <i class="fa fa-circle-o text-bold-700 text-primary"></i>
                                    <span class="text-bold-600 ml-50"><?='$s->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </div>
                            <div class="chart-info d-flex justify-content-between mb-1">
                                <div class="series-info d-flex align-items-center">
                                    <i class="fa fa-circle-o text-bold-700 text-primary"></i>
                                    <span class="text-bold-600 ml-50"><?='$s->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </div>






<!--                            <div class="chart-info d-flex justify-content-between mb-1">-->
<!--                                <div class="series-info d-flex align-items-center">-->
<!--                                    <i class="fa fa-circle-o text-bold-700 text-warning"></i>-->
<!--                                    <span class="text-bold-600 ml-50">Pending</span>-->
<!--                                </div>-->
<!--                                <div class="item-result">-->
<!--                                    <span>14658</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="chart-info d-flex justify-content-between mb-75">-->
<!--                                <div class="series-info d-flex align-items-center">-->
<!--                                    <i class="fa fa-circle-o text-bold-700 text-danger"></i>-->
<!--                                    <span class="text-bold-600 ml-50">Rejected</span>-->
<!--                                </div>-->
<!--                                <div class="item-result">-->
<!--                                    <span>4758</span>-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <h4 class="card-title">test</h4>
<!--                        <div class="dropdown chart-dropdown">-->
<!--                            <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--                                Last 7 Days-->
<!--                            </button>-->
<!--                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem3">-->
<!--                                <a class="dropdown-item" href="#">Last 28 Days</a>-->
<!--                                <a class="dropdown-item" href="#">Last Month</a>-->
<!--                                <a class="dropdown-item" href="#">Last Year</a>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="card-content">
                        <div class="card-body py-0">
                            <div id="plans-chart"></div>
                        </div>
                        <ul class="list-group list-group-flush customer-info">




                            <li class="list-group-item d-flex justify-content-between ">
                                <div class="series-info">
                                    <i class="fa fa-circle font-small-3 text-primary"></i>
                                    <span class="text-bold-600"><?='$p->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </li>

                            <li class="list-group-item d-flex justify-content-between ">
                                <div class="series-info">
                                    <i class="fa fa-circle font-small-3 text-primary"></i>
                                    <span class="text-bold-600"><?='$p->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </li>

                            <li class="list-group-item d-flex justify-content-between ">
                                <div class="series-info">
                                    <i class="fa fa-circle font-small-3 text-primary"></i>
                                    <span class="text-bold-600"><?='$p->_name'?></span>
                                </div>
                                <div class="item-result">
                                    <span><?=0?></span>
                                </div>
                            </li>

<!--                            <li class="list-group-item d-flex justify-content-between ">-->
<!--                                <div class="series-info">-->
<!--                                    <i class="fa fa-circle font-small-3 text-warning"></i>-->
<!--                                    <span class="text-bold-600">Returning</span>-->
<!--                                </div>-->
<!--                                <div class="item-result">-->
<!--                                    <span>258</span>-->
<!--                                </div>-->
<!--                            </li>-->
<!--                            <li class="list-group-item d-flex justify-content-between ">-->
<!--                                <div class="series-info">-->
<!--                                    <i class="fa fa-circle font-small-3 text-danger"></i>-->
<!--                                    <span class="text-bold-600">Referrals</span>-->
<!--                                </div>-->
<!--                                <div class="item-result">-->
<!--                                    <span>149</span>-->
<!--                                </div>-->
<!--                            </li>-->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- // Statistics Card section end-->

@endsection

@section('scripts')
<!-- BEGIN: Page JS-->
<script src="<?=url('')?>/app-assets/vendors/js/charts/apexcharts.min.js"></script>
<script>

    $(window).on("load", function () {

        var $primary = '#7367F0';
        var $success = '#28C76F';
        var $danger = '#EA5455';
        var $warning = '#FF9F43';
        var $info = '#00cfe8';
        var $primary_light = '#A9A2F6';
        var $danger_light = '#f29292';
        var $success_light = '#55DD92';
        var $warning_light = '#ffc085';
        var $info_light = '#1fcadb';
        var $strok_color = '#b9c3cd';
        var $label_color = '#e7e7e7';
        var $white = '#fff';

        // gender Chart
    // ----------------------------------

    var genderChartOptions = {
        chart: {
            type: 'donut',
            height: 325,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        labels: ['Males', 'Females'],
        series: [00, 00],
        legend: { show: false },
        // comparedResult: [2, -3, 8],
        stroke: { width: 0 },
        colors: [$primary, $warning],
        fill: {
            type: 'gradient',
            gradient: {
                gradientToColors: [$primary_light, $warning_light, $danger_light]
            }
        }
    }

    var genderChart = new ApexCharts(
        document.querySelector("#gender-chart"),
        genderChartOptions
    );

    genderChart.render();

    // courses Chart starts
    // -----------------------------

    <?php
    $course_series = array();
    $course_labels = array();
    $course_total = 0;
    // foreach ( $course_statistics as $s ) {
	//     $course_labels[]=$s->course_name;
    //     $course_series[]=intval($s->count);
    //     $course_total+=$s->count;
    // }
    ?>
    var coursesChartOptions = {
        chart: {
            height: 325,
            type: 'radialBar',
        },
        colors: [$primary, $warning, $danger],
        fill: {
            type: 'gradient',
            gradient: {
                // enabled: true,
                shade: 'dark',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: [$primary_light, $warning_light, $danger_light],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            },
        },
        stroke: {
            lineCap: 'round'
        },
        plotOptions: {
            radialBar: {
                size: 165,
                hollow: {
                    size: '20%'
                },
                track: {
                    strokeWidth: '100%',
                    margin: 15,
                },
                dataLabels: {
                    name: {
                        fontSize: '18px',
                    },
                    value: {
                        fontSize: '16px',
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function (w) {
                            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                            return <?=$course_total?>
                        }
                    }
                }
            }
        },
        labels: [],
        series: [],
    }

    var coursesChart = new ApexCharts(
        document.querySelector("#courses-chart"),
        coursesChartOptions
    );

        coursesChart.render();


    // plans Chart
    // -----------------------------
	    <?php
	    $plan_labels = array();
	    $plan_series = array();
	    $plan_total = 0;
	    // foreach ( $plan_statistics as $p ) {
		//     $plan_labels[]=$p->plan_name;
		//     $plan_series[]=intval($p->count);
		//     $plan_total+=$p->count;
	    // }
	    ?>
    var plansChartOptions = {
        chart: {
            type: 'pie',
            height: 330,
            dropShadow: {
                enabled: false,
                blur: 5,
                left: 1,
                top: 1,
                opacity: 0.2
            },
            toolbar: {
                show: false
            }
        },
        labels: <?=json_encode($plan_labels)?>,
        series: <?=json_encode($plan_series)?>,
        dataLabels: {
            enabled: false
        },
        legend: { show: false },
        stroke: {
            width: 5
        },
        colors: [$primary, $warning, $danger],
        fill: {
            type: 'gradient',
            gradient: {
                gradientToColors: [$primary_light, $warning_light, $danger_light]
            }
        }
    }

    var plansChart = new ApexCharts(
        document.querySelector("#plans-chart"),
        plansChartOptions
    );

        plansChart.render();

    });
</script>
@endsection
