<?php
$page_title = 'Dashboard';
require_once 'templates/header.php';
?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-graduate me-2"></i>Total Students</h5>
                <p class="card-text fs-4" id="total-students">--</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chalkboard-teacher me-2"></i>Total Teachers</h5>
                <p class="card-text fs-4" id="total-teachers">--</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-smile me-2"></i>Avg. Student Mood (Today)</h5>
                <p class="card-text fs-4" id="avg-student-mood">--</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-smile-beam me-2"></i>Avg. Teacher Mood (Today)</h5>
                <p class="card-text fs-4" id="avg-teacher-mood">--</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-chart-line me-1"></i>
            Mood Analysis
        </div>
        <div class="btn-group btn-group-sm" role="group" aria-label="Chart Filters">
            <button type="button" class="btn btn-outline-secondary active" id="filter-daily">Last 7 Days</button>
            <button type="button" class="btn btn-outline-secondary" id="filter-monthly">Last 12 Months</button>
            <button type="button" class="btn btn-outline-secondary" id="filter-yearly">By Year</button>
        </div>
    </div>
    <div class="card-body" style="height: 300px;">
         <canvas id="moodChart"></canvas>
    </div>
</div>

<?php
// Link the new JavaScript file for charts
$custom_js = 'admin/assets/js/dashboard-charts.js';
require_once 'templates/footer.php';
?>
