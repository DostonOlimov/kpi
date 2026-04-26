@extends('layouts.app')

<style>
:root {
    --green-primary:   #10b981;
    --green-dark:      #059669;
    --green-light:     #d1fae5;
    --green-lighter:   #ecfdf5;
    --blue-accent:     #3b82f6;
    --purple-accent:   #6366f1;
    --text-primary:    #1e293b;
    --text-secondary:  #64748b;
    --text-muted:      #94a3b8;
    --border-color:    #e2e8f0;
    --bg-page:         #f1f5f9;
    --bg-card:         #ffffff;
    --shadow-sm:       0 1px 3px rgba(0,0,0,.08);
    --shadow-md:       0 4px 12px rgba(0,0,0,.10);
    --shadow-lg:       0 8px 24px rgba(0,0,0,.12);
    --radius-md:       10px;
    --radius-lg:       14px;
    --radius-xl:       20px;
    --transition:      .25s cubic-bezier(.4,0,.2,1);
}

/* ── Page wrapper ── */
.skp-wrapper {
    background: var(--bg-page);
    min-height: 100vh;
    padding: 24px 0 48px;
}

/* ── Hero banner ── */
.skp-hero {
    background: linear-gradient(135deg, var(--green-primary) 0%, #0ea5e9 100%);
    border-radius: var(--radius-xl);
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(16,185,129,.30);
    position: relative;
    overflow: hidden;
}
.skp-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.skp-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -20px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.skp-hero .hero-title {
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: -.3px;
    margin-bottom: 4px;
    position: relative;
    z-index: 1;
}
.skp-hero .hero-subtitle {
    font-size: .875rem;
    opacity: .85;
    position: relative;
    z-index: 1;
}
.skp-hero .hero-score-box {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: var(--radius-md);
    padding: 12px 20px;
    text-align: center;
    backdrop-filter: blur(8px);
    position: relative;
    z-index: 1;
}
.skp-hero .hero-score-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    opacity: .8;
    margin-bottom: 2px;
}
.skp-hero .hero-score-value {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
}
.skp-hero .hero-progress-track {
    height: 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.25);
    overflow: hidden;
    margin-top: 6px;
}
.skp-hero .hero-progress-fill {
    height: 100%;
    border-radius: 999px;
    background: #fff;
    transition: width .5s ease;
}
.skp-hero .hero-pct-label {
    font-size: .8rem;
    font-weight: 700;
    margin-top: 3px;
    opacity: .95;
    text-align: right;
}

/* ── Stat chips in hero ── */
.skp-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px;
    padding: 5px 12px;
    font-size: .78rem;
    font-weight: 600;
    color: #fff;
    position: relative;
    z-index: 1;
}
.skp-chip i { font-size: .7rem; }

/* ── User info card ── */
.skp-user-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    padding: 18px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}
.skp-user-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green-primary), #0ea5e9);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.skp-user-info .name {
    font-size: 1rem; font-weight: 700; color: var(--text-primary);
    margin-bottom: 2px;
}
.skp-user-meta {
    display: flex; flex-wrap: wrap; gap: 10px;
}
.skp-user-meta span {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .78rem; color: var(--text-secondary);
}
.skp-user-meta i { color: var(--green-primary); }

/* ── KPI item card ── */
.skp-kpi-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    border: 1.5px solid var(--border-color);
    padding: 0;
    margin-bottom: 12px;
    overflow: hidden;
    transition: box-shadow var(--transition), border-color var(--transition), transform var(--transition);
}
.skp-kpi-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}
.skp-kpi-card.is-scored {
    border-left: 4px solid var(--green-primary);
    border-color: var(--green-primary);
}
.skp-kpi-card.not-scored {
    border-left: 4px solid var(--border-color);
}
.skp-kpi-card.is-scored .skp-kpi-top {
    background: var(--green-lighter);
}

.skp-kpi-top {
    display: flex; align-items: center;
    gap: 14px; padding: 16px 20px;
    background: #fff;
    flex-wrap: wrap;
}
.skp-kpi-index {
    width: 32px; height: 32px; min-width: 32px;
    border-radius: 50%;
    background: var(--bg-page);
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700;
    color: var(--text-secondary);
    border: 1.5px solid var(--border-color);
}
.skp-kpi-name-group { flex: 1 1 200px; }
.skp-kpi-name {
    font-size: .935rem; font-weight: 650;
    color: var(--text-primary); margin-bottom: 4px;
}

