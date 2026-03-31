<?php
$page_title = 'Dasbor';
require_once 'templates/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Dasbor</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Siswa -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border-b-4 border-fun-pink">
        <div class="flex justify-between items-start">
            <div>
                <h5 class="text-gray-500 font-semibold mb-1">Total Siswa</h5>
                <p class="text-3xl font-bold text-gray-800" id="total-students">--</p>
            </div>
            <div class="bg-pink-100 p-3 rounded-2xl text-fun-pink text-xl">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    <!-- Total Guru -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border-b-4 border-fun-orange">
        <div class="flex justify-between items-start">
            <div>
                <h5 class="text-gray-500 font-semibold mb-1">Total Guru</h5>
                <p class="text-3xl font-bold text-gray-800" id="total-teachers">--</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-2xl text-fun-orange text-xl">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    <!-- Rata-rata Mood Siswa -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border-b-4 border-fun-yellow">
        <div class="flex justify-between items-start">
            <div>
                <h5 class="text-gray-500 font-semibold mb-1">Mood Siswa <span class="period-text block text-xs font-normal"></span></h5>
                <p class="text-2xl font-bold text-gray-800" id="avg-student-mood">--</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-2xl text-fun-yellow text-xl">
                <i class="fas fa-smile"></i>
            </div>
        </div>
    </div>
    <!-- Rata-rata Mood Guru -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border-b-4 border-fun-blue">
        <div class="flex justify-between items-start">
            <div>
                <h5 class="text-gray-500 font-semibold mb-1">Mood Guru <span class="period-text block text-xs font-normal"></span></h5>
                <p class="text-2xl font-bold text-gray-800" id="avg-teacher-mood">--</p>
            </div>
            <div class="bg-green-100 p-3 rounded-2xl text-fun-blue text-xl">
                <i class="fas fa-smile-beam"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="font-bold text-gray-700"><i class="fas fa-chart-line mr-2 text-fun-purple"></i> Analisis Suasana Hati</div>
                <div class="flex bg-gray-100 p-1 rounded-xl" role="group">
                    <button type="button" class="px-4 py-1.5 text-xs font-bold rounded-lg transition active-filter" id="filter-daily">7 Hari</button>
                    <button type="button" class="px-4 py-1.5 text-xs font-bold rounded-lg transition text-gray-500 hover:text-gray-700" id="filter-monthly">12 Bulan</button>
                    <button type="button" class="px-4 py-1.5 text-xs font-bold rounded-lg transition text-gray-500 hover:text-gray-700" id="filter-yearly">Tahun</button>
                </div>
            </div>
            <div class="p-6 relative" style="height: 350px;">
                <canvas id="moodChart"></canvas>
            </div>
        </div>
    </div>
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden h-full">
            <div class="px-6 py-4 border-b font-bold text-gray-700">
                <i class="fas fa-chart-bar mr-2 text-fun-pink"></i> Laporan Perundungan
            </div>
            <div class="p-6 relative" style="height: 350px;">
                <canvas id="bullyingChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .active-filter {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        color: #4D96FF;
    }
</style>

<?php
// Link the new JavaScript file for charts
$custom_js = 'admin/assets/js/dashboard-charts.js';
require_once 'templates/footer.php';
?>
