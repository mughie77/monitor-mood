<?php
$page_title = 'Dasbor';
require_once 'templates/header.php';
?>

<h1 class="mb-4">Dasbor</h1>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card summary-card summary-card-1">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Siswa</h5>
                        <p class="card-text fs-2 fw-bold" id="total-students">--</p>
                    </div>
                    <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card summary-card summary-card-2">
            <div class="card-body">
                 <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Guru</h5>
                        <p class="card-text fs-2 fw-bold" id="total-teachers">--</p>
                    </div>
                    <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card summary-card summary-card-3">
            <div class="card-body">
                 <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Rata-rata Mood Siswa <span class="period-text d-block small fw-normal"></span></h5>
                        <p class="card-text fs-4 fw-bold" id="avg-student-mood">--</p>
                    </div>
                    <div class="card-icon"><i class="fas fa-smile"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card summary-card summary-card-4">
            <div class="card-body">
                 <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Rata-rata Mood Guru <span class="period-text d-block small fw-normal"></span></h5>
                        <p class="card-text fs-4 fw-bold" id="avg-teacher-mood">--</p>
                    </div>
                    <div class="card-icon"><i class="fas fa-smile-beam"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><i class="fas fa-chart-line me-1"></i> Analisis Suasana Hati</div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Filter Grafik">
                    <button type="button" class="btn btn-outline-secondary active" id="filter-daily">7 Hari Terakhir</button>
                    <button type="button" class="btn btn-outline-secondary" id="filter-monthly">12 Bulan Terakhir</button>
                    <button type="button" class="btn btn-outline-secondary" id="filter-yearly">Per Tahun</button>
                </div>
            </div>
            <div class="card-body" style="height: 300px;"><canvas id="moodChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Laporan Perundungan per Bulan</div>
            <div class="card-body" style="height: 300px;"><canvas id="bullyingChart"></canvas></div>
        </div>
    </div>
</div>

<?php
// Link the new JavaScript file for charts
$custom_js = 'admin/assets/js/dashboard-charts.js';
require_once 'templates/footer.php';
?>