/* status badges */
.skp-badge {
    display: inline-block;
    padding: 2px 10px; border-radius: 999px;
    font-size: .72rem; font-weight: 600; letter-spacing: .03em;
}
.skp-badge.new      { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.skp-badge.progress { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.skp-badge.done     { background: var(--green-light); color: var(--green-dark); border: 1px solid #6ee7b7; }
.skp-badge.unknown  { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

/* inline progress */
.skp-mini-progress { flex: 1 1 140px; min-width: 120px; }
.skp-mini-progress .track {
    height: 8px; border-radius: 999px;
    background: var(--bg-page);
    overflow: hidden;
    margin-bottom: 3px;
}
.skp-mini-progress .fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, var(--green-primary), #0ea5e9);
    transition: width .4s ease;
}
.skp-mini-progress .labels {
    display: flex; justify-content: space-between;
    font-size: .7rem; color: var(--text-muted);
}

/* score input group */
.skp-score-group {
    flex: 0 0 auto;
    display: flex; align-items: center; gap: 8px;
}
.skp-score-input-wrap {
    position: relative; display: flex; align-items: center;
}
.skp-score-input-wrap input[type="number"] {
    width: 86px;
    height: 40px;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0 34px 0 12px;
    font-size: .92rem; font-weight: 600; color: var(--text-primary);
    background: var(--bg-card);
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
    -moz-appearance: textfield;
}
.skp-score-input-wrap input[type="number"]::-webkit-inner-spin-button,
.skp-score-input-wrap input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }
.skp-score-input-wrap input[type="number"]:focus {
    border-color: var(--green-primary);
    box-shadow: 0 0 0 3px rgba(16,185,129,.15);
}
.skp-score-input-wrap input[type="number"].input-valid {
    border-color: var(--green-primary);
    background: var(--green-lighter);
}
.skp-score-max-label {
    position: absolute; right: 8px;
    font-size: .7rem; color: var(--text-muted);
    pointer-events: none;
}

.skp-comment-btn {
    height: 40px; width: 40px;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-card);
    color: var(--text-secondary);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: .85rem;
    transition: all var(--transition);
}
.skp-comment-btn:hover {
    border-color: var(--blue-accent);
    color: var(--blue-accent);
    background: #eff6ff;
}
.skp-comment-btn.active {
    border-color: var(--blue-accent);
    color: #fff;
    background: var(--blue-accent);
}

/* feedback area */
.skp-feedback-area {
    border-top: 1px solid var(--border-color);
    padding: 14px 20px;
    background: #f8fafc;
}
.skp-feedback-area textarea {
    width: 100%;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 10px 14px;
    font-size: .85rem;
    color: var(--text-primary);
    resize: vertical;
    outline: none;
    background: #fff;
    transition: border-color var(--transition);
    min-height: 64px;
}
.skp-feedback-area textarea:focus {
    border-color: var(--blue-accent);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

/* ── Floating action bar ── */
.skp-action-bar {
    position: sticky; bottom: 20px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    padding: 14px 24px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
    backdrop-filter: blur(8px);
    z-index: 100;
    margin-top: 20px;
}
.skp-action-bar .save-info {
    display: flex; align-items: center; gap: 8px;
    font-size: .82rem; color: var(--text-secondary);
}
.skp-action-bar .save-info strong { color: var(--text-primary); }
.skp-btn {
    display: inline-flex; align-items: center; gap: 7px;
    height: 42px; padding: 0 22px;
    border-radius: var(--radius-md);
    font-size: .875rem; font-weight: 600;
    border: none; cursor: pointer;
    transition: all var(--transition);
    text-decoration: none;
}
.skp-btn-back {
    background: var(--bg-page);
    color: var(--text-secondary);
    border: 1.5px solid var(--border-color);
}
.skp-btn-back:hover {
    background: #e2e8f0; color: var(--text-primary);
    text-decoration: none;
}
.skp-btn-save {
    background: linear-gradient(135deg, var(--green-primary), var(--green-dark));
    color: #fff;
    box-shadow: 0 4px 12px rgba(16,185,129,.35);
}
.skp-btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(16,185,129,.45);
    color: #fff;
}

/* ── Empty state ── */
.skp-empty {
    text-align: center; padding: 56px 24px;
    color: var(--text-muted);
}
.skp-empty .icon { font-size: 3rem; margin-bottom: 12px; opacity: .5; }
.skp-empty p { font-size: .95rem; }

/* ── Alert ── */
.skp-alert-success {
    background: var(--green-lighter);
    border: 1px solid #6ee7b7;
    border-radius: var(--radius-md);
    padding: 12px 18px;
    display: flex; align-items: center; gap: 10px;
    font-size: .875rem; color: var(--green-dark);
    margin-bottom: 20px;
}
</style>

@section('content')
<div class="skp-wrapper">
<div class="container-fluid px-3 px-md-4">

    <!-- Breadcrumb -->
    <div class="page-header mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('kpis.user-kpis-dashboard') }}"><i class="fe fe-users mr-1"></i> Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kpis.user-kpis-detail', $user->id) }}">{{ $user->full_name }}</a>
            </li>
            <li class="breadcrumb-item active">{{ $parentKpi->name }}</li>
        </ol>
    </div>

    <!-- Hero Banner -->
    <div class="skp-hero">
        <div class="row align-items-center">
            <div class="col-md-5 mb-3 mb-md-0">
                <div class="hero-title">
                    <i class="fa fa-bar-chart mr-2"></i>{{ $parentKpi->name }}
                </div>
                <div class="hero-subtitle mb-3">KPI baholash sahifasi</div>
                <div class="d-flex flex-wrap gap-2" style="gap:8px; display:flex!important;">
                    <span class="skp-chip">
                        <i class="fa fa-list-ol"></i>
                        {{ $userKpis->count() }} ta KPI
                    </span>
                    <span class="skp-chip">
                        <i class="fa fa-check-circle"></i>
                        {{ $userKpis->where('status','completed')->count() }} bajarildi
                    </span>
                    <span class="skp-chip">
                        <i class="fa fa-clock-o"></i>
                        {{ $userKpis->where('status','in_progress')->count() }} jarayonda
                    </span>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div style="position:relative;z-index:1;">
                    <div class="d-flex justify-content-between mb-1">
                        <small style="opacity:.85;">Umumiy progress</small>
                        <small style="font-weight:700;" id="main-progress-pct">{{ $percentage }}%</small>
                    </div>
                    <div class="hero-progress-track">
                        <div class="hero-progress-fill" id="main-progress-bar" style="width:{{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row" style="position:relative;z-index:1;gap:8px;margin:0;">
                    <div class="col hero-score-box p-2 mr-1" style="border-radius:10px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);text-align:center;">
                        <div class="hero-score-label">Joriy ball</div>
                        <div class="hero-score-value" id="live-score-display">{{ $totalCurrent }}</div>
                    </div>
                    <div class="col hero-score-box p-2" style="border-radius:10px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);text-align:center;">
                        <div class="hero-score-label">Maqsad</div>
                        <div class="hero-score-value">{{ $totalTarget }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="skp-user-card">
        <div class="skp-user-avatar">
            {{ mb_strtoupper(mb_substr($user->first_name ?? 'U', 0, 1, 'UTF-8')) }}
        </div>
        <div class="skp-user-info">
            <div class="name">{{ $user->full_name }}</div>
            <div class="skp-user-meta">
                <span><i class="fa fa-briefcase"></i> {{ $user->lavozimi ?? '-' }}</span>
                <span><i class="fa fa-map-marker"></i> {{ $user->work_zone->name ?? '-' }}</span>
                <span><i class="fa fa-tag"></i> {{ $user->role->name ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Flash message -->
    @if(session('message'))
        <div class="skp-alert-success">
            <i class="fa fa-check-circle"></i> {{ session('message') }}
        </div>
    @endif

    <!-- KPI Cards -->
    @if($userKpis->isEmpty())
        <div class="skp-empty">
            <div class="icon"><i class="fa fa-inbox"></i></div>
            <p>Bu kategoriya uchun KPI lar topilmadi</p>
        </div>
    @else
        <form method="POST" action="{{ route('kpis.store-kpi-type-scores', [$user->id, $parentKpi->id]) }}" id="kpi-score-form">
            @csrf

            @foreach($userKpis as $index => $userKpi)
                @php
                    $isScored  = !is_null($userKpi->current_score) && $userKpi->current_score > 0;
                    $pct       = $userKpi->target_score > 0
                                    ? round(($userKpi->current_score / $userKpi->target_score) * 100)
                                    : 0;
                    $hasFeedback = $userKpi->score?->feedback;
                @endphp
                <div class="skp-kpi-card {{ $isScored ? 'is-scored' : 'not-scored' }}" id="card-{{ $userKpi->id }}">
                    <div class="skp-kpi-top">

                        <!-- Index bubble -->
                        <div class="skp-kpi-index">{{ $index + 1 }}</div>

                        <!-- Name + status -->
                        <div class="skp-kpi-name-group">
                            <div class="skp-kpi-name">{{ $userKpi->kpi->name }}</div>
                            @if($userKpi->status == 'new')
                                <span class="skp-badge new"><i class="fa fa-circle" style="font-size:.5rem;vertical-align:middle;"></i> Yangi</span>
                            @elseif($userKpi->status == 'in_progress')
                                <span class="skp-badge progress"><i class="fa fa-circle" style="font-size:.5rem;vertical-align:middle;"></i> Jarayonda</span>
                            @elseif($userKpi->status == 'completed')
                                <span class="skp-badge done"><i class="fa fa-check" style="font-size:.6rem;vertical-align:middle;"></i> Bajarildi</span>
                            @else
                                <span class="skp-badge unknown">Noma'lum</span>
                            @endif
                        </div>

                        <!-- Mini progress -->
                        <div class="skp-mini-progress">
                            <div class="track">
                                <div class="fill" id="progress-bar-{{ $userKpi->id }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="labels">
                                <span>{{ $userKpi->current_score ?? 0 }} ball</span>
                                <span id="progress-pct-{{ $userKpi->id }}">{{ $pct }}%</span>
                            </div>
                        </div>

                        <!-- Score input -->
                        <div class="skp-score-group">
                            <div class="skp-score-input-wrap">
                                <input
                                    type="number"
                                    name="scores[{{ $userKpi->id }}]"
                                    class="kpi-score-field {{ $isScored ? 'input-valid' : '' }}"
                                    value="{{ $userKpi->current_score ?? '' }}"
                                    min="0"
                                    max="{{ $userKpi->target_score }}"
                                    placeholder="0"
                                    data-kpi-id="{{ $userKpi->id }}"
                                    data-target="{{ $userKpi->target_score }}"
                                >
                                <span class="skp-score-max-label">/{{ $userKpi->target_score }}</span>
                            </div>

                            <!-- Comment toggle btn -->
                            <button type="button"
                                    class="skp-comment-btn {{ $hasFeedback ? 'active' : '' }}"
                                    id="comment-btn-{{ $userKpi->id }}"
                                    onclick="toggleFeedback({{ $userKpi->id }})"
                                    title="Izoh">
                                <i class="fa fa-comment"></i>
                            </button>
                        </div>

                    </div>

                    <!-- Feedback -->
                    <div class="skp-feedback-area" id="feedback-{{ $userKpi->id }}" style="{{ $hasFeedback ? '' : 'display:none;' }}">
                        <textarea
                            name="feedbacks[{{ $userKpi->id }}]"
                            rows="2"
                            placeholder="Izoh qoldiring (ixtiyoriy)...">{{ $hasFeedback ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach

            <!-- Floating action bar -->
            <div class="skp-action-bar">
                <div class="save-info">
                    <i class="fa fa-info-circle text-muted"></i>
                    <span>Jami: <strong id="action-bar-score">{{ $totalCurrent }}</strong> / {{ $totalTarget }} ball</span>
                </div>
                <div class="d-flex" style="gap:10px;">
                    <a href="{{ route('kpis.user-kpis-detail', $user->id) }}" class="skp-btn skp-btn-back">
                        <i class="fa fa-arrow-left"></i> Ortga
                    </a>
                    <button type="submit" class="skp-btn skp-btn-save">
                        <i class="fa fa-save"></i> Saqlash
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFeedback(id) {
        const area = document.getElementById('feedback-' + id);
        const btn  = document.getElementById('comment-btn-' + id);
        const open = area.style.display !== 'none';
        area.style.display = open ? 'none' : '';
        btn.classList.toggle('active', !open);
    }

    document.querySelectorAll('.kpi-score-field').forEach(function (input) {
        input.addEventListener('input', function () {
            const kpiId  = this.dataset.kpiId;
            const target = parseFloat(this.dataset.target) || 0;
            let   val    = parseFloat(this.value);
            if (isNaN(val)) val = 0;

            // Clamp
            if (val > target) { this.value = val = target; }
            if (val < 0)      { this.value = val = 0; }

            // Update card border class
            const card = document.getElementById('card-' + kpiId);
            if (card) {
                card.classList.toggle('is-scored',  val > 0);
                card.classList.toggle('not-scored', val <= 0);
            }

            // Valid class on input
            this.classList.toggle('input-valid', val > 0);

            // Update individual bar
            const pct = target > 0 ? Math.round((val / target) * 100) : 0;
            const bar = document.getElementById('progress-bar-' + kpiId);
            const pctLabel = document.getElementById('progress-pct-' + kpiId);
            if (bar)      bar.style.width = pct + '%';
            if (pctLabel) pctLabel.textContent = pct + '%';

            updateOverallProgress();
        });
    });

    function updateOverallProgress() {
        let totalCurrent = 0, totalMax = 0;

        document.querySelectorAll('.kpi-score-field').forEach(function (input) {
            totalCurrent += parseFloat(input.value) || 0;
            totalMax     += parseFloat(input.dataset.target) || 0;
        });

        const pct = totalMax > 0 ? Math.round((totalCurrent / totalMax) * 100) : 0;

        const mainBar = document.getElementById('main-progress-bar');
        const mainPct = document.getElementById('main-progress-pct');
        const scoreDisplay   = document.getElementById('live-score-display');
        const actionBarScore = document.getElementById('action-bar-score');

        if (mainBar)       mainBar.style.width = pct + '%';
        if (mainPct)       mainPct.textContent  = pct + '%';
        if (scoreDisplay)  scoreDisplay.textContent  = Math.round(totalCurrent);
        if (actionBarScore) actionBarScore.textContent = Math.round(totalCurrent);
    }
</script>
@endsection
