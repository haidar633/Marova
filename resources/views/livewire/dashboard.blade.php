<div class="container-fluid py-4">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">calendar_today</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Attempts in {{ $currentYear }}</p>
                        <h4 class="mb-0">{{ $totalAttemptsYTD }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Current</span> year activity</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">history</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">All-Time Attempts</p>
                        <h4 class="mb-0">{{ $allTimeAttempts }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-info text-sm font-weight-bolder">Total</span> recorded since start</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">done_all</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Success Rate</p>
                        <h4 class="mb-0">{{ $successRate }}%</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Consistent</span> performance</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Monthly Line Chart -->
        <div class="col-lg-6 col-md-12 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="monthly-chart" class="chart-canvas" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0 ">Monthly Activity ({{ $currentYear }})</h6>
                    <p class="text-sm ">Tracking attempts month by month</p>
                </div>
            </div>
        </div>

        <!-- Yearly Bar Chart -->
        <div class="col-lg-6 col-md-12 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="yearly-chart" class="chart-canvas" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0 ">Yearly Comparison</h6>
                    <p class="text-sm ">Total attempts per year</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Category Distribution Doughnut Chart -->
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="category-chart" class="chart-canvas" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0 ">Category Breakdown</h6>
                    <p class="text-sm ">Distribution of attempt types</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared chart options for line/bar
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: { size: 12, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: { size: 12, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 },
                    }
                }
            }
        };

        // 1. Monthly Line Chart
        var ctx1 = document.getElementById("monthly-chart").getContext("2d");
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: "Attempts",
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255, 255, 255, .8)",
                    pointBorderColor: "transparent",
                    borderColor: "rgba(255, 255, 255, .8)",
                    backgroundColor: "transparent",
                    fill: true,
                    data: @json($monthlyCounts),
                }],
            },
            options: commonOptions
        });

        // 2. Yearly Comparison Bar Chart
        var ctx2 = document.getElementById("yearly-chart").getContext("2d");
        new Chart(ctx2, {
            type: "bar",
            data: {
                labels: @json($yearlyLabels),
                datasets: [{
                    label: "Total Attempts",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "rgba(255, 255, 255, .8)",
                    data: @json($yearlyCounts),
                    maxBarThickness: 50
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(255, 255, 255, .2)'
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 10,
                            beginAtZero: true,
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                            color: "#fff"
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            display: true,
                            color: '#f8f9fa',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });

        // 3. Category Doughnut Chart
        var ctx3 = document.getElementById("category-chart").getContext("2d");
        new Chart(ctx3, {
            type: "doughnut",
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    label: "Count",
                    weight: 9,
                    cutout: 60,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: ['#fff', 'rgba(255, 255, 255, 0.6)', 'rgba(255, 255, 255, 0.4)', 'rgba(255, 255, 255, 0.2)'],
                    data: @json($categoryCounts),
                    fill: false
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { color: '#fff', padding: 10, font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endpush
