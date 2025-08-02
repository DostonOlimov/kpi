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
            <!-- Total Revenue Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Jami miqdor
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format(245680, 2) }}
                                </div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> 12.5% oldingi oydan ko'p
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-primary">
                                    <i class="fa fa-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Customers Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Xodimlar soni
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format(1247) }}
                                </div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> 8.2% yangi xodim
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-success">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Ish hajmi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">3.48%</div>
                                <div class="text-danger small">
                                    <i class="fa fa-arrow-down"></i> 2.1% oldingi oydan kam
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

            <!-- Active Projects Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Jami topshiriqlar
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                                <div class="text-success small">
                                    <i class="fa fa-arrow-up"></i> 5 ta yangi haftada
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kpi-icon gradient-warning">
                                    <i class="fa fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Revenue Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card chart-container">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-line me-2"></i>
                            O'shish ko'rsatkichlari (oxirgi 6 oyda)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Traffic Sources Chart -->
            <div class="col-lg-4 mb-4">
                <div class="card chart-container">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-pie me-2"></i>
                            Bo'limlar ko'rsatkichlari
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
{{--        <div class="row mb-4">--}}
{{--            <div class="col-12">--}}
{{--                <div class="card table-container">--}}
{{--                    <div class="card-header bg-transparent border-0">--}}
{{--                        <div class="d-flex justify-content-between align-items-center">--}}
{{--                            <h5 class="mb-0">--}}
{{--                                <i class="fa fa-history me-2"></i>--}}
{{--                                Recent Activity--}}
{{--                            </h5>--}}
{{--                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="table-responsive">--}}
{{--                            <table class="table table-hover mb-0">--}}
{{--                                <thead class="table-light">--}}
{{--                                <tr>--}}
{{--                                    <th>Date</th>--}}
{{--                                    <th>Customer</th>--}}
{{--                                    <th>Product</th>--}}
{{--                                    <th>Amount</th>--}}
{{--                                    <th>Status</th>--}}
{{--                                    <th>Action</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @php--}}
{{--                                    $activities = [--}}
{{--                                        ['date' => '2024-01-15', 'customer' => 'John Smith', 'product' => 'Premium Plan', 'amount' => 299.00, 'status' => 'completed'],--}}
{{--                                        ['date' => '2024-01-14', 'customer' => 'Sarah Johnson', 'product' => 'Basic Plan', 'amount' => 99.00, 'status' => 'pending'],--}}
{{--                                        ['date' => '2024-01-13', 'customer' => 'Mike Davis', 'product' => 'Enterprise Plan', 'amount' => 599.00, 'status' => 'completed'],--}}
{{--                                        ['date' => '2024-01-12', 'customer' => 'Emily Wilson', 'product' => 'Premium Plan', 'amount' => 299.00, 'status' => 'failed'],--}}
{{--                                        ['date' => '2024-01-11', 'customer' => 'Robert Brown', 'product' => 'Basic Plan', 'amount' => 99.00, 'status' => 'completed'],--}}
{{--                                    ];--}}
{{--                                @endphp--}}

{{--                                @foreach($activities as $activity)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y') }}</td>--}}
{{--                                        <td>--}}
{{--                                            <div class="d-flex align-items-center">--}}
{{--                                                <div class="avatar avatar-sm me-2">--}}
{{--                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">--}}
{{--                                                        <i class="fa fa-user text-white"></i>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                {{ $activity['customer'] }}--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>{{ $activity['product'] }}</td>--}}
{{--                                        <td>${{ number_format($activity['amount'], 2) }}</td>--}}
{{--                                        <td>--}}
{{--                                            @switch($activity['status'])--}}
{{--                                                @case('completed')--}}
{{--                                                    <span class="badge bg-success">Completed</span>--}}
{{--                                                    @break--}}
{{--                                                @case('pending')--}}
{{--                                                    <span class="badge bg-warning">Pending</span>--}}
{{--                                                    @break--}}
{{--                                                @case('failed')--}}
{{--                                                    <span class="badge bg-danger">Failed</span>--}}
{{--                                                    @break--}}
{{--                                                @default--}}
{{--                                                    <span class="badge bg-secondary">Unknown</span>--}}
{{--                                            @endswitch--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="btn-group" role="group">--}}
{{--                                                <button type="button" class="btn btn-sm btn-outline-primary">--}}
{{--                                                    <i class="fa fa-eye"></i>--}}
{{--                                                </button>--}}
{{--                                                <button type="button" class="btn btn-sm btn-outline-secondary">--}}
{{--                                                    <i class="fa fa-edit"></i>--}}
{{--                                                </button>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <!-- Quick Stats Row -->--}}
{{--        <div class="row mb-4">--}}
{{--            <div class="col-md-4 mb-3">--}}
{{--                <div class="card kpi-card">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <i class="fa fa-clock fa-2x text-primary mb-2"></i>--}}
{{--                        <h6>Avg. Response Time</h6>--}}
{{--                        <h4 class="text-primary">2.4 hrs</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-4 mb-3">--}}
{{--                <div class="card kpi-card">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <i class="fa fa-star fa-2x text-warning mb-2"></i>--}}
{{--                        <h6>Customer Satisfaction</h6>--}}
{{--                        <h4 class="text-warning">4.8/5</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-4 mb-3">--}}
{{--                <div class="card kpi-card">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <i class="fa fa-trophy fa-2x text-success mb-2"></i>--}}
{{--                        <h6>Goals Achieved</h6>--}}
{{--                        <h4 class="text-success">87%</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection
@section('scripts')
    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue ($)',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Traffic Chart
            const trafficCtx = document.getElementById('trafficChart').getContext('2d');
            const trafficChart = new Chart(trafficCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Direct', 'Social Media', 'Email', 'Search Engine'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c'],
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
