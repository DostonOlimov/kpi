@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection

@section('content')
    <div class="page-header mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fa fa-user-circle mr-1"></i>&nbsp; Xodimning shaxsiy ko‘rsatkichlari
            </li>
        </ol>
    </div>
    <div class="panel panel-primary">
        <div class="tab_wrapper page-tab">
            <ul class="tab_list">
                <li>
                    <a href="{{ route('employees.list') }}">
                        <span class="visible-xs"></span>
                        <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                    </a>
                </li>
                <li class="active">
                    <span class="visible-xs"></span>
                    <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                    <b>{{ trans('app.Qo\'shish')}}</b>
                </li>
            </ul>
        </div>
    </div>
{{--    <div class="header-actions">--}}
{{--        <button class="btn btn-outline-primary" onclick="refreshDashboard()">--}}
{{--            <i class="fas fa-sync-alt"></i> Yangilash--}}
{{--        </button>--}}
{{--        <button class="btn btn-primary" onclick="exportReport()">--}}
{{--            <i class="fas fa-download"></i> Hisobot--}}
{{--        </button>--}}
{{--    </div>--}}
    <div class="dashboard-container">
        <!-- Header Section -->

        <!-- Statistics Cards -->
        <div class="stats-grid animate__animated animate__fadeInUp">
            <div class="stat-card total-kpis">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ $totalKpis }}">{{ $totalKpis }}</h3>
                    <p class="stat-label">Jami KPI</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="trend-value">+5%</span>
                </div>
            </div>

            <div class="stat-card completed-kpis">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ $completedKpis }}">{{ $completedKpis }}</h3>
                    <p class="stat-label">Bajarilgan</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="trend-value">+12%</span>
                </div>
            </div>

            <div class="stat-card current-score">
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ $totalCurrentScore }}">{{ $totalCurrentScore }}</h3>
                    <p class="stat-label">Jami Ball</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="trend-value">+8%</span>
                </div>
            </div>

            <div class="stat-card target-score">
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ $totalTargetScore }}">{{ $totalTargetScore }}</h3>
                    <p class="stat-label">Maqsad Ball</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-minus text-warning"></i>
                    <span class="trend-value">0%</span>
                </div>
            </div>
        </div>
        <!-- KPI Hierarchy Section -->
        <div class="kpi-section animate__animated animate__fadeInUp animate__delay-2s">
            <div class="section-header">
                <h3 class="section-title">Sizning KPI laringiz</h3>
                <div class="section-filters">
                    <select class="form-select" onchange="filterKPIs(this.value)">
                        <option value="all">Barcha KPI</option>
                        <option value="completed">Bajarilgan</option>
                        <option value="in-progress">Jarayonda</option>
                        <option value="not-started">Boshlanmagan</option>
                    </select>
                </div>
            </div>

            <div class="kpi-hierarchy">
                @forelse($parentKpis as $index => $parentKpi)
                    <div class="parent-kpi-container" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <!-- Parent KPI Card -->
                        <div class="parent-kpi-card">
                            <div class="parent-kpi-header">
                                <div class="parent-info">
                                    <h4 class="parent-title">
                                        <i class="fas fa-folder-open"></i>
                                        {{ $parentKpi->name }}
                                    </h4>
                                    <p class="parent-subtitle">
                                        Asosiy kategoriya • {{ $parentKpi->children->count() }} ta ichki KPI
                                    </p>
                                </div>
                                <div class="parent-stats">
                                    @php
                                        $parentTotalCurrent = 0;
                                        $parentTotalTarget = 0;
                                        $parentTotalMax = 0;
                                        foreach($parentKpi->children as $child) {
                                            if($child->user_kpis->isNotEmpty()) {
                                                $userKpi = $child->user_kpis->first();
                                                $parentTotalCurrent += $userKpi->current_score;
                                                $parentTotalTarget += $userKpi->target_score;
                                                $parentTotalMax += $child->max_score;
                                            }
                                        }
                                        $parentProgress = $parentTotalMax > 0 ? ($parentTotalCurrent / $parentTotalMax) * 100 : 0;
                                    @endphp
                                    <div class="parent-score">
                                        <span class="score-value">{{ $parentTotalCurrent }}</span>
                                        <span class="score-label">Jami ball</span>
                                    </div>
                                </div>
                            </div>

                            @if($parentTotalMax > 0)
                                <div class="parent-progress">
                                    <div class="progress-info">
                                        <span class="progress-label">Umumiy jarayon</span>
                                        <span class="progress-percentage">{{ number_format($parentProgress, 1) }}%</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="--progress: {{ $parentProgress }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Child KPIs Grid -->
                        <div class="child-kpis-grid">
                            @foreach($parentKpi->children as $childIndex => $childKpi)
                                @php
                                    $userKpi = $childKpi->user_kpis->first();
                                @endphp
                                @if($userKpi)
                                    <div class="child-kpi-card" data-aos="fade-up" data-aos-delay="{{ ($index * 100) + ($childIndex * 50) }}">
                                        <div class="kpi-card-header">
                                            <h6 class="kpi-title">
                                                <i class="fas fa-arrow-right"></i>
                                                {{ $childKpi->name }}
                                            </h6>
                                            <span class="kpi-type-badge {{ strtolower($childKpi->type) }}">
                                            {{ ucfirst($childKpi->type) }}
                                        </span>
                                        </div>

                                        <div class="kpi-progress-section">
                                            <div class="progress-header">
                                                <span class="progress-label">Jarayon</span>
                                                <span class="progress-value">{{ number_format(($userKpi->current_score / $childKpi->max_score) * 100, 1) }}%</span>
                                            </div>
                                            <div class="circular-progress" data-percentage="{{ ($userKpi->current_score / $childKpi->max_score) * 100 }}">
                                                <svg class="progress-ring" width="80" height="80">
                                                    <circle class="progress-ring-circle" cx="40" cy="40" r="35"></circle>
                                                </svg>
                                                <div class="progress-text">{{ number_format(($userKpi->current_score / $childKpi->max_score) * 100, 0) }}%</div>
                                            </div>
                                        </div>

                                        <div class="kpi-scores">
                                            <div class="score-item current">
                                                <span class="score-label">Joriy</span>
                                                <span class="score-value">{{ $userKpi->current_score }}</span>
                                            </div>
                                            <div class="score-item target">
                                                <span class="score-label">Maqsad</span>
                                                <span class="score-value">{{ $userKpi->target_score }}</span>
                                            </div>
                                            <div class="score-item max">
                                                <span class="score-label">Maksimal</span>
                                                <span class="score-value">{{ $childKpi->max_score }}</span>
                                            </div>
                                        </div>

                                        @if($userKpi->score)
                                            <div class="final-score-badge
                                            @if($userKpi->score->score >= $userKpi->target_score) success
                                            @elseif($userKpi->score->score >= $userKpi->target_score * 0.8) warning
                                            @else danger @endif">
                                                <i class="fas fa-trophy"></i>
                                                Yakuniy ball: {{ $userKpi->score->score }}
                                            </div>
                                        @endif

                                        @if($userKpi->score && $userKpi->score->feedback)
                                            <div class="feedback-preview">
                                                <i class="fas fa-comment"></i>
                                                <span>{{ Str::limit($userKpi->score->feedback, 60) }}</span>
                                            </div>
                                        @endif

                                        <button class="btn-detail" onclick="showKpiDetail({{ json_encode($userKpi) }})">
                                            <i class="fas fa-eye"></i>
                                            Batafsil Ko'rish
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="empty-title">KPI topilmadi</h4>
                        <p class="empty-description">Sizga hali KPI tayinlanmagan.</p>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            KPI Qo'shish
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
{{--        <!-- Charts Section -->--}}
{{--        <div class="charts-section animate__animated animate__fadeInUp animate__delay-1s">--}}
{{--            <div class="chart-card">--}}
{{--                <div class="chart-header">--}}
{{--                    <h5 class="chart-title">--}}
{{--                        <i class="fas fa-chart-pie"></i>--}}
{{--                        KPI Jarayoni Umumiy Ko'rinishi--}}
{{--                    </h5>--}}
{{--                    <div class="chart-actions">--}}
{{--                        <button class="btn-icon" onclick="toggleChartType('progress')">--}}
{{--                            <i class="fas fa-expand-arrows-alt"></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="chart-body">--}}
{{--                    <div class="chart-container">--}}
{{--                        <canvas id="progressChart"></canvas>--}}
{{--                    </div>--}}
{{--                    <div class="chart-legend">--}}
{{--                        <div class="legend-item">--}}
{{--                            <span class="legend-color bg-success"></span>--}}
{{--                            <span class="legend-text">Bajarilgan ({{ $completedKpis }})</span>--}}
{{--                        </div>--}}
{{--                        <div class="legend-item">--}}
{{--                            <span class="legend-color bg-warning"></span>--}}
{{--                            <span class="legend-text">Jarayonda ({{ $totalKpis - $completedKpis }})</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="chart-card">--}}
{{--                <div class="chart-header">--}}
{{--                    <h5 class="chart-title">--}}
{{--                        <i class="fas fa-chart-bar"></i>--}}
{{--                        Ball va Maqsad Taqqoslash--}}
{{--                    </h5>--}}
{{--                    <div class="chart-actions">--}}
{{--                        <button class="btn-icon" onclick="toggleChartType('comparison')">--}}
{{--                            <i class="fas fa-expand-arrows-alt"></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="chart-body">--}}
{{--                    <div class="chart-container">--}}
{{--                        <canvas id="comparisonChart"></canvas>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--    </div>--}}

    <!-- Enhanced KPI Detail Modal -->
    <div class="modal fade" id="kpiDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chart-line"></i>
                        KPI Tafsilotlari
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="kpiDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Ma'lumotlar yuklanmoqda...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/kpi/dashboard.js') }}"></script>
@endpush
