@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/commission/checking.css') }}">
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="section">
    <!-- PAGE HEADER -->
    <div class="page-header mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('commission.band_scores.list', $kpi->type) }}">
                    <i class="fa fa-arrow-left mr-1"></i> Orqaga
                </a>
            </li>
            <li class="breadcrumb-item active">Davomat asosida baholash</li>
        </ol>
    </div>

    <!-- User + KPI Info -->
    <div class="user-info-section mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-title">
                        <i class="fa fa-user-circle"></i> Xodim ma'lumotlari
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ismi-sharifi</span>
                        <span class="info-value">{{ $user->first_name }} {{ $user->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Lavozimi</span>
                        <span class="info-value">{{ $user->lavozimi }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Bo'limi</span>
                        <span class="info-value">{{ $user->work_zone->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-title">
                        <i class="fa fa-calendar-alt"></i> Baholash ma'lumotlari
                    </div>
                    <div class="info-item">
                        <span class="info-label">KPI nomi</span>
                        <span class="info-value">{{ $kpi->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Baholanuvchi oy</span>
                        <span class="info-value">{{ $monthName }} {{ $year }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Joriy ball</span>
                        <span class="info-value">
                            @if($userKpi && $userKpi->current_score)
                                <span class="badge badge-success">{{ number_format($userKpi->current_score, 1) }}</span>
                            @else
                                <span class="badge badge-secondary">Baholanmagan</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Cards -->
    @php $base = $workingDays ?: $total; @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="att-card att-card--working">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-briefcase"></i></div>
                    <span class="att-card__pct">100%</span>
                </div>
                <div class="att-card__number">{{ $workingDays ?? '-' }}</div>
                <div class="att-card__label">Ish kunlari</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:100%"></div></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="att-card att-card--total">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-calendar"></i></div>
                    @php $totalPct = $base > 0 ? round($total / $base * 100) : 0; @endphp
                    <span class="att-card__pct">{{ $totalPct }}%</span>
                </div>
                <div class="att-card__number">{{ $total }}</div>
                <div class="att-card__label">Jami kunlar</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:{{ min(100, $totalPct) }}%"></div></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            @php $presentPct = $base > 0 ? round($present / $base * 100) : 0; @endphp
            <div class="att-card att-card--present">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-check-circle"></i></div>
                    <span class="att-card__pct">{{ $presentPct }}%</span>
                </div>
                <div class="att-card__number">{{ $present }}</div>
                <div class="att-card__label">Kelgan</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:{{ min(100, $presentPct) }}%"></div></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            @php $absentPct = $base > 0 ? round($absent / $base * 100) : 0; @endphp
            <div class="att-card att-card--absent">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-times-circle"></i></div>
                    <span class="att-card__pct">{{ $absentPct }}%</span>
                </div>
                <div class="att-card__number">{{ $absent }}</div>
                <div class="att-card__label">Kelmagan</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:{{ min(100, $absentPct) }}%"></div></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            @php $latePct = $base > 0 ? round($late / $base * 100) : 0; @endphp
            <div class="att-card att-card--late">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-clock"></i></div>
                    <span class="att-card__pct">{{ $latePct }}%</span>
                </div>
                <div class="att-card__number">{{ $late }}</div>
                <div class="att-card__label">Kech qoldi</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:{{ min(100, $latePct) }}%"></div></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            @php $earlyPct = $base > 0 ? round($early / $base * 100) : 0; @endphp
            <div class="att-card att-card--early">
                <div class="att-card__header">
                    <div class="att-card__icon"><i class="fa fa-sign-out-alt"></i></div>
                    <span class="att-card__pct">{{ $earlyPct }}%</span>
                </div>
                <div class="att-card__number">{{ $early }}</div>
                <div class="att-card__label">Erta ketdi</div>
                <div class="att-card__bar"><div class="att-card__bar-fill" style="width:{{ min(100, $earlyPct) }}%"></div></div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card mb-4" style="border:none;box-shadow:0 4px 16px rgba(0,0,0,0.1);border-radius:14px;overflow:hidden;">
        <div class="card-header d-flex align-items-center" style="background:linear-gradient(135deg,#3c4b64 0%,#4f6080 100%);border-bottom:2px solid rgba(255,255,255,0.1);padding:0.9rem 1.25rem;gap:0.75rem;">
            <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa fa-calendar-check" style="color:#fff;"></i>
            </div>
            <span style="color:#fff;font-weight:600;font-size:0.95rem;">{{ $monthName }} {{ $year }} — Davomat ro'yxati</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Sana</th>
                            <th>Kelish</th>
                            <th>Ketish</th>
                            <th>Holat</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $i => $att)
                            <tr class="text-center align-middle">
                                <td>{{ $i + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($att->date)->format('d.m.Y') }}</td>
                                <td>
                                    @if($att->first_in)
                                        <span class="{{ $att->is_late ? 'text-danger font-weight-bold' : '' }}">
                                            @if($att->is_late)<i class="fa fa-exclamation-circle mr-1"></i>@endif
                                            {{ date('H:i', strtotime($att->first_in)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($att->last_out)
                                        <span class="{{ $att->is_early ? 'text-danger font-weight-bold' : '' }}">
                                            @if($att->is_early)<i class="fa fa-exclamation-circle mr-1"></i>@endif
                                            {{ date('H:i', strtotime($att->last_out)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $att->status_class }}">{{ $att->display_status }}</span>
                                    @if($att->is_late && $att->display_status !== 'Sababli')
                                        <br><small class="text-danger">Kech qoldi</small>
                                    @endif
                                    @if($att->is_early && $att->display_status !== 'Sababli')
                                        <br><small class="text-danger">Erta ketdi</small>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $att->comment ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa fa-calendar-times fa-2x mb-2 d-block"></i>
                                    Bu oy uchun davomat ma'lumotlari topilmadi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scoring Form -->
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="evaluation-card fade-in">
                <div class="card-body">
                    <form action="{{ route('commission.attendance_score.store', ['kpi' => $kpi->id, 'user' => $user->id]) }}" method="POST">
                        @csrf

                        <!-- Quick score shortcuts based on attendance -->
                        @if($total > 0)
                        @php
                            $maxScore = $kpi->max_score ?? 100;

                            // Penalty tiers: 1-5 → 1 ball, 6-10 → 2 ball, 11+ → 3 ball
                            $penaltyTier = fn($count) => $count <= 0 ? 0 : ($count <= 5 ? 1 : ($count <= 10 ? 2 : 3));
                            $tierLabel   = fn($count) => $count <= 0 ? '-' : ($count <= 5 ? '1-5' : ($count <= 10 ? '6-10' : '11+'));

                            $absentPenalty = $penaltyTier($absent);
                            $latePenalty   = $penaltyTier($late);
                            $earlyPenalty  = $penaltyTier($early);
                            $totalPenalty  = $absentPenalty + $latePenalty + $earlyPenalty;
                            $finalScore    = max(0, $maxScore - $totalPenalty);
                        @endphp
                        <div class="penalty-section fade-in mb-4">
                            <div class="penalty-header">
                                <div class="penalty-header__icon"><i class="fa fa-calculator"></i></div>
                                <div class="penalty-header__text">
                                    <div class="penalty-header__title">Jazo ballari hisoblash</div>
                                    <small class="text-muted">1-5 kun → 1 ball, 6-10 kun → 2 ball, 11+ kun → 3 ball jazo</small>
                                </div>
                            </div>

                            <div class="penalty-table-wrap">
                                <table class="penalty-table">
                                    <thead>
                                        <tr>
                                            <th>Holat</th>
                                            <th class="text-center">Soni</th>
                                            <th class="text-center">Diapazon</th>
                                            <th class="text-center">Jazo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="penalty-dot" style="background:#ef4444;"></span> Kelmagan</td>
                                            <td class="text-center fw-semibold">{{ $absent }}</td>
                                            <td class="text-center">{{ $tierLabel($absent) }}</td>
                                            <td class="text-center fw-bold {{ $absentPenalty > 0 ? 'text-danger' : '' }}">-{{ $absentPenalty }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="penalty-dot" style="background:#8b5cf6;"></span> Kech qoldi</td>
                                            <td class="text-center fw-semibold">{{ $late }}</td>
                                            <td class="text-center">{{ $tierLabel($late) }}</td>
                                            <td class="text-center fw-bold {{ $latePenalty > 0 ? 'text-danger' : '' }}">-{{ $latePenalty }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="penalty-dot" style="background:#f97316;"></span> Erta ketdi</td>
                                            <td class="text-center fw-semibold">{{ $early }}</td>
                                            <td class="text-center">{{ $tierLabel($early) }}</td>
                                            <td class="text-center fw-bold {{ $earlyPenalty > 0 ? 'text-danger' : '' }}">-{{ $earlyPenalty }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="penalty-total-row">
                                            <td colspan="3">Jami jazo</td>
                                            <td class="text-center fw-bold text-danger">-{{ $totalPenalty }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="penalty-result">
                                <div class="penalty-result__row">
                                    <span class="penalty-result__label">Maksimal ball</span>
                                    <span class="penalty-result__value">{{ $maxScore }}</span>
                                </div>
                                <div class="penalty-result__row">
                                    <span class="penalty-result__label">Jami jazo</span>
                                    <span class="penalty-result__value text-danger">-{{ $totalPenalty }}</span>
                                </div>
                                <div class="penalty-result__divider"></div>
                                <div class="penalty-result__row penalty-result__final">
                                    <span class="penalty-result__label">Yakuniy ball</span>
                                    <span class="penalty-result__value" style="color:#10b981;font-size:1.3rem;">{{ $finalScore }}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap mt-3" style="gap:8px;">
                                <button type="button" onclick="setScore({{ $maxScore }})"
                                        class="btn btn-sm btn-outline-success" style="border-radius:20px;padding:4px 14px;">
                                    To'liq: {{ $maxScore }}
                                </button>
                                @if($totalPenalty > 0)
                                <button type="button" onclick="setScore({{ $finalScore }})"
                                        class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:4px 14px;">
                                    Jazo hisobiga: {{ $finalScore }}
                                </button>
                                @endif
                            </div>
                        </div>
                        @endif

                        @php
                            $suggestedScore = $finalScore ?? ($kpi->max_score ?? 100);
                        @endphp

                         <div class="mb-4">
                            <label class="form-label fw-bold mb-1">
                                <i class="fa fa-star text-warning mr-1"></i>
                                Ball kiriting
                                @if($kpi->max_score)
                                    <small class="text-muted">(maks: {{ $kpi->max_score }})</small>
                                @endif
                            </label>
                            <input type="number"
                                   name="score"
                                   class="form-control form-control-lg @error('score') is-invalid @enderror"
                                   value="{{ old('score', $userKpi?->current_score ?? $suggestedScore) }}"
                                   min="0"
                                   max="{{ $kpi->max_score ?? 100 }}"
                                   step="0.1"
                                   placeholder="Masalan: 7"
                                   required>
                            @error('score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="comments-section fade-in mb-4">
                            <div class="comments-header">
                                <div class="comments-icon"><i class="fa fa-comment"></i></div>
                                <div class="comments-title">Qo'shimcha izohlar</div>
                            </div>
                            <textarea class="form-control" name="feedback" rows="4"
                                      placeholder="Baholash bo'yicha izoh...">{{ old('feedback', $userKpi?->score?->feedback ?? '') }}</textarea>
                        </div>

                        <div class="submit-section">
                            <a href="{{ route('commission.band_scores.list', $kpi->type) }}"
                               class="btn btn-secondary mr-2">
                                <i class="fa fa-times mr-1"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-submit">
                                <span class="btn-text text-white">
                                    <i class="fa fa-save mr-2"></i>Saqlash
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setScore(val) {
        document.querySelector('input[name="score"]').value = val;
    }
</script>
@endsection
