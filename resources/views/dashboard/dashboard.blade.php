@extends('layouts.app')

@section('styles')
    <style>
        .modern-dashboard {
            padding: 28px 8px 36px;
            color: #102a43;
        }

        .hero-card {
            border: none;
            border-radius: 30px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.28), transparent 28%),
                linear-gradient(135deg, #0f766e 0%, #0b7fab 45%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 28px 75px rgba(15, 118, 110, 0.22);
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 13px;
            font-weight: 700;
        }

        .hero-stat-grid,
        .metric-grid,
        .two-column-grid {
            display: grid;
            gap: 18px;
        }

        .hero-stat-grid {
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            margin-top: 24px;
        }

        .hero-stat-card {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
        }

        .hero-stat-card small {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
        }

        .hero-stat-card strong {
            font-size: 28px;
            line-height: 1;
        }

        .metric-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 22px;
        }

        .surface-card,
        .metric-card {
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
        }

        .metric-card {
            padding: 20px;
        }

        .metric-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .metric-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        .metric-card h3 {
            margin: 0;
            font-size: 30px;
            color: #0f172a;
        }

        .metric-card p {
            margin: 6px 0 0;
            color: #486581;
        }

        .subtle {
            color: #627d98;
            font-size: 14px;
        }

        .progress-rail {
            width: 100%;
            height: 10px;
            margin-top: 16px;
            border-radius: 999px;
            background: #e6eef8;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: inherit;
        }

        .two-column-grid {
            grid-template-columns: 1.2fr 0.8fr;
            margin-top: 22px;
        }

        .surface-card {
            padding: 22px;
        }

        .surface-card h4 {
            margin: 0;
            color: #102a43;
            font-size: 20px;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .section-card,
        .task-row,
        .legend-item {
            padding: 18px;
            border-radius: 18px;
            background: #f8fbff;
            border: 1px solid #e6eef8;
        }

        .section-card h5 {
            margin: 0 0 12px;
            color: #102a43;
            font-size: 17px;
        }

        .section-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .section-stat {
            padding: 10px 12px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e6eef8;
        }

        .section-stat strong {
            display: block;
            color: #102a43;
        }

        .section-stat span {
            color: #627d98;
            font-size: 12px;
        }

        .legend-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .legend-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        @media (max-width: 991px) {
            .two-column-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $statusLegend = [
            ['label' => 'Yangi', 'count' => $kpiStatusData['new'], 'color' => '#f97316'],
            ['label' => 'Jarayonda', 'count' => $kpiStatusData['in_progress'], 'color' => '#2563eb'],
            ['label' => 'Bajarilgan', 'count' => $kpiStatusData['completed'], 'color' => '#10b981'],
        ];
    @endphp

    <div class="container-fluid modern-dashboard">
        <div class="hero-card card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div style="max-width: 700px;">
                        <span class="hero-chip">
                            <i class="fa fa-chart-pie"></i>
                            Respublika KPI boshqaruvi
                        </span>
                        <h1 class="mt-3 mb-2" style="font-size: 34px; line-height: 1.15; font-weight: 800;">
                            KPI natijalari va hududiy ko'rinish
                        </h1>
                        <p class="mb-0" style="font-size: 16px; color: rgba(255,255,255,0.82);">
                            Xodimlar, KPI holatlari, fokus section natijalari va O'zbekiston hududlari bo'yicha interaktiv ko'rinish bitta sahifada.
                        </p>
                    </div>
                    <span class="hero-chip">
                        <i class="fa fa-user-circle"></i>
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </span>
                </div>

                <div class="hero-stat-grid">
                    <div class="hero-stat-card">
                        <small>Jami xodimlar</small>
                        <strong>{{ number_format($totalEmployees) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>KPI bajarilishi</small>
                        <strong>{{ $completionPercentage }}%</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>Eng faol hudud</small>
                        <strong>{{ $topRegion['name'] ?? 'Ma\'lumot yo\'q' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Xodimlar soni</div>
                        <h3>{{ number_format($totalEmployees) }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #1d4ed8, #38bdf8);">
                        <i class="fa fa-users"></i>
                    </span>
                </div>
                <p>Faol hududlar va bo'limlar kesimida ishlayotgan xodimlar.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: 100%; background: linear-gradient(90deg, #1d4ed8, #38bdf8);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Jami topshiriqlar</div>
                        <h3>{{ number_format($totalTasks) }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #0f766e, #2dd4bf);">
                        <i class="fa fa-list-check"></i>
                    </span>
                </div>
                <p>So'nggi monitoring bo'yicha tizimdagi barcha topshiriqlar.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($totalTasks > 0 ? 100 : 0, 100) }}%; background: linear-gradient(90deg, #0f766e, #2dd4bf);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Bajarilgan KPI</div>
                        <h3>{{ $completedKpis }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #7c3aed, #c084fc);">
                        <i class="fa fa-check-circle"></i>
                    </span>
                </div>
                <p>Joriy oy bo'yicha yakunlangan KPI yozuvlari.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($completionPercentage, 100) }}%; background: linear-gradient(90deg, #7c3aed, #c084fc);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Fokus hudud natijasi</div>
                        <h3>{{ $topRegion ? $topRegion['progress'] : 0 }}%</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #ea580c, #fb923c);">
                        <i class="fa fa-map-marker"></i>
                    </span>
                </div>
                <p>{{ $topRegion['name'] ?? 'Ma\'lumot yo\'q' }} hududida joriy natija ko'rsatkichi.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($topRegion['progress'] ?? 0, 100) }}%; background: linear-gradient(90deg, #ea580c, #fb923c);"></div>
                </div>
            </div>
        </div>

        <div class="two-column-grid">
            <div class="surface-card">
                <div>
                    <h4>Fokus section results</h4>
                    <div class="subtle">Eng yuqori natija ko'rsatayotgan hududlar va sectionlar bo'yicha umumiy ko'rinish.</div>
                </div>

                <div class="section-grid">
                    @foreach($focusSections as $section)
                        <div class="section-card">
                            <h5>{{ $section['name'] }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="subtle">Natija</span>
                                <strong>{{ $section['progress'] }}%</strong>
                            </div>
                            <div class="progress-rail">
                                <div class="progress-fill" style="width: {{ min($section['progress'], 100) }}%; background: linear-gradient(90deg, #0f766e, #1d4ed8);"></div>
                            </div>

                            <div class="section-stats">
                                <div class="section-stat">
                                    <strong>{{ $section['employee_count'] }}</strong>
                                    <span>Xodim</span>
                                </div>
                                <div class="section-stat">
                                    <strong>{{ $section['section_count'] }}</strong>
                                    <span>Section</span>
                                </div>
                                <div class="section-stat">
                                    <strong>{{ $section['completed_kpis'] }}/{{ $section['total_kpis'] }}</strong>
                                    <span>KPI</span>
                                </div>
                                <div class="section-stat">
                                    <strong>{{ $section['task_count'] }}</strong>
                                    <span>Topshiriq</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="surface-card">
                <div>
                    <h4>KPI holati</h4>
                    <div class="subtle">Tizim bo'yicha joriy KPI taqsimoti.</div>
                </div>

                <div class="legend-list">
                    @foreach($statusLegend as $item)
                        <div class="legend-item">
                            <div class="legend-label">
                                <span class="dot" style="background: {{ $item['color'] }};"></span>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <strong>{{ $item['count'] }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <canvas id="kpiStatusChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="surface-card mt-4">
            <div>
                <div>
                    <h4>KPI 6 oylik trendi</h4>
                    <div class="subtle">Oxirgi 6 oyda jami KPI va bajarilgan KPI ko'rsatkichi.</div>
                </div>
                <div class="mt-4">
                    <canvas id="kpiProgressChart" height="280"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyLabels = [@foreach($monthlyData as $data) '{{ $data['month'] }}', @endforeach];
            const completedData = [@foreach($monthlyData as $data) {{ $data['completed'] }}, @endforeach];
            const totalData = [@foreach($monthlyData as $data) {{ $data['total'] }}, @endforeach];

            new Chart(document.getElementById('kpiProgressChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Bajarilgan KPI',
                        data: completedData,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.12)',
                        tension: 0.35,
                        fill: true
                    }, {
                        label: 'Jami KPI',
                        data: totalData,
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.08)',
                        tension: 0.35,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
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

            new Chart(document.getElementById('kpiStatusChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Yangi', 'Jarayonda', 'Bajarilgan'],
                    datasets: [{
                        data: [{{ $kpiStatusData['new'] }}, {{ $kpiStatusData['in_progress'] }}, {{ $kpiStatusData['completed'] }}],
                        backgroundColor: ['#f97316', '#2563eb', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

        });
    </script>
@endsection
