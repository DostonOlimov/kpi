@extends('layouts.app')

@section('styles')
    <style>
        .user-dashboard {
            padding: 28px 8px 36px;
            color: #102a43;
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.32), transparent 28%),
                linear-gradient(135deg, #0f766e 0%, #0891b2 48%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 24px 70px rgba(15, 118, 110, 0.24);
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            right: -50px;
            bottom: -70px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-chip,
        .metric-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .hero-stat-grid,
        .metric-grid,
        .panel-grid {
            display: grid;
            gap: 18px;
        }

        .hero-stat-grid {
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            margin-top: 22px;
        }

        .hero-stat-card {
            border-radius: 22px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
        }

        .hero-stat-card small {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.78);
        }

        .hero-stat-card strong {
            font-size: 28px;
            line-height: 1;
        }

        .metric-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 22px;
        }

        .metric-card,
        .surface-card {
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
        }

        .metric-card {
            padding: 20px;
        }

        .metric-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .metric-icon {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
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

        .panel-grid {
            grid-template-columns: 1.45fr 1fr;
            margin-top: 22px;
        }

        .surface-card {
            padding: 22px;
        }

        .surface-card h4 {
            margin: 0;
            font-size: 20px;
            color: #102a43;
        }

        .surface-card .subtle {
            color: #627d98;
            font-size: 14px;
        }

        .diagram-wrap {
            margin-top: 18px;
            padding: 18px;
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fbff 0%, #edf6ff 100%);
        }

        .bar-list {
            margin-top: 18px;
            display: grid;
            gap: 14px;
        }

        .bar-row strong,
        .focus-row strong {
            color: #102a43;
        }

        .bar-meta,
        .focus-meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .focus-list,
        .task-list,
        .legend-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .focus-row,
        .task-row {
            padding: 16px 18px;
            border-radius: 18px;
            background: #f8fbff;
            border: 1px solid #e6eef8;
        }

        .task-title {
            color: #102a43;
            font-weight: 700;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            color: #627d98;
            font-size: 13px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .ring-chart {
            width: 220px;
            margin: 6px auto 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fbff;
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

        .empty-state {
            margin-top: 18px;
            padding: 26px 18px;
            border-radius: 18px;
            text-align: center;
            background: #f8fbff;
            color: #627d98;
            border: 1px dashed #cbd5e1;
        }

        @media (max-width: 991px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $statusTotal = max(array_sum(array_column($statusSegments, 'count')), 1);
        $circumference = 2 * pi() * 54;
        $offset = 0;

        $rates = collect($personalMonthlyData)->pluck('rate');
        $maxRate = max($rates->max() ?? 0, 100);
        $monthlyCount = max(count($personalMonthlyData), 1);
        $points = collect($personalMonthlyData)->values()->map(function ($item, $index) use ($maxRate, $monthlyCount) {
            $stepX = $monthlyCount > 1 ? 320 / ($monthlyCount - 1) : 0;
            $x = 18 + ($index * $stepX);
            $y = 150 - (($item['rate'] / $maxRate) * 118);

            return round($x, 2) . ',' . round($y, 2);
        })->implode(' ');
    @endphp

    <div class="container-fluid user-dashboard">
        <div class="hero-panel card">
            <div class="card-body p-4 p-lg-5 position-relative">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div style="max-width: 620px;">
                        <span class="hero-chip">
                            <i class="fa fa-user"></i>
                            Shaxsiy foydalanuvchi paneli
                        </span>
                        <h1 class="mt-3 mb-2" style="font-size: 34px; line-height: 1.15; font-weight: 800;">
                            {{ $user->full_name }} tizimga  xush kelibsiz!
                        </h1>
                        <p class="mb-0" style="font-size: 16px; color: rgba(255,255,255,0.82);">
                            KPI holati, topshiriqlar va oyning umumiy natijasini bitta zamonaviy panelda ko'ring.
                        </p>
                    </div>
                    <div class="metric-chip">
                        <i class="fa fa-briefcase"></i>
                        {{ $user->lavozimi ?: 'Foydalanuvchi' }}
                    </div>
                </div>

                <div class="hero-stat-grid">
                    <div class="hero-stat-card">
                        <small>Joriy oy natijasi</small>
                        <strong>{{ $scoreRate }}%</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>Bajarilgan KPI</small>
                        <strong>{{ $completedKpis }}/{{ $assignedKpis }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>Baholangan topshiriqlar</small>
                        <strong>{{ $scoredTasks }}/{{ $totalTasks }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">KPI bajarilishi</div>
                        <h3>{{ $completionRate }}%</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #1d4ed8, #38bdf8);">
                        <i class="fa fa-bullseye"></i>
                    </span>
                </div>
                <p>{{ $completedKpis }} ta KPI yopilgan, {{ $inProgressKpis }} tasi jarayonda.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($completionRate, 100) }}%; background: linear-gradient(90deg, #1d4ed8, #38bdf8);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Ball dinamikasi</div>
                        <h3>{{ rtrim(rtrim(number_format($currentScore, 1, '.', ''), '0'), '.') }}/{{ rtrim(rtrim(number_format($targetScore, 1, '.', ''), '0'), '.') }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #0f766e, #2dd4bf);">
                        <i class="fa fa-line-chart"></i>
                    </span>
                </div>
                <p>Maqsad ballga nisbatan {{ $scoreRate }}% bajarilgan.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($scoreRate, 100) }}%; background: linear-gradient(90deg, #0f766e, #2dd4bf);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Topshiriqlar</div>
                        <h3>{{ $pendingTasks }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #7c3aed, #c084fc);">
                        <i class="fa fa-check-circle"></i>
                    </span>
                </div>
                <p>{{ $pendingTasks }} ta topshiriq hali yakuniy baholanmagan.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($totalTasks > 0 ? round(($pendingTasks / $totalTasks) * 100) : 0, 100) }}%; background: linear-gradient(90deg, #7c3aed, #c084fc);"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-head">
                    <div>
                        <div class="subtle">Vazifa sifati</div>
                        <h3>{{ $averageTaskScore }}</h3>
                    </div>
                    <span class="metric-icon" style="background: linear-gradient(135deg, #ea580c, #fb923c);">
                        <i class="fa fa-star"></i>
                    </span>
                </div>
                <p>Baholangan topshiriqlar bo'yicha o'rtacha natija.</p>
                <div class="progress-rail">
                    <div class="progress-fill" style="width: {{ min($averageTaskScore, 100) }}%; background: linear-gradient(90deg, #ea580c, #fb923c);"></div>
                </div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="surface-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4>6 oylik natija diagrammasi</h4>
                        <div class="subtle">Har oy uchun maqsadga nisbatan bajarilish foizi.</div>
                    </div>
                </div>

                <div class="diagram-wrap">
                    <svg viewBox="0 0 360 180" width="100%" height="220" role="img" aria-label="6 oylik KPI diagrammasi">
                        <defs>
                            <linearGradient id="lineFill" x1="0%" x2="100%" y1="0%" y2="0%">
                                <stop offset="0%" stop-color="#0f766e" />
                                <stop offset="100%" stop-color="#1d4ed8" />
                            </linearGradient>
                        </defs>
                        <line x1="18" y1="150" x2="342" y2="150" stroke="#cbd5e1" stroke-width="1.5" />
                        <line x1="18" y1="32" x2="18" y2="150" stroke="#cbd5e1" stroke-width="1.5" />
                        <polyline points="{{ $points }}" fill="none" stroke="url(#lineFill)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        @foreach($personalMonthlyData as $index => $item)
                            @php
                                $count = max(count($personalMonthlyData), 1);
                                $stepX = $count > 1 ? 320 / ($count - 1) : 0;
                                $x = 18 + ($index * $stepX);
                                $y = 150 - (($item['rate'] / $maxRate) * 118);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="5.5" fill="#fff" stroke="#0f766e" stroke-width="3" />
                            <text x="{{ $x }}" y="170" text-anchor="middle" font-size="12" fill="#486581">{{ $item['month'] }}</text>
                            <text x="{{ $x }}" y="{{ max($y - 12, 20) }}" text-anchor="middle" font-size="11" fill="#102a43">{{ $item['rate'] }}%</text>
                        @endforeach
                    </svg>
                </div>

                <div class="bar-list">
                    @foreach($performanceBands as $band)
                        <div class="bar-row">
                            <div class="bar-meta">
                                <strong>{{ $band['label'] }}</strong>
                                <span>{{ $band['value'] }}%</span>
                            </div>
                            <div class="progress-rail">
                                <div class="progress-fill" style="width: {{ min($band['value'], 100) }}%; background: {{ $band['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="surface-card">
                <div>
                    <h4>Holatlar diagrammasi</h4>
                    <div class="subtle">Joriy oy bo'yicha KPI taqsimoti.</div>
                </div>

                <div class="ring-chart">
                    <svg viewBox="0 0 180 180" width="100%" height="220" role="img" aria-label="KPI holatlari">
                        <circle cx="90" cy="90" r="54" fill="none" stroke="#e6eef8" stroke-width="18"></circle>
                        @foreach($statusSegments as $segment)
                            @php
                                $segmentLength = ($segment['count'] / $statusTotal) * $circumference;
                            @endphp
                            <circle
                                cx="90"
                                cy="90"
                                r="54"
                                fill="none"
                                stroke="{{ $segment['color'] }}"
                                stroke-width="18"
                                stroke-linecap="round"
                                transform="rotate(-90 90 90)"
                                stroke-dasharray="{{ $segmentLength }} {{ $circumference }}"
                                stroke-dashoffset="-{{ $offset }}"
                            ></circle>
                            @php $offset += $segmentLength; @endphp
                        @endforeach
                        <text x="90" y="84" text-anchor="middle" font-size="15" fill="#627d98">KPI</text>
                        <text x="90" y="104" text-anchor="middle" font-size="26" font-weight="800" fill="#102a43">{{ $assignedKpis }}</text>
                    </svg>
                </div>

                <div class="legend-list">
                    @foreach($statusSegments as $segment)
                        <div class="legend-item">
                            <div class="legend-label">
                                <span class="dot" style="background: {{ $segment['color'] }};"></span>
                                <span>{{ $segment['label'] }}</span>
                            </div>
                            <strong>{{ $segment['count'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="surface-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4>Asosiy KPI yo'nalishlari</h4>
                        <div class="subtle">Eng muhim KPI lar bo'yicha tezkor ko'rinish.</div>
                    </div>
                </div>

                @if($focusKpis->isEmpty())
                    <div class="empty-state">
                        Hozircha sizga KPI biriktirilmagan.
                    </div>
                @else
                    <div class="focus-list">
                        @foreach($focusKpis as $item)
                            <div class="focus-row">
                                <div class="focus-meta">
                                    <strong>{{ $item['name'] }}</strong>
                                    <span>{{ $item['current_score'] }}/{{ $item['target_score'] }}</span>
                                </div>
                                <div class="progress-rail">
                                    <div class="progress-fill" style="width: {{ $item['progress'] }}%; background: linear-gradient(90deg, #0f766e, #1d4ed8);"></div>
                                </div>
                                <div class="task-meta">
                                    <span>{{ $item['status'] }}</span>
                                    <span>{{ $item['task_count'] }} ta topshiriq</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="surface-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4>So'nggi topshiriqlar</h4>
                        <div class="subtle">Yaqinda qo'shilgan vazifalar va ularning holati.</div>
                    </div>
                </div>

                @if($recentTasks->isEmpty())
                    <div class="empty-state">
                        So'nggi topshiriqlar mavjud emas.
                    </div>
                @else
                    <div class="task-list">
                        @foreach($recentTasks as $task)
                            @php
                                $status = optional($task->user_kpi)->status ?? \App\Models\UserKpi::STATUS_NEW;
                                $statusMap = [
                                    \App\Models\UserKpi::STATUS_NEW => ['label' => 'Yangi', 'bg' => 'rgba(249, 115, 22, 0.12)', 'color' => '#c2410c'],
                                    \App\Models\UserKpi::STATUS_IN_PROGRESS => ['label' => 'Jarayonda', 'bg' => 'rgba(37, 99, 235, 0.12)', 'color' => '#1d4ed8'],
                                    \App\Models\UserKpi::STATUS_COMPLETED => ['label' => 'Bajarilgan', 'bg' => 'rgba(16, 185, 129, 0.12)', 'color' => '#047857'],
                                ];
                                $badge = $statusMap[$status] ?? ['label' => 'Noma\'lum', 'bg' => '#e2e8f0', 'color' => '#475569'];
                            @endphp
                            <div class="task-row">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="task-title">{{ $task->name ?: 'Nomsiz topshiriq' }}</div>
                                        <div class="subtle mt-1">{{ optional(optional($task->user_kpi)->kpi)->name ?: 'KPI nomi mavjud emas' }}</div>
                                    </div>
                                    <span class="status-pill" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>
                                <div class="task-meta">
                                    <span><i class="fa fa-calendar me-1"></i> {{ optional($task->created_at)->format('d.m.Y H:i') ?: '-' }}</span>
                                    <span><i class="fa fa-star me-1"></i> {{ $task->score !== null ? $task->score : 'Baholanmagan' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
