@extends('layouts.app')

@section('content')
    <div class="fade-in">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold text-dark mb-2">Ishlash Ko'rsatkichlari Paneli</h1>
                <p class="text-muted">KPI jarayonlaringizni kuzatib boring va yutuqlaringizni ko'ring</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="fa fa-tasks fa-2x mb-3"></i>
                        <h3 class="fw-bold">{{ $totalKpis }}</h3>
                        <p class="mb-0">Jami KPI</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="fa fa-check-circle fa-2x mb-3"></i>
                        <h3 class="fw-bold">{{ $completedKpis }}</h3>
                        <p class="mb-0">Bajarilgan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="fa fa-chart-bar fa-2x mb-3"></i>
                        <h3 class="fw-bold">{{ $totalCurrentScore }}</h3>
                        <p class="mb-0">Jami Ball</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="fa fa-target fa-2x mb-3"></i>
                        <h3 class="fw-bold">{{ $totalTargetScore }}</h3>
                        <p class="mb-0">Maqsad Ball</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-chart-pie text-primary me-2"></i>KPI Jarayoni Umumiy Ko'rinishi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="progressChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-chart-bar text-primary me-2"></i>Ball va Maqsad Taqqoslash
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Hierarchy -->
        <div class="row">
            <div class="col-12 mb-3">
                <h3 class="fw-bold text-dark">Sizning KPI laringiz</h3>
            </div>

            @forelse($parentKpis as $parentKpi)
                <div class="col-12 mb-4">
                    <!-- Parent KPI Card -->
                    <div class="card parent-kpi-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="parent-title mb-1">
                                        <i class="fa fa-folder-open me-2"></i>{{ $parentKpi->name }}
                                    </h4>
                                    <small class="text-muted">Asosiy kategoriya - {{ $parentKpi->children->count() }} ta ichki KPI</small>
                                </div>
                                <div class="text-end">
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
                                    <div class="text-success fw-bold fs-4">{{ $parentTotalCurrent }}</div>
                                    <small class="text-muted">Jami ball</small>
                                </div>
                            </div>

                            @if($parentTotalMax > 0)
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Umumiy jarayon</small>
                                        <small class="fw-bold">{{ number_format($parentProgress, 1) }}%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $parentProgress }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Child KPIs -->
                    @foreach($parentKpi->children as $childKpi)
                        @php
                            $userKpi = $childKpi->user_kpis->first();
                        @endphp
                        @if($userKpi)
                            <div class="child-kpi-card">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title fw-bold text-dark mb-1">
                                                <i class="fa fa-arrow-right text-primary me-2"></i>{{ $childKpi->name }}
                                            </h6>
                                            <span class="badge bg-secondary">{{ ucfirst($childKpi->type) }}</span>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Jarayon</span>
                                                <span class="fw-bold">{{ number_format(($userKpi->current_score / $childKpi->max_score) * 100, 1) }}%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ ($userKpi->current_score / $childKpi->max_score) * 100 }}%"></div>
                                            </div>
                                        </div>

                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="text-muted small">Joriy</div>
                                                <div class="fw-bold text-primary">{{ $userKpi->current_score }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-muted small">Maqsad</div>
                                                <div class="fw-bold text-warning">{{ $userKpi->target_score }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-muted small">Maksimal</div>
                                                <div class="fw-bold text-success">{{ $childKpi->max_score }}</div>
                                            </div>
                                        </div>

                                        @if($userKpi->score)
                                            <div class="mb-3">
                                <span class="score-badge
                                    @if($userKpi->score->score >= $userKpi->target_score) bg-success
                                    @elseif($userKpi->score->score >= $userKpi->target_score * 0.8) bg-warning
                                    @else bg-danger @endif text-white">
                                    Yakuniy ball: {{ $userKpi->score->score }}
                                </span>
                                            </div>
                                        @endif

                                        @if($userKpi->score && $userKpi->score->feedback)
                                            <div class="alert alert-info small mb-3">
                                                <i class="fa fa-comment me-2"></i>{{ Str::limit($userKpi->score->feedback, 100) }}
                                            </div>
                                        @endif

                                        <button class="btn btn-primary btn-sm w-100" onclick="showKpiDetail({{ json_encode($userKpi) }})">
                                            <i class="fa fa-eye me-2"></i>Batafsil Ko'rish
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <i class="fa fa-chart-line fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">KPI topilmadi</h4>
                            <p class="text-muted">Sizga hali KPI tayinlanmagan.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- KPI Detail Modal -->
    <div class="modal fade" id="kpiDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">KPI Tafsilotlari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="kpiDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Chart.js configurations
        const chartColors = {
            primary: '#4f46e5',
            secondary: '#6366f1',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6'
        };

        // Progress Overview Chart
        const progressCtx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(progressCtx, {
            type: 'doughnut',
            data: {
                labels: ['Bajarilgan', 'Jarayonda', 'Boshlanmagan'],
                datasets: [{
                    data: [{{ $completedKpis }}, {{ $totalKpis - $completedKpis }}, 0],
                    backgroundColor: [chartColors.success, chartColors.warning, chartColors.danger],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Score vs Target Chart
        const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
        const comparisonChart = new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Joriy Ball', 'Maqsad Ball', 'Maksimal Ball'],
                datasets: [{
                    label: 'Ball',
                    data: [{{ $totalCurrentScore }}, {{ $totalTargetScore }}, {{ $totalMaxScore }}],
                    backgroundColor: [chartColors.primary, chartColors.warning, chartColors.success],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Show KPI Detail Modal
        function showKpiDetail(userKpi) {
            const modal = new bootstrap.Modal(document.getElementById('kpiDetailModal'));
            const content = document.getElementById('kpiDetailContent');

            const progressPercentage = (userKpi.current_score / userKpi.kpi.max_score * 100).toFixed(1);
            const targetPercentage = (userKpi.target_score / userKpi.kpi.max_score * 100).toFixed(1);

            content.innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <h4 class="fw-bold text-primary">${userKpi.kpi.name}</h4>
                <p class="text-muted mb-3">Turi: <span class="badge bg-secondary">${userKpi.kpi.type.charAt(0).toUpperCase() + userKpi.kpi.type.slice(1)}</span></p>

                <div class="mb-4">
                    <h6 class="fw-bold">Jarayon Umumiy Ko'rinishi</h6>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-primary" style="width: ${progressPercentage}%"></div>
                    </div>
                    <small class="text-muted">Maksimal ballning ${progressPercentage}% i erishildi</small>
                </div>

                ${userKpi.score && userKpi.score.feedback ? `
                <div class="mb-4">
                    <h6 class="fw-bold">Fikr-mulohaza</h6>
                    <div class="alert alert-info">
                        <i class="fa fa-comment me-2"></i>${userKpi.score.feedback}
                    </div>
                </div>
                ` : ''}
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Ball Taqsimoti</h6>
                        <div class="mb-3">
                            <div class="h4 text-primary">${userKpi.current_score}</div>
                            <small class="text-muted">Joriy Ball</small>
                        </div>
                        <div class="mb-3">
                            <div class="h4 text-warning">${userKpi.target_score}</div>
                            <small class="text-muted">Maqsad Ball</small>
                        </div>
                        <div class="mb-3">
                            <div class="h4 text-success">${userKpi.kpi.max_score}</div>
                            <small class="text-muted">Maksimal Ball</small>
                        </div>
                        ${userKpi.score ? `
                        <div class="mb-3">
                            <div class="h4 text-info">${userKpi.score.score}</div>
                            <small class="text-muted">Yakuniy Ball</small>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;

            modal.show();
        }

        // Add smooth scrolling and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
@endpush
