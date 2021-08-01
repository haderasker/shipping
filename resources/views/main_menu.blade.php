{{-- <li class=" nav-item">
    <a class="d-flex align-items-center" href="index.html"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboards</span><span class="badge badge-light-warning badge-pill ml-auto mr-1">2</span></a>
    <ul class="menu-content">
        <li><a class="d-flex align-items-center" href="dashboard-analytics.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Analytics">Analytics</span></a>
        </li>
        <li><a class="d-flex align-items-center" href="dashboard-ecommerce.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="eCommerce">eCommerce</span></a>
        </li>
    </ul>
</li>
<li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Apps &amp; Pages</span><i data-feather="more-horizontal"></i>
</li>
<li class=" nav-item"><a class="d-flex align-items-center" href="app-email.html"><i data-feather="mail"></i><span class="menu-title text-truncate" data-i18n="Email">Email</span></a>
</li> --}}




<li class=" nav-item">
	<a href="<?= url(''); ?>/dashboard">
		<i data-feather="home"></i>
		<span class="menu-title">Dashboard</span>
	</a>
</li>

<li class="nav-item">
    <a class="d-flex align-items-center" href="#">
        <i data-feather="bar-chart-2"></i>
        <span class="menu-title">Reports</span>
    </a>
    <ul class="menu-content">
        <li><a class="d-flex align-items-center" href="<?= url(''); ?>/report/planbydriver"><i data-feather="circle"></i><span class="menu-item text-truncate">Plan By Driver</span></a>
        </li>
        <li><a class="d-flex align-items-center" href="<?= url(''); ?>/report/planbyzone"><i data-feather="circle"></i><span class="menu-item text-truncate">Plan By Zone</span></a>
        </li>
        <li><a class="d-flex align-items-center" href="<?= url(''); ?>/report/delivereditems"><i data-feather="circle"></i><span class="menu-item text-truncate">Delivered Items</span></a>
        </li>
        <li><a class="d-flex align-items-center" href="<?= url(''); ?>/report/returneditems"><i data-feather="circle"></i><span class="menu-item text-truncate">Returnd Items</span></a>
        </li>
    </ul>
</li>

<li class=" navigation-header"><span>Shipments</span></li>
<li class=" nav-item">
	<a href="<?= url(''); ?>/shipment">
		<i data-feather="archive"></i>
		<span class="menu-title">All Shipments</span>
	</a>
</li>
<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/pending">
        <i data-feather="circle" class="text-warning"></i>
        <span class="menu-title">Pending</span>
    </a>
</li>
<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/approved">
        <i data-feather="circle" class="text-primary"></i>
        <span class="menu-title">Approved</span>
    </a>
</li>
{{-- <li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/scheduled">
        <i data-feather="circle" class="text-secondary"></i>
        <span class="menu-title">Scheduled</span>
    </a>
</li> --}}
{{-- <li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/readyshipments">
        <i data-feather="circle text-warning"></i>
        <span class="menu-title">Ready</span>
    </a>
</li> --}}
<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/shipped">
        <i data-feather="circle" class="text-secondary"></i>
        <span class="menu-title">Shipped</span>
    </a>
</li>

{{-- <li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/delivered">
        <i data-feather="circle" class="text-success"></i>
        <span class="menu-title">Delivered</span>
    </a>
</li>
<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/returned">
        <i data-feather="circle" class="text-danger"></i>
        <span class="menu-title">Returned</span>
    </a>
</li> --}}

<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/completed">
        <i data-feather="circle" class="text-success"></i>
        <span class="menu-title">Completed</span>
    </a>
</li>
<li>
    <a class="d-flex align-items-center" href="<?= url(''); ?>/shipment/cancelled">
        <i data-feather="circle" class="text-danger"></i>
        <span class="menu-title">Cancelled</span>
    </a>
</li>

<li class=" nav-item">
    <a href="<?= url(''); ?>/shipment/view">
        <i data-feather="plus"></i>
        <span class="menu-title">Add Shipment</span>
    </a>
</li>

<?php
if (App\Helpers\AppHelper::canView('item')){
    ?>
    <li class=" navigation-header"><span>Plans</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/plan">
            <i data-feather="truck"></i>
            <span class="menu-title">Daily Plan</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/plan/driverplan">
            <i data-feather="list"></i>
            <span class="menu-title">Driver Plan</span>
        </a>
    </li>
    {{-- <li class=" nav-item">
        <a href="<?= url(''); ?>/custody">
            <i data-feather="dollar-sign"></i>
            <span class="menu-title">Driver Custody</span>
        </a>
    </li> --}}
    {{-- <li class=" nav-item">
        <a href="<?= url(''); ?>/item/view">
            <i data-feather="plus"></i>
            <span class="menu-title">Create Plan</span>
        </a>
    </li> --}}
    <?php
}
?>

<li class=" navigation-header"><span>Custody</span></li>
<li class=" nav-item">
    <a href="<?= url(''); ?>/custody/withdrawal">
        <i data-feather="trending-up"></i>
        <span class="menu-title">Withdrawal</span>
    </a>
</li>
<li class=" nav-item">
    <a href="<?= url(''); ?>/custody/adjustment">
        <i data-feather="trending-down"></i>
        <span class="menu-title">Adjustment</span>
    </a>
</li>


<?php
if (App\Helpers\AppHelper::canView('item')){
    ?>
    <li class=" navigation-header"><span>Items</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/item">
            <i data-feather="shopping-bag"></i>
            <span class="menu-title">All Items</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/item/view">
            <i data-feather="plus"></i>
            <span class="menu-title">Add Item</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/variation">
            <i data-feather="flag"></i>
            <span class="menu-title">Variations</span>
        </a>
    </li>
    <?php
}
?>

<?php
if (App\Helpers\AppHelper::canView('client')){
    ?>
    <li class=" navigation-header"><span>Clients</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/client">
            <i data-feather="users"></i>
            <span class="menu-title">All Clients</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/client/view">
            <i data-feather="plus"></i>
            <span class="menu-title">Add Client</span>
        </a>
    </li>
<?php
}
?>

<?php
if (App\Helpers\AppHelper::canView('customer')){
    ?>
    <li class=" navigation-header"><span>Customers</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/customer">
            <i data-feather="users"></i>
            <span class="menu-title">All Customers</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/customer/view">
            <i data-feather="plus"></i>
            <span class="menu-title">Add Customer</span>
        </a>
    </li>
<?php
}
?>

<?php
if (App\Helpers\AppHelper::canView('customer')){
    ?>
    <li class=" navigation-header"><span>Drivers</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/driver">
            <i data-feather="users"></i>
            <span class="menu-title">All Drivers</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/driver/view">
            <i data-feather="plus"></i>
            <span class="menu-title">Add Driver</span>
        </a>
    </li>
<?php
}
?>

<?php
if (App\Helpers\AppHelper::canView('')){
    ?>
    <li class=" navigation-header"><span>Settings</span></li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/user/">
            <i data-feather="user"></i>
            <span class="menu-title">Users</span>
        </a>
    </li>
    <li class=" nav-item">
        <a href="<?= url(''); ?>/zone/">
            <i data-feather="map-pin"></i>
            <span class="menu-title">Zones</span>
        </a>
    </li>
    {{-- <li class=" nav-item">
        <a href="<?= url(''); ?>/user/">
            <i data-feather="user"></i>
            <span class="menu-title">Integration</span>
        </a>
    </li> --}}
<?php
}
?>
