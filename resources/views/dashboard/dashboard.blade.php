@extends('layouts.app')

@section('styles')
    <style>
        .kpi-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border: none;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
        }
        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
@endsection
@section('content')

    <!-- Dashboard Header -->
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fa fa-tachometer-alt me-2"></i>
                Bosh sahifa
            </h1>

        </div>

        <!-- Welcome Message -->
        <div class="alert alert-info border-0 mb-4" style="border-radius: 15px;">
            <h5 class="alert-heading">
                <i class="fa fa-user-circle me-2"></i>
                Xush kelibsiz!
            </h5>
            <p class="mb-0">{{ Auth::user()->first_name  }} {{ Auth::user()->last_name  }}.</p>
        </div>

        <!-- KPI Cards Row -->
        <div class="row mb-4">
            <!-- Total Employees Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Xodimlar soni
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalEmployees) }}
                                </div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> {{ $totalEmployees > 0 ? round((($totalEmployees - 100) / 100) * 100, 1) : 0 }}% oldingi oydan ko'p
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-primary">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tasks Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Jami topshiriqlar
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalTasks) }}
                                </div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> {{ $totalTasks > 0 ? round((($totalTasks - 50) / 50) * 100, 1) : 0 }}% yangi topshiriqlar
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-success">
                                    <i class="fa fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Completion Rate Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    KPI bajarilishi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completionPercentage }}%</div>
                                <div class="{{ $completionPercentage >= 50 ? 'text-success' : 'text-danger' }} small">
                                    <i class="fa {{ $completionPercentage >= 50 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> 
                                    {{ abs($completionPercentage - 50) }}% {{ $completionPercentage >= 50 ? 'maqsaddan yuqori' : 'maqsaddan past' }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-info">
                                    <i class="fa fa-percent"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed KPIs Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Bajarilgan KPI-lar
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedKpis }}</div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> {{ $completedKpis > 0 ? round((($completedKpis - 10) / 10) * 100, 1) : 0 }}% bu haftada
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-warning">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- KPI Progress Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card chart-container">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-line me-2"></i>
                            KPI bajarilish ko'rsatkichi (oxirgi 6 oyda)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="kpiProgressChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- KPI Status Distribution Chart -->
            <div class="col-lg-4 mb-4">
                <div class="card chart-container">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-pie me-2"></i>
                            KPI holati taqsimoti
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="kpiStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tasks Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card table-container">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-history me-2"></i>
                                So'ngi topshiriqlar
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>KPI</th>
                                    <th>Topshiriq nomi</th>
                                    <th>Holati</th>
                                    <th>Sana</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentTasks as $task)
                                    <tr>
                                        <td>
                                            @if($task->user_kpi && $task->user_kpi->kpi)
                                                {{ $task->user_kpi->kpi->name }}
                                            @else
                                                Noma'lum KPI
                                            @endif
                                        </td>
                                        <td>{{ $task->name ?? 'Nomsiz' }}</td>
                                        <td>
                                            @if($task->user_kpi)
                                                @switch($task->user_kpi->status)
                                                    @case('new')
                                                        <span class="badge bg-info">Yangi</span>
                                                        @break
                                                    @case('in_progress')
                                                        <span class="badge bg-warning">Jarayonda</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Bajarilgan</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Noma'lum</span>
                                                @endswitch
                                            @else
                                                <span class="badge bg-secondary">Ma'lumot yo'q</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->created_at ? $task->created_at->format('d.m.Y H:i') : 'Noma\'lum sana' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Hozircha hech qanday topshiriq mavjud emas</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // KPI Progress Chart
            const progressCtx = document.getElementById('kpiProgressChart').getContext('2d');
            
            // Prepare data for the chart
            const monthlyLabels = [@foreach($monthlyData as $data) '{{ $data['month'] }}', @endforeach];
            const completedData = [@foreach($monthlyData as $data) {{ $data['completed'] }}, @endforeach];
            const totalData = [@foreach($monthlyData as $data) {{ $data['total'] }}, @endforeach];
            
            const progressChart = new Chart(progressCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Bajarilgan KPI-lar',
                        data: completedData,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Jami KPI-lar',
                        data: totalData,
                        borderColor: '#764ba2',
                        backgroundColor: 'rgba(118, 75, 162, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // KPI Status Chart
            const statusCtx = document.getElementById('kpiStatusChart').getContext('2d');
            
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Yangi', 'Jarayonda', 'Bajarilgan'],
                    datasets: [{
                        data: [
                            {{ $kpiStatusData['new'] }}, 
                            {{ $kpiStatusData['in_progress'] }}, 
                            {{ $kpiStatusData['completed'] }}
                        ],
                        backgroundColor: ['#667eea', '#f093fb', '#11998e'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection