@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fa fa-users mr-1"></i>&nbsp; Bo'limlarning Samaradorlik ko'rsatkichlari
                    </li>
                </ol>
            </div>
            {{-- <div class="d-flex justify-content-between align-items-center">

                <div>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fa fa-print me-2"></i>Print Report
                    </button>
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Barcha bo'limlar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overallStats['total_departments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Xodimlar soni
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overallStats['total_employees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                O'rtacha samaradorlik ko'rsatkichi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($overallStats['total_employees'] > 0)
                                    {{ round($overallStats['sum_score'] / $overallStats['total_employees'], 2) }}%
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-line-chart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Yuqori samaradorlikka erishganlar soni
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overallStats['high_performers_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Statistics Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bo'limlarning ko'rsatkichlari</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="departmentTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Bo'lim nomi</th>
                                    <th>Xodimlar</th>
                                    <th>Topshiriqlar soni</th>
                                    <th>O'rtacha baho</th>
                                    <th>Ijro holati</th>
                                    <th>Yuqori ko'rsatkichlar</th>
                                    <th>Past ko'rsatkichlar</th>
                                    <th>Harakat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentStats as $dept)
                                @php $avg_score = ($dept->total_employees != 0) ? $dept->sum_score / $dept->total_employees : 0; @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $dept->department_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $dept->total_employees }}</span>
                                    </td>
                                    <td>{{ $dept->total_kpis }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2">{{ $avg_score }}%</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar 
                                                    @if($avg_score >= 80) bg-success
                                                    @elseif($avg_score >= 60) bg-warning
                                                    @else bg-danger
                                                    @endif" 
                                                    style="width: {{ $avg_score }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($avg_score >= 80)
                                            <span class="badge badge-success">Ajoyib</span>
                                        @elseif($avg_score >= 60)
                                            <span class="badge badge-warning">Yaxshi</span>
                                        @else
                                            <span class="badge badge-danger">Past</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $dept->high_performers }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">{{ $dept->low_performers }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kpi.department.detail', $dept->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i> Tafsilotlarni ko'rish
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        Ma'lumotlar mavjud emas
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers by Department -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Eng yuqori ko'rsatkichga erishgan bo'limlar</h6>
                </div>
                <div class="card-body">
                    @forelse($topPerformers as $deptName => $performers)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">{{ $deptName }}</h6>
                        <div class="row">
                            @foreach($performers->take(3) as $performer)
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-success">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-success">
                                                    <i class="fa fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $performer->user_name }}</div>
                                                <div class="text-muted small">{{ $performer->email }}</div>
                                                <div class="text-success font-weight-bold">{{ $performer->avg_score }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Ma'lumotlar mavjud emas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Trends Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kpi ko'rsatkichlari (oxirgi 6 oy)</h6>
                </div>
                <div class="card-body">
                    <canvas id="kpiTrendsChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // KPI Trends Chart
    const ctx = document.getElementById('kpiTrendsChart').getContext('2d');
    const trendsData = @json($kpiTrends);
    
    const datasets = [];
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
    let colorIndex = 0;
    
    Object.keys(trendsData).forEach(department => {
        const data = trendsData[department];
        const monthlyScores = {};
        
        data.forEach(item => {
            monthlyScores[item.month] = item.avg_score;
        });
        
        datasets.push({
            label: department,
            data: Object.values(monthlyScores),
            borderColor: colors[colorIndex % colors.length],
            backgroundColor: colors[colorIndex % colors.length] + '20',
            tension: 0.1
        });
        colorIndex++;
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['6 oy oldin', '5 oy oldin', '4 oy oldin', '3 oy oldin', '2 oy oldin', 'Oxirgi oy'],
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Bo\'limlar KPI ko\'rsatkichlari (oxirgi 6 oy)'
                }
            }
        }
    });
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.progress {
    height: 0.5rem;
}
.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
@media print {
    .btn {
        display: none !important;
    }
}
</style>
@endsection
