@extends('layouts.app')

 <style>
        .tasks-section .task-item {
            min-height: 180px;
            background-color: #f8f9fc;
            transition: all 0.3s;
        }

        .tasks-section .task-item:hover {
            background-color: #eaf6fb;
        }

        .tasks-section .row {
            /* Make all task cards same height */
            display: flex;
            flex-wrap: wrap;
        }

        .tasks-section .col-md-6 {
            display: flex;
        }

        .tasks-section .task-item {
            width: 100%;
        }

        .toggle-tasks-btn {
            min-width: 110px;
        }

        .show-full-desc {
            cursor: pointer;
            color: #4e73df;
            text-decoration: underline;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-secondary {
            border-left: 0.25rem solid #858796 !important;
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

        .kpi-section {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 2rem;
        }

        .kpi-section:last-child {
            border-bottom: none;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #5a5c69;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #858796;
            text-transform: uppercase;
        }

        .task-item {
            background-color: #f8f9fc;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background-color: #eaecf4;
            transform: translateY(-2px);
        }

        .task-score {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .progress {
            height: 0.5rem;
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

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fa fa-users mr-1"></i>&nbsp; {{ $user->full_name }}ning samaradorlik ko'rsatkichlari
                        </li>
                    </ol>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url()->previous() }}">Foydalanuvchi
                                        KPI</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $user->full_name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Orqaga
                        </a>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fa fa-print me-2"></i>Chop etish
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Jami KPI
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $userStats['total_kpis'] }} ta
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Umumiy Ball
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $userStats['sum_score'] }} ball
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Bajarilgan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $userStats['completed_kpis'] }} ta
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Maksimal Ball
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            100 ball
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Details -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">KPI Tafsilotlari</h6>
                    </div>
                    <div class="card-body">
                        @forelse($parentKpis as $parentKpi)
                            <div class="kpi-section mb-4">
                                <!-- Parent KPI -->
                                <div class="parent-kpi mb-3">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h5 class="text-primary mb-1">
                                                        <i class="fa fa-folder-open me-2"></i>
                                                        {{ $parentKpi->name_uz ?? $parentKpi->name }}
                                                    </h5>
                                                    <p class="text-muted mb-0">
                                                        {{ $parentKpi->description_uz ?? $parentKpi->description }}</p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge badge-primary">Asosiy KPI</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Child KPIs -->
                                @if ($userKpis->count() > 0)
                                    <div class="child-kpis ms-4">
                                        @foreach ($userKpis as $userKpi)
                                            @if ($userKpi->kpi?->parent_id == $parentKpi->id)
                                                <div class="child-kpi mb-3">
                                                    <div class="card border-left-secondary">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="text-secondary mb-2">
                                                                        <i class="fa fa-task me-2"></i>
                                                                        {{ $userKpi->kpi?->name }}
                                                                    </h6>
                                                                    <p class="text-muted small mb-2">
                                                                        {{ $userKpi->kpi?->description }}</p>
                                                                    <span
                                                                        class="badge badge-{{ $userKpi->kpi?->type == \App\Models\Kpi::SELF_BY_PERSON ? 'success' : 'info' }}">
                                                                        <div
                                                                            style="width:20px; height:40px border-radius:50%">
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @if ($userKpi)
                                                                        <div class="kpi-stats">
                                                                            <div class="row text-center">
                                                                                <div class="col-4">
                                                                                    <div class="stat-item">
                                                                                        <div class="stat-value">
                                                                                            {{ $userKpi->current_score }}
                                                                                        </div>
                                                                                        <div class="stat-label">Joriy Ball
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-4">
                                                                                    <div class="stat-item">
                                                                                        <div class="stat-value">
                                                                                            {{ $userKpi->target_score }}
                                                                                        </div>
                                                                                        <div class="stat-label">Maqsad</div>
                                                                                    </div>
                                                                                </div>

                                                                                @php
                                                                                    $completionPercentage =
                                                                                        $userKpi->target_score > 0
                                                                                            ? ($userKpi->current_score /
                                                                                                    $userKpi->target_score) *
                                                                                                100
                                                                                            : 0;
                                                                                @endphp

                                                                                <div class="col-4">
                                                                                    <div class="stat-item">
                                                                                        <div
                                                                                            class="stat-value text-{{ $completionPercentage >= 80 ? 'success' : ($completionPercentage >= 60 ? 'warning' : 'danger') }}">
                                                                                            {{ round($completionPercentage, 1) }}%
                                                                                        </div>
                                                                                        <div class="stat-label">Bajarilish
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="progress mt-3" style="height: 8px;">
                                                                                <div class="progress-bar bg-{{ $completionPercentage >= 80 ? 'success' : ($completionPercentage >= 60 ? 'warning' : 'danger') }}"
                                                                                    style="width: {{ $completionPercentage }}%">
                                                                                </div>
                                                                            </div>

                                                                            <div class="mt-2">
                                                                                <span
                                                                                    class="badge badge-{{ $userKpi->status == 'completed' ? 'success' : ($userKpi->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                                                    {{ $userKpi->status_name }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="text-center text-muted">
                                                                            <i
                                                                                class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                                                            <p>Bu KPI uchun ma'lumot mavjud emas</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Tasks for task-based KPIs -->
                                                            @if ($userKpi && $userKpi->tasks->count() > 0)
                                                                <div class="tasks-section mt-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-3">
                                                                        <h6 class="mb-0"
                                                                            style="color:#fff; background:#36b9cc; padding:8px 16px; border-radius:6px; font-weight:600;">
                                                                            <i class="fa fa-list-check me-2"></i>Bajarilgan topshiriqlar
                                                                        </h6>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-info toggle-tasks-btn"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#tasks-{{ $userKpi->id }}"
                                                                            aria-expanded="true"
                                                                            aria-controls="tasks-{{ $userKpi->id }}">
                                                                            <span class="show-text">Yopish</span>
                                                                            <span class="hide-text d-none">Ko'rsatish</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="row collapse show"
                                                                        id="tasks-{{ $userKpi->id }}">
                                                                        @foreach ($userKpi->tasks as $userTask)
                                                                            <div class="col-md-6 mb-2">
                                                                                <div
                                                                                    class="task-item p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                                                                    <div>
                                                                                        <!-- Task Title -->
                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                            <div>
                                                                                                <i class="fa fa-tasks me-1 text-primary"></i>
                                                                                                <strong>{{ $userTask->name }}</strong>
                                                                                            </div>
                                                                                            <div class="text-end">
                                                                                                <!-- Task Score -->
                                                                                                <div>
                                                                                                    <i class="fa fa-star me-1 text-warning"></i>
                                                                                                    @if (!is_null($userTask->score))
                                                                                                        <span class="task-score text-success">
                                                                                                            {{ $userTask->score }}
                                                                                                        </span>
                                                                                                    @else
                                                                                                        <span class="task-score text-secondary">
                                                                                                            Tekshirilmagan
                                                                                                        </span>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <!-- Task Date -->
                                                                                                @if ($userTask->created_at)
                                                                                                    <small class="text-success">
                                                                                                        <i class="fa fa-calendar-check me-1"></i>
                                                                                                        {{ $userTask->created_at->format('d.m.Y') }}
                                                                                                    </small>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Task Description -->
                                                                                        @if ($userTask->description)
                                                                                            <div class="mt-2">
                                                                                                <i class="fa fa-align-left me-1 text-info"></i>
                                                                                                <span class="fw-bold">Tavsif:</span>
                                                                                                <span
                                                                                                    class="text-muted small d-block task-description"
                                                                                                    style="max-height: 4.5em; overflow: hidden; text-overflow: ellipsis;">
                                                                                                    {{ Str::limit($userTask->description, 200, '...') }}
                                                                                                </span>
                                                                                                @if (mb_strlen($userTask->description) > 200)
                                                                                                    <a href="#"
                                                                                                        class="show-full-desc"
                                                                                                        data-desc="{{ e($userTask->description) }}">Ko'proq...</a>
                                                                                                @endif
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    <!-- Task File -->
                                                                                    <div>
                                                                                        @if ($userTask->file_path)
                                                                                            <div class="mt-2">
                                                                                                <i class="fa fa-paperclip me-1 text-secondary"></i>
                                                                                                <a href="{{ asset('storage/' . $userTask->file_path) }}"
                                                                                                    target="_blank"
                                                                                                    class="btn btn-sm btn-outline-primary">
                                                                                                    Ilova fayl
                                                                                                </a>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    <!-- Task Feedback -->
                                                                                    @if ($userTask->task_score && $userTask->task_score->feedback)
                                                                                        <div class="mt-3">
                                                                                            <div class="p-2 rounded" style="background-color: #f0f4ff; border-left: 4px solid #4e73df;">
                                                                                                <i class="fa fa-comment-dots me-1 text-primary"></i>
                                                                                                <span class="fw-bold">Fikr-mulohaza:</span>
                                                                                                <span
                                                                                                    class="text-dark small d-block task-feedback"
                                                                                                    style="max-height: 6em; overflow: hidden; text-overflow: ellipsis;">
                                                                                                    {{ Str::limit($userTask->task_score->feedback, 300, '...') }}
                                                                                                </span>
                                                                                                @if (mb_strlen($userTask->task_score->feedback) > 300)
                                                                                                    <a href="#"
                                                                                                        class="show-full-desc"
                                                                                                        data-desc="{{ e($userTask->task_score->feedback) }}">Ko'proq...</a>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fa fa-chart-line fa-3x mb-3"></i>
                                <h5>KPI ma'lumotlari mavjud emas</h5>
                                <p>Bu foydalanuvchi uchun hali KPI belgilanmagan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trends -->
        @if ($performanceTrends->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Natijalar Tendensiyasi</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" width="400" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Chart.js -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.show-full-desc').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const desc = this.getAttribute('data-desc');

            // Remove any existing modal
            const oldModal = document.getElementById('descModal');
            if (oldModal) oldModal.remove();

            // Create modal wrapper and append it to the DOM
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div class="modal fade" id="descModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Vazifa tavsifi</h5>
                                <button type="button" class="btn-close" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p style="white-space: pre-line;">${desc}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(wrapper);

            const modalEl = document.getElementById('descModal');
            const cModal = new coreui.Modal(modalEl); // <-- use coreui.Modal here
            cModal.show();

            // Close manually (CoreUI doesn't auto-bind data-bs-dismiss)
            modalEl.querySelector('.btn-close').addEventListener('click', () => {
                cModal.hide();
            });

            modalEl.addEventListener('hidden.coreui.modal', function () {
                wrapper.remove();
            });
        });
    });
});

    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if ($performanceTrends->count() > 0)
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('performanceChart').getContext('2d');
                const trendsData = @json($performanceTrends);

                const labels = trendsData.map(item => item.month);
                const scores = trendsData.map(item => parseFloat(item.avg_score));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'O\'rtacha Ball',
                            data: scores,
                            borderColor: '#4e73df',
                            backgroundColor: '#4e73df20',
                            tension: 0.1,
                            fill: true
                        }]
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
                                text: 'So\'nggi 6 oy davomidagi natijalar'
                            }
                        }
                    }
                });
            });
        @endif
    </script>
   
@endsection
